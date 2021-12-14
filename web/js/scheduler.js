/**
 * The following content was designed & implemented under AlexSeif.com
 **/

let add_minutes = function (dt, minutes) {
    return new Date(dt.getTime() + minutes * 60000);
};
let pad = function (num, size = 2) {
    var s = "00" + num;
    return s.substr(s.length - size);
}
$(function () {
    let today = new Date();
    $('.today .task-item:not(.completed)').each(function () {
        let $est = ($(this).data('est'));
        $est = ($est) ? $est : 60;
        today = add_minutes(today, $est);
        $(this).attr('title', today.getHours() + ":" + today.getMinutes());
        console.log($(this).find('.task-label'));
        $(this).find('.task-label').append(pad(today.getHours()) + ":" + pad(today.getMinutes()))
    });
});