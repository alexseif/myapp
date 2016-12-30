var translate = [0, 0];
var colPos = 0;
var colWidth = '240px';
var colCount;
var colHeight;
function spreadNotes() {
    colCount = Math.floor($(window).width() / 256);
    if (colCount <= 1) {
        colCount = 1;
        colWidth = '95%';
        $('.notes-container').css('width', 'auto');
    } else {
        colWidth = '240px';
        $('.notes-container').css('width', 256 * colCount);
    }
    colHeight = Array.apply(null, Array(colCount)).map(function () {
        return 0;
    });
    $('.note').each(function () {
        pos = colPos % colCount;
        translate[0] = (pos) * 256;
        translate[1] = colHeight[pos];
        $(this).parent().css('width', colWidth);
        $(this).parent().css('transform', 'translate(' + translate[0] + 'px, ' + translate[1] + 'px)');
        colHeight[pos] += $(this).height() + 16;
        colPos++;
    });
}
$(document).ready(spreadNotes);
// Wait a bit before executing on resize
var doit;
$(window).resize(function () {
    clearTimeout(doit);
    doit = setTimeout(spreadNotes, 100);
});