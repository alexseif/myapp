function focusTitle() {
  $path = window.location.pathname;
  if ($path.indexOf('focus') !== -1) {
    $list = $('.task-list input[type="checkbox"]');
    $newTitle = true;
    $i = 0;
    while ($newTitle) {
      if (!$($list[$i]).is(':checked')) {
        $text = $($list[$i]).parent().text().trim();
        $newTitle = false;
      }
      $i++;
    }
    $('title').text($text + "| myApp");
  }
}

function drawFocusTasks() {
  var time = {
    day: 480,
    worked: 0,
    lowest: 480
  };
  var timeLeft = 480;
  var timeNeeded = 0;
  var tasksLis = $('.task-list li').not('.completed');
  var completed = $('.completed');
  var container = $('.container');
  var focus = $('<ul class="list-group task-list" id="focus"></ul>');
  focus.prependTo(container);

  var tasks = [];
  var tasksIndex = 0

  completed.each(function () {
    tasks[tasksIndex] = {
      id: $(this).data('id'),
      est: ($(this).data("time")) ? $(this).data("time") : 0,
      order: $(this).data("order-no"),
      completed: true
    };
    time.worked += tasks[tasksIndex].est;
    focus.append(this);
    tasksIndex++;
  });

  tasksLis.each(function () {
    tasks[tasksIndex] = {
      id: $(this).data('id'),
      est: ($(this).data("time")) ? $(this).data("time") : 0,
      order: $(this).data("order-no"),
      completed: $(this).hasClass('completed')
    };

    if (tasks[tasksIndex].est < time.lowest) {
      time.lowest = tasks[tasksIndex].est;
    }
    time.day -= tasks[tasksIndex].est;
    if (time.day > 0) {
      focus.append(this);
    }
    tasksIndex++;
  });

  console.log(tasks);
  console.log(time);


//  tasksLis.each(function () {
//  focus.children('li').not('.completed').each(function () {
//    $est = $(this).data('time');
//    if ($est) {
//      $(this).animate({height: ($est / timeNeeded) * remainingHeight + 'vh'});
//    }
//    $(this).children('.task').addClass('btn-primary').removeClass('btn-default');
//  });
}
$(document).ready(function () {
  $('[data-toggle="tooltip"]').tooltip();
  $('[data-toggle="popover"]').popover({html: true, trigger: 'focus'});
  //Checking Tasks
  $('.task-list input[type="checkbox"]').change(function () {
    taskId = $(this).data('taskid');
    $taskLi = $(this).parent().parent();
    if ($(this).is(":checked")) {
      $(this).parent().removeClass('btn-sm').addClass('btn-xs');
      $taskLi.addClass('completed').css('height', 'auto');
      completed = 1;
    } else {
      $taskLi.removeClass('completed');
      $(this).parent().removeClass('btn-xs').addClass('btn-sm');
      completed = 0;
    }
    $.ajax({
      type: "POST",
      url: tasks_path + taskId + "/edit",
      dataType: "json",
      data: {
        "completed": completed
      }
    })
            .done(focusTitle())
  });
  //Disabling on mobile devices
  // Maybe I should use screen width
  if (touch) {
//Sorting Tasks
    $(".task-list").sortable({
      connectWith: ".task-list",
      placeholder: "ui-state-highlight",
      items: "li:not(.completed)",
      update: function (event, ui) {
        var data = $(this).sortable("serialize", {"key": "tasks[][id]", attribute: "data-order"});
        $.ajax({
          data: data,
          dataType: "json",
          type: 'POST',
          url: tasks_order
        }).done(focusTitle());
      }
    });
  }

//Modal handling
  $('#newTask').on('shown.bs.modal', function () {
    $('#tasks_task').focus();
  });
});