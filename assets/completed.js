import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

$(document).ready(function () {
    let tasksTable = $('#tasks').DataTable({
        stateSave: true,
        responsive: true,
    });

    $('#tasks tbody').on('dblclick', 'tr', function () {
        $(this).find('td:first-child input[type="checkbox"]').click();
    });

    $('#select').click(function () {
        $('input[name="tasks[]"]').prop('checked', true);
    });

    $('#log').click(function () {
        submitTasks(this.dataset.url);
    });

    $('#unloggable').click(function () {
        submitTasks(this.dataset.url);
    });

    $('#delete').click(function () {
        submitTasks(this.dataset.url);
    });

    $('#reset').click(function () {
        tasksTable.state.clear();
        return true;
    });

    function submitTasks(url) {
        let data = $('input[name="tasks[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        $.post(url, {"task_ids": data}, function () {
            location.reload();
        });
    }
});