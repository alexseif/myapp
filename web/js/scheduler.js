/**
 * The following content was designed & implemented under AlexSeif.com
 **/

let add_minutes = function (dt, minutes) {
    return new Date(dt.getTime() + minutes * 60000);
};

$(function () {
    let today = new Date();
    $('.today .task-item:not(.completed)').each(function () {
        $est = ($(this).data('est'));
        $est = ($est) ? $est : 60;
        today = add_minutes(today, $est);
        $(this).attr('title', today.getHours() + ":" + today.getMinutes());
    });
});