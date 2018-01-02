// Tasks Object
var Tasks = {
  isFocus: (window.location.pathname.indexOf('focus') !== -1) ? true : false,
  day: {
    completed: 0,
    time: 300,
    remaining: 0
  },

  init: function () {
    this.bindEvents();
  },

  bindEvents: function () {
    $('.task-list input[type="checkbox"]').change(this.updateTask);
    $('.task-list a.postpone').click(this.postponeTask);
    $('[data-toggle="popover"]').popover({html: true, container: "body"});
    if (!touch) {
      $("#tasks").sortable({
        connectWith: ".task-list",
        items: "li:not(.completed)",
        update: this.updateOrder
      });
    }
    if (this.isFocus) {
      $('<ul class="list-group task-list" id="focus"></ul>').prependTo('.container');
      this.drawFocus();
      if (!touch) {
        $("#focus").sortable({
          connectWith: ".task-list",
          items: "li:not(.completed)",
          update: this.updateOrder
        });
      }
    }
  },

  postponeTask: function () {
    var taskEl = this;
    //TODO: handle errors
    var postpone = true;
    var taskId = $(taskEl).data('taskid');
    $.ajax({
      type: "POST",
      url: tasks_path + $(taskEl).data('taskid') + "/edit",
      dataType: "json",
      data: {
        "id": $(taskEl).data('taskid'),
        "postpone": postpone
      }
    }).done(function () {

      var undoLink = $('<a class="undo"><strong>Undo</strong></a>')
              .data('taskid', taskId);

      var div = $('<div/>')
              .addClass("alert alert-success alert-dismissable")
              .css("margin-bottom", 0)
              .append('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Postponed!</strong>&nbsp;')
              .append(undoLink);

      var li = $('<li/>')
              .addClass("list-group-item")
              .append(div);

      $(taskEl).parent().parent().after(li);

      $('a.undo').click(Tasks.undoPostponeTask);

      $(taskEl).parent().parent().remove();

      if (self.isFocus) {
        self.drawFocus();
      }
    });
  },

  undoPostponeTask: function () {
    var taskEl = this;
    //TODO: handle errors
    $.ajax({
      type: "POST",
      url: tasks_path + $(taskEl).data('taskid') + "/edit",
      dataType: "json",
      data: {
        "id": $(taskEl).data('taskid'),
        "undo": true
      }
    }).done(function () {
      location.reload();
      if (self.isFocus) {
        self.drawFocus();
      }
    });
  },

  updateTask: function () {
    var taskEl = this;
    var isCompleted = $(taskEl).is(':checked') ? 1 : 0;
    //TODO: handle errors
    $.ajax({
      type: "POST",
      url: tasks_path + $(taskEl).data('taskid') + "/edit",
      dataType: "json",
      data: {
        "id": $(taskEl).data('taskid'),
        "completed": isCompleted
      }
    }).done(function () {
      if (isCompleted) {
        $(taskEl).parent().parent()
                .addClass('completed')
                .children('.btn-sm:not(.info-link)')
                .removeClass('btn-sm').addClass('btn-xs');
      } else {
        $(taskEl).parent().parent()
                .removeClass('completed')
                .children('.btn-xs:not(.info-link)')
                .removeClass('btn-xs').addClass('btn-sm');
      }
      if (self.isFocus) {
        self.drawFocus();
      }
    });
  },

  drawFocus: function () {
    self = this;
    completed = $('.completed');
    $('#completed').append(completed);
    $('li:not(.completed)').prependTo('#tasks');

    this.day.completed = 0;
    completed.each(function () {
      self.day.completed += ($(this).data("time")) ? $(this).data("time") : 0;
    });

    this.day.remaining = this.day.time - this.day.completed;
    while (this.day.remaining > 0) {
      task = $('#tasks li:first');
      if (task.length) {
        $taskTime = (task.data("time")) ? task.data("time") : 0;
        $remainingTime = this.day.remaining - $taskTime;
        if (($remainingTime >= 0)) {
          this.day.remaining = $remainingTime;
          $('#focus').append(task);
        } else {
          //Break Task
          $taskTime = $remainingTime + $taskTime;
          var taskDuplicate = task.clone();
          taskDuplicate.data("time", $taskTime);
          taskDuplicate.children('small.text-info').text($taskTime + "m");
          taskDuplicate.data("id", null);
          task.children('small.text-info').text(task.data('time') + "-" + $taskTime + "m");
          this.day.remaining -= (taskDuplicate.data("time"));
          //TODO: disable task check or handle task completion
          $('#focus').append(taskDuplicate);
        }
      } else {
        break;
      }
    }
    this.setFocusTaskHeight();
    this.focusTitle();
  },

  focusTitle: function () {
    $('title').text($('#focus li:first label').text().trim() + "| myApp");
  },

//Update Tasks size to fit screen
  setFocusTaskHeight: function () {
    $('#focus li').css('height', ((1 - ($('#completed').height() / $(window).height())) * 100 / $('#focus li').length + 'vh'));
    $('#completed li').css('height', 'auto');
    $('#tasks li').css('height', 'auto');
  },

//Update order of tasks based on sorting
  updateOrder: function () {
    var dataString = "";
    $('.task-list li:not(.completed)').each(function () {
      dataString += "tasks[][id]=" + $(this).data('id') + "&";
    });
    $.ajax({
      data: dataString,
      dataType: "json",
      type: 'POST',
      url: tasks_order
    }).done(function () {
      if (self.isFocus) {
        self.drawFocus();
      }
    });
  }
}
Tasks.init();