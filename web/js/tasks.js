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
var time = {
  day: 480,
  completed: 0,
  lowest: 480,
  remaining: 480
};
var container = $('.container');
var focus = $('<ul class="list-group task-list" id="focus"></ul>');
focus.prependTo(container);
var completed = $('.completed');
function drawFocusTasks() {
  completed = $('.completed');
  focus.children('li').prependTo('#tasks');
  time.completed = 0;
  completed.each(function () {
    time.completed += ($(this).data("time")) ? $(this).data("time") : 0;
  });
  time.remaining = time.day - time.completed;
  while (time.remaining > 0) {
    task = $('#tasks li:first');
    time.remaining -= (task.data("time")) ? task.data("time") : 0;
    focus.append(task);
  }
  focus.append(completed);
  focusTitle();
}

function updateOrder() {
  var data = {};
  var order = 0;
  var dataString = "";
//  tasks[][id]=544&tasks[][id]=711&tasks[][id]=709&tasks[][id]=717&tasks[][id]=766&tasks[][id]=716&tasks[][id]=703&tasks[][id]=754&tasks[][id]=753&tasks[][id]=710&tasks[][id]=752&tasks[][id]=760
  $('.task-list li').each(function () {
    dataString += "tasks[][id]=" + $(this).data('id') + "&";
  });
  $.ajax({
    data: dataString,
    dataType: "json",
    type: 'POST',
    url: tasks_order
  }).done(drawFocusTasks());
}

function focusTaskChange() {
  if ($(this).is(":checked")) {
    $(this).parent().removeClass('btn-sm').addClass('btn-xs');
    $(this).parent().parent().addClass('completed').css('height', 'auto');
    $ajaxValue = 1;
  } else {
    $(this).parent().parent().removeClass('completed');
    $(this).parent().removeClass('btn-xs').addClass('btn-sm');
    $ajaxValue = 0;
  }
  $.ajax({
    type: "POST",
    url: tasks_path + $(this).data('taskid') + "/edit",
    dataType: "json",
    data: {
      "completed": $ajaxValue
    }
  }).done(drawFocusTasks());
}
function focusInit() {
  $('[data-toggle="tooltip"]').tooltip();
  $('[data-toggle="popover"]').popover({html: true, trigger: 'focus'});
//Checking Tasks
  $('.task-list input[type="checkbox"]').change(focusTaskChange);
  //Disabling on mobile devices
  // Maybe I should use screen width
  if (!touch) {
    $("#focus, #tasks").sortable({
      connectWith: ".task-list",
//      placeholder: "ui-state-highlight",
      items: "li:not(.completed)",
      update: updateOrder
//      update: function (event, ui) {
//        var data = $(this).sortable("serialize", {"key": "tasks[][id]", attribute: "data-order"});
//        $.ajax({
//          data: data,
//          dataType: "json",
//          type: 'POST',
//          url: tasks_order
//        }).done(drawFocusTasks());
//      }
    });
  }
}
