$(document).ready(function () {
    $('.task-list input[type="checkbox"]').change(function () {
        taskId = $(this).data('taskid');
        if ($(this).is(":checked")) {
            $(this).parent().addClass('strike');
            completed = 1;
        } else {
            $(this).parent().removeClass('strike');
            completed = 0;
        }
        $.ajax({
            type: "POST",
            url: tasks_path + taskId + "/edit",
            dataType: "json",
            data: {
                "completed": completed
            },
            success: function (response) {
                console.log(response);
            }
        });
    });
});