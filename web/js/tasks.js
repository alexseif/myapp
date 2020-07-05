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
    if (this.isFocus) {
      $('<div class="accordion task-list m-4" id="focus"></div>').prependTo('.container');
      this.drawFocus();
      if (!touch) {
        $("#focus").sortable({
          connectWith: ".task-list",
          items: ".task-item:not(.completed)",
          update: this.updateOrder
        });
      }
    }
  },
  bindEvents: function () {
    $('.task-list input[type="checkbox"]').change(this.updateTask);
    $('.task-list a.postpone').click(this.postponeTask);
    $('[data-toggle="popover"]').popover({html: true, container: "body"});
    $('[data-toggle="modal"]').click(this.showModal);

    if (!touch) {
      $("#tasks").sortable({
        connectWith: ".task-list",
        items: ".task-item:not(.completed)",
        update: this.updateOrder
      });
    }
  },
  postponeTask: function () {
    var taskEl = this;
    //TODO: handle errors
    var taskId = $(taskEl).data('taskid');
    var postpone = $(taskEl).data('postpone');
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
      var li = $('<div/>')
              .append(div);
      console.log($(taskEl).parents('.task-item'));
      $(taskEl).parents('.task-item').after(li);
      $('a.undo').click(Tasks.undoPostponeTask);
      $(taskEl).parents('.task-item').remove();
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
        $(taskEl).parents('.task-item')
                .addClass('completed')
                .children('.btn-sm:not(.info-link)')
                .removeClass('btn-sm').addClass('btn-xs');
      } else {
        $(taskEl).parents('.task-item')
                .removeClass('completed')
                .children('.btn-xs:not(.info-link)')
                .removeClass('btn-xs').addClass('btn-sm');
      }
      if (self.isFocus) {
        self.drawFocus();
      }
    });
    getBottomBarDetails();
  },
  drawFocus: function () {
    self = this;
    completed = $('.completed.task-item');
    $('#completed').append(completed);
    $('.task-item:not(.completed)').prependTo('#tasks');
    if (todayHours >= 0) {
      this.day.time = todayHours * 60;
    }
    this.day.completed = 0;
    completed.each(function () {
      self.day.completed += ($(this).data("duration")) ? $(this).data("duration") : 0;
      if ($('body').hasClass('has-contract')) {
        if ($(this).data('client') != $('.client-details').data('client')) {
          self.day.completed -= ($(this).data("duration")) ? $(this).data("duration") : 0;
        }
      }
    });
    this.day.remaining = this.day.time - this.day.completed;
    while (this.day.remaining > 0) {
      task = $('#tasks .task-item:first-child');
      if (task.length) {
        $taskTime = (task.data("est")) ? task.data("est") : 60;
        $remainingTime = this.day.remaining - $taskTime;
        if (($remainingTime >= 0)) {
          this.day.remaining = $remainingTime;
          $('#focus').append(task);
        } else {
          //Break Task
          $taskTime = $remainingTime + $taskTime;
          var taskDuplicate = task.clone();
          taskDuplicate.data("est", $taskTime);
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
    $('title').text($('#focus .task-item:first-child label').text().trim() + "| myApp");
  },
//Update Tasks size to fit screen
  setFocusTaskHeight: function () {
//    $('#focus .task-item .card-header').css('min-height', ((1 - ($('#completed').height() / $(window).height())) * 80 / $('#focus .task-item').length + 'vh'));
//    $('#completed .task-item .card-header').css('min-height', 'auto');
//    $('#tasks .task-item .card-header').css('min-height', 'auto');
  },
//Update order of tasks based on sorting
  updateOrder: function () {
    var dataString = "";
    $('.task-list .task-item:not(.completed)').each(function () {
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
  },

//Open task modal
  showModal: function (e) {
// Support for AJAX loaded modal window.
// Focuses on first input textbox after it loads the window.
    e.preventDefault();
    var url = $(this).attr('href');
    if (url.indexOf('#') == 0) {
      $(url).modal('open');
    } else {
      $.get(url, function (data) {
        $('<div class="modal" tabindex="-1" role="dialog">' + data + '</div>').modal().on('hidden', function () {
          $(this).remove();
        });
      }).done(function () {
        $('input:text:visible:first').focus();
      });
    }
  }
}
Tasks.init();