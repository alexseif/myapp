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
$(document).ready(function () {
  $('[data-toggle="tooltip"]').tooltip();
  $('[data-toggle="popover"]').popover({html: true, trigger: 'focus'});
  //Checking Tasks
  $('.task-list input[type="checkbox"]').change(function () {
    taskId = $(this).data('taskid');
    if ($(this).is(":checked")) {
      $(this).parent().parent().addClass('completed');
      completed = 1;
    } else {
      $(this).parent().parent().removeClass('completed');
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

  //Sorting Tasks
  $(".task-list").sortable({
    placeholder: "ui-state-highlight",
    items: "li:not(.completed)",
    update: function (event, ui) {
      var data = $(this).sortable("serialize", {"key": "tasks[][id]", attribute: "data-order"});
      console.log(data);
      $.ajax({
        data: data,
        dataType: "json",
        type: 'POST',
        url: tasks_order
      }).done(focusTitle());
    }
  });

  //Modal handling
  $('#newTask').on('shown.bs.modal', function () {
    $('#tasks_task').focus();
  });
});