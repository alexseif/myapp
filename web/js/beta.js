/* 
 * The following content was designed & implemented under AlexSeif.com
 */

var BetaItems = {
  init: function () {
    $('.action-list .action-item').click(this.expandItem);
    $('.action-prev').click(this.prevItem);
    $('.action-next').click(this.nextItem);
    $('.action-start').click(this.startItem);
    $('.action-hide').click(this.hideItem);
    window.addEventListener("keydown", this.keyboardNavigation, true);
    $(window.location.hash).click();
    if (!touch) {
      $('.action-list').sortable({
        update: this.updateOrder
      });
    }
  },
  updateOrder: function () {
    var dataString = "";
    $('.action-list .action-item').each(function () {
      dataString += "tasks[][id]=" + $(this).data('id') + "&";
    });
    $.ajax({
      data: dataString,
      dataType: "json",
      type: 'POST',
      url: tasks_order
    }).done(function () {
      if (self.isFocus) {
        self.drawFocus();
      }
    });
  },
  expandItem: function () {
    var lastActiveItemLi = $('.action-list .action-item.active');
    var activeItemLi = $(this);
    if (!activeItemLi.hasClass('active')) {
      lastActiveItemLi.children('.btns').hide();
      lastActiveItemLi.removeClass('active');
      activeItemLi.children('.btns').show();
      activeItemLi.addClass('active');
    }
    window.location.hash = $(this).attr('id');
  },
  prevItem: function (event) {
    event.stopPropagation();
    if ($('.action-item.active').prev().length) {
      $('.action-item.active').prev()[0].click();
    }
  },
  nextItem: function (event) {
    event.stopPropagation();
    if ($('.action-item.active').next().length) {
      $('.action-item.active').next()[0].click();
    }
  },
  startItem: function (event) {
    event.stopPropagation();
    //TODO: start timer and log time
  },
  hideItem: function (event) {
    event.stopPropagation();
    $thisID = $('.action-item.active').attr('id');
    $nextID = $('.action-item.active').next().attr('id');
    $('#' + $thisID).remove();
    $('#' + $nextID).click();
    //TODO: store hidden items to keep hidden for 5 mins
  },
  keyboardNavigation(event) {
    if (event.defaultPrevented) {
      return; // Do nothing if the event was already processed
    }
    switch (event.key) {
      case "ArrowRight":
      case "ArrowDown":
        BetaItems.nextItem(event);
        break;
      case "ArrowLeft":
      case "ArrowUp":
        BetaItems.prevItem(event);
        break;
      case "Enter":
        // Do something for "enter" or "return" key press.
        break;
      case "Escape":
        // Do something for "esc" key press.
        break;
      default:
        return; // Quit when this doesn't handle the key event.
    }

    // Cancel the default action to avoid it being handled twice
    event.preventDefault();
  }
}

$(document).ready(function () {
  BetaItems.init();
});