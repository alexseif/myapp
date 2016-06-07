$(document).ready(function () {
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
        });
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
            });
        }
    });

    //Modal handling
    $('#newTask').on('shown.bs.modal', function () {
        $('#tasks_task').focus();
    })
});