/* 
 * The following content was designed & implemented under AlexSeif.com
 */


var adjustment;

$("ol.simple_with_animation").sortable({
  group: 'simple_with_animation',
  pullPlaceholder: false,
  // animation on drop
  onDrop: function ($item, container, _super) {
    var $clonedItem = $('<li/>').css({height: 0});
    $item.before($clonedItem);
    $clonedItem.animate({'height': $item.height()});

    $item.animate($clonedItem.position(), function () {
      $clonedItem.detach();
      _super($item, container);
    });
  },

  // set $item relative to cursor position
  onDragStart: function ($item, container, _super) {
    var offset = $item.offset(),
            pointer = container.rootGroup.pointer;

    adjustment = {
      left: pointer.left - offset.left,
      top: pointer.top - offset.top
    };

    _super($item, container);
  },
  onDrag: function ($item, position) {
    $item.css({
      left: position.left - adjustment.left,
      top: position.top - adjustment.top
    });
  }
});

function draw() {
  $position = $('.mangement-panel').position();
  $('.task-panel').height('calc(100vh - ' + $position.top + 'px)');
  $height = $('.task-panel').height();
  $width = $('.task-panel').width();
  $bodyWidth = $('body').width();
  $calcWidth = ($bodyWidth - $width) / 2;
  $('.table-panel').height($height + 'px)');
  $('.table-panel').width(($bodyWidth - $width) + 'px)');
  $('.table-panel table tbody td ol').height(($height / 2) + 'px');
  $('.table-panel table tbody td ol').width($calcWidth + 'px');
}

function start() {
  $("li[data-priority='0'][data-urgency='0']").appendTo('#normal');
  $("li[data-priority='0'][data-urgency='1']").appendTo('#urgent');
  $("li[data-priority='1'][data-urgency='0']").appendTo('#important');
  $("li[data-priority='1'][data-urgency='1']").appendTo('#importantAndUrgent')
}
draw();
start();

$('ol li').dblclick(function () {
  var win = window.open($(this).data('task-url'), '_blank');
});
