var time = {
  day: 480,
  completed: 0,
  lowest: 480,
  remaining: 480
};
var container = $('.container');
var focus = $('<ul class="list-group task-list" id="focus"></ul>');
var completed = $('.completed');
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
function drawTaskSize() {
  $('#focus li').css('height', ((1 - ($('#completed').height() / $(window).height())) * 100 / $('#focus li').length + 'vh'));
  $('#completed li').css('height', 'auto');
}
function drawFocusTasks() {
  completed = $('.completed');
  $('#completed').append(completed);
  $('li:not(.completed)').prependTo('#tasks');

  time.completed = 0;
  completed.each(function () {
    time.completed += ($(this).data("time")) ? $(this).data("time") : 0;
  });

  time.remaining = time.day - time.completed;
  while (time.remaining > 0) {
    task = $('#tasks li:first');
    if (task.length) {
      time.remaining -= (task.data("time")) ? task.data("time") : 0;
      focus.append(task);
    } else {
      break;
    }
  }
  focusTitle();
  drawTaskSize();
}
function updateOrder() {
  var dataString = "";
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
  $li = $(this).parent().parent();
  $ul = $li.parent();
  $ajaxValue = ($(this).is(":checked")) ? 1 : 0;
  $.ajax({
    type: "POST",
    url: tasks_path + $(this).data('taskid') + "/edit",
    dataType: "json",
    data: {
      "completed": $ajaxValue
    }
  }).done(function () {
    if ($ajaxValue) {
      $($li).addClass('completed');
    } else {
      $($li).removeClass('completed');
    }
    drawFocusTasks();
  });
}
function focusInit() {
  focus.prependTo(container);
  // $('[data-toggle="tooltip"]').tooltip();
  $('[data-toggle="popover"]').popover({html: true});
  // Checking Tasks
  $('.task-list input[type="checkbox"]').change(focusTaskChange);
  // Disabling on mobile devices
  // Maybe I should use screen width
  if (!touch) {
    $("#focus, #tasks").sortable({
      connectWith: ".task-list",
      items: "li:not(.completed)",
      update: updateOrder
    });
  }
  drawFocusTasks();
}
