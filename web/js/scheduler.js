/**
 * The following content was designed & implemented under AlexSeif.com
 **/

let add_minutes = function (dt, minutes) {
    return new Date(dt.getTime() + minutes * 60000);
};
let pad = function (num, size = 2) {
    let s = "00" + num;
    return s.substr(s.length - size);
}

let Scheduler = {
    today: new Date(),
    schedule: [],
    init: function () {
        const self = this;
        Scheduler.bindEvents();
        Scheduler.displayTime();
    },
    bindEvents: function () {
        const self = this;
        $('#save_schedule').click(Scheduler.saveSchedule);
        $("#garbage").droppable({
            hoverClass: "ui-state-active",
            drop: Scheduler.removeFromSchedule
        });
    },
    displayTime: function () {
        const self = this;
        $('.today .task-item:not(.completed)').each(function () {
            that = $(this);
            let $est = (that.data('est'));
            $est = ($est) ? $est : 60;
            Scheduler.today = add_minutes(Scheduler.today, $est);
            that.attr('title', Scheduler.today.getHours() + ":" + Scheduler.today.getMinutes());
            that.find('.task-label').append(pad(Scheduler.today.getHours()) + ":" + pad(Scheduler.today.getMinutes()))
        });
    },
    saveSchedule: function () {
        const self = this;
        Scheduler.schedule = {};
        $('.task-list').each(function () {
            thisTaskList = $(this);
            dayId = thisTaskList.attr('id');
            $dayList = [];
            thisTaskList.children('.task-item:not(.completed)').each(function () {
                thisTask = $(this);
                $dayList.push({
                    "id": thisTask.data('schedule'),
                    "task": thisTask.data('id'),
                    "est": thisTask.data('est'),
                    "eta": dayId
                });
            });
            Scheduler.schedule[dayId] = $dayList;
        });
        let data = {"data": Scheduler.schedule};
        $.post(scheduler_save_route, data);
    },
    removeFromSchedule: function (event, ui) {
        $task = $(ui.draggable);
        $scheduleId = $task.data('schedule');
        if ($scheduleId) {
            $.post(scheduler_delete_route, {"schedule_id": $scheduleId});
        }
        $task.remove();
        $(this)
            .addClass("ui-state-highlight")
            .find("p")
            .html("Removed!");
    }
};
$(function () {
    Scheduler.init();
});