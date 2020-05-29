/* 
 * The following content was designed & implemented under AlexSeif.com
 */

var inbox = {
  init: function () {
    $('#inbox .nav-link').click(this.loadTasks);
    $('#inbox .nav-link').first().click();
  },
  loadTasks: function () {
    var inboxTaskList = this;
    var dataUrl = $(this).data('url');
    var dataTarget = $(this).data('target');
    $('#inbox .nav-item').removeClass('active-trail');
    $(this).parent('.nav-item').addClass('active-trail');
    //@TODO: Error handling 
    var jqxhr = $.ajax({
      url: dataUrl
    })
            .done(function (html) {
              $(dataTarget).html(html);
              Tasks.bindEvents();
            })
            .fail(function () {
              alert("error");
            })
            .always(function () {
//              alert("complete");
            });
    $('title').text($(this).text().trim() + " | myApp");
  },
}
inbox.init();