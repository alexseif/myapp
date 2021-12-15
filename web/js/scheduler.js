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
        Scheduler.schedule = [];
        $('.task-item:not(.completed)').each(function () {
            that = $(this);
            Scheduler.schedule.push({
                "id": that.data('schedule'),
                "task": that.data('id'),
                "est": that.data('est'),
                "eta": that.parent().attr("id")
            });
            console.log(Scheduler.schedule);
        });
        $.ajax({
            url: scheduler_save_route,
            type: "POST",
            dataType: "json",
            data: {"data": Scheduler.schedule},
            async: true,
            success: function (data) {
                console.log(data)
                $('div#ajax-results').html(data.output);
            }
        });
    }
};
$(function () {
    Scheduler.init();
});