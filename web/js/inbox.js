/* 
 * The following content was designed & implemented under AlexSeif.com
 */
// @todo: move to tasks.js
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
                Tasks.init();
                // Tasks.bindEvents();
                $(".task-list").sortable({
                    connectWith: ".task-list",
                    items: ".task-item:not(.completed)",
                    update: function () {
                        var dataString = "";
                        $('.task-list .task-item:not(.completed)').each(function () {
                            dataString += "tasks[][id]=" + $(this).data('id') + "&";
                        });
                        $.ajax({
                            data: dataString,
                            dataType: "json",
                            type: 'POST',
                            url: tasks_order
                        }).done(function () {
                        });
                    }
                });
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