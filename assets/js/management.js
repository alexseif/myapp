/* 
 * The following content was designed & implemented under AlexSeif.com
 */
$(document).ready(function () {
  $('.postpone').click(function () {
    var taskDelay = this;
    //TODO: handle errors
    var taskId = $(taskDelay).data('taskid');
    var postpone = $(taskDelay).data('postpone');
    $.ajax({
      type: "POST",
      url: tasks_path + $(taskDelay).data('taskid') + "/edit",
      dataType: "json",
      data: {
        "id": $(taskDelay).data('taskid'),
        "postpone": postpone
      }
    }).done(function () {

      $('li[data-id="' + taskId + '"]').fadeOut('slow', function () {
        $(this).remove()
      });
      $.notify({
        // options
        icon: 'glyphicon glyphicon-time',
        message: 'Postponed'
      }, {type: "success", });

    });
  });

});