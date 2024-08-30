/**
 * The following content was designed & implemented under AlexSeif.com
 **/
/*jshint strict: false */
/*jshint esversion: 6 */
/*globals $:false */
/*globals scheduler_save_route:false */
/*globals scheduler_delete_route:false */
let add_minutes = function (dt, minutes) {
    return new Date(dt.getTime() + minutes * 60000);
};
let pad = function (num, size = 2) {
    let s = "00" + num;
    return s.substr(s.length - size);
};

let Scheduler = {
    today: new Date(),
    schedule: [],
    init: function () {
        Scheduler.bindEvents();
        Scheduler.displayTime();
    },
    bindEvents: function () {
        $('#save_schedule').click(Scheduler.saveSchedule);
        $("#garbage").droppable({
            hoverClass: "ui-state-active",
            drop: Scheduler.removeFromSchedule
        });
    },
    displayTime: function () {
        $('.today .task-item:not(.completed)').each(function () {
            let _self;
            _self = $(this);
            let $est = (_self.data('est'));
            $est = ($est) ? $est : 60;
            Scheduler.today = add_minutes(Scheduler.today, $est);
            _self.attr('title', Scheduler.today.getHours() + ":" + Scheduler.today.getMinutes());
            _self.find('.task-label').append(pad(Scheduler.today.getHours()) + ":" + pad(Scheduler.today.getMinutes()));
        });
    },
    saveSchedule: function () {
        Scheduler.schedule = {};
        $('.task-list').each(function () {
            let thisTaskList = $(this);
            let dayId = thisTaskList.attr('id');
            let $dayList = [];
            thisTaskList.children('.task-item:not(.completed)').each(function () {
                let thisTask = $(this);
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
        let $task = $(ui.draggable);
        let $scheduleId = $task.data('schedule');
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

    $('#form_concatenate').change(function () {
        $('form').submit();
    });
});