// Tasks Object
let Tasks = {
    isFocus: (window.location.pathname.indexOf('focus') !== -1), day: {
        completed: 0, time: 300, remaining: 0
    }, init: function () {
        this.bindEvents();
        if (this.isFocus) {
            $('<div class="task-list m-4" id="focus"></div>').prependTo('.container');
            this.drawFocus();
            if (!window.touch) {
                $(".task-list").sortable({
                    connectWith: ".task-list", items: ".task-item:not(.completed)", update: this.updateOrder
                });
            }
        }
    }, bindEvents: function () {
        $('.task-item input[type="checkbox"]').change(this.updateTask);
        $('.task-item .postpone').click(Tasks.postponeTask);
        $('[data-toggle="popover"]').popover({html: true, container: "body"});
        $('[data-toggle="modal"]').click(this.showModal);

        if (!window.touch) {
            $(".task-list").sortable({
                connectWith: ".task-list", items: ".task-item:not(.completed)", update: this.updateOrder
            });
        }
    }, postponeTask: function () {
        const self = this;
        //TODO: handle errors
        const taskId = $(self).data('taskid');
        const postpone = $(self).data('postpone');
        $.ajax({
            type: "POST", url: tasks_path + $(self).data('taskid') + "/edit", dataType: "json", data: {
                "id": $(self).data('taskid'), "postpone": postpone
            }
        }).done(function () {

            if (self.isFocus) {
                const undoLink = $('<a class="undo"><strong>Undo</strong></a>')
                    .data('taskid', taskId);
                const div = $('<div/>')
                    .addClass("alert alert-success alert-dismissable")
                    .css("margin-bottom", 0)
                    .append('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Postponed!</strong>&nbsp;')
                    .append(undoLink);
                const li = $('<div/>')
                    .append(div);
                console.log($(self).parents('.task-item'));
                $(self).parents('.task-item').after(li);
                $('a.undo').click(Tasks.undoPostponeTask);
                $(self).parents('.task-item').remove();
                self.drawFocus();
            }
        });
    }, undoPostponeTask: function () {
        const taskEl = this;
        //TODO: handle errors
        $.ajax({
            type: "POST", url: tasks_path + $(taskEl).data('taskid') + "/edit", dataType: "json", data: {
                "id": $(taskEl).data('taskid'), "undo": true
            }
        }).done(function () {
            location.reload();
            if (self.isFocus) {
                self.drawFocus();
            }
        });
    }, updateTask: function (event) {
        const taskEl = this;
        const isCompleted = $(taskEl).is(':checked') ? 1 : 0;
        let duration;
        duration = $(taskEl).data('duration');
        if (isCompleted) {
            duration = prompt("Duration", duration);
            if (1 > duration) {
                alert('khara');
                taskEl.checked = !isCompleted;
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        }
        //TODO: handle errors
        $.ajax({
            type: "POST", url: tasks_path + $(taskEl).data('taskid') + "/edit", dataType: "json", data: {
                "id": $(taskEl).data('taskid'), "completed": isCompleted, "duration": duration
            }
        }).done(function () {
            if (isCompleted) {
                $(taskEl).parents('.task-item')
                    .addClass('completed');
                $.get(workarea_get_tasks_completedToday_count_path, function (data) {
                    $('#completedToday-pill').text(data);
                });
            } else {
                $(taskEl).parents('.task-item')
                    .removeClass('completed');
            }
            if (self.isFocus) {
                self.drawFocus();
            }
        });
        getBottomBarDetails();
    },
    drawFocus: function () {
        self = this;
        let completed = $('.completed.task-item');
        $('#completed').append(completed);
        $('.task-item:not(.completed)').prependTo('#tasks');
        if (todayHours >= 0) {
            this.day.time = todayHours * 60;
        }
        this.day.completed = 0;
        completed.each(function () {
            self.day.completed += ($(this).data("duration")) ? $(this).data("duration") : 0;
            if ($('body').hasClass('has-contract')) {
                if ($(this).data('client') !== $('.client-details').data('client')) {
                    self.day.completed -= ($(this).data("duration")) ? $(this).data("duration") : 0;
                }
            }
        });
        this.day.remaining = this.day.time - this.day.completed;
        while (this.day.remaining > 0) {
            let task = $('#tasks .task-item:first-child');
            if (task.length) {
                let $taskTime = (task.data("est")) ? task.data("est") : 60;
                let $remainingTime = this.day.remaining - $taskTime;
                if (($remainingTime >= 0)) {
                    this.day.remaining = $remainingTime;
                    $('#focus').append(task);
                } else {
                    //Break Task
                    $taskTime = $remainingTime + $taskTime;
                    const taskDuplicate = task.clone();
                    taskDuplicate.data("est", $taskTime);
                    taskDuplicate.children('small.text-info').text($taskTime + "m");
                    taskDuplicate.data("id", null);
                    task.children('small.text-info').text(task.data('time') + "-" + $taskTime + "m");
                    this.day.remaining -= (taskDuplicate.data("time"));
                    //TODO: disable task check or handle task completion
                    $('#focus').append(taskDuplicate);
                }
            } else {
                break;
            }
        }
        this.focusTitle();
    },
    focusTitle: function () {
        $('title').text($('#focus .task-item:first-child label').text().trim() + "| myApp");
    }, //Update order of tasks based on sorting
    updateOrder: function () {
        let dataString = "";
        $('.task-list .task-item:not(.completed)').each(function () {
            dataString += "tasks[][id]=" + $(this).data('id') + "&";
        });
        $.ajax({
            data: dataString, dataType: "json", type: 'POST', url: tasks_order
        }).done(function () {
            if (self.isFocus) {
                self.drawFocus();
            }
        });
    },

//Open task modal
    showModal: function (e) {
// Support for AJAX loaded modal window.
// Focuses on first input textbox after it loads the window.
        e.preventDefault();
        const url = $(this).attr('href');
        if (url.indexOf('#') === 0) {
            $(url).modal('open');
        } else {
            $.get(url, function (data) {
                $('<div class="modal" tabindex="-1" role="dialog">' + data + '</div>').modal().on('hidden', function () {
                    $(this).remove();
                });
            }).done(function () {
                $('input:text:visible:first').focus();
            });
        }
    }, /**
     * Inbox Tasks
     */
    bindInbox: function () {
        //Inbox Tasks
        $('#inbox .nav-link').click(this.loadTasks);
    }, loadTasks: function () {
        self = this;
        const dataUrl = $(this).data('url');
        const dataTarget = $(this).data('target');
        $('#inbox .nav-item').removeClass('active-trail');
        $(this).parent('.nav-item').addClass('active-trail');
        $.ajax({
            url: dataUrl
        }).done(function (html) {
            $(dataTarget).html(html);
            Tasks.init();
            $(".task-list").sortable({
                connectWith: ".task-list", items: ".task-item:not(.completed)", update: function () {
                    let dataString = "";
                    $('.task-list .task-item:not(.completed)').each(function () {
                        dataString += "tasks[][id]=" + $(this).data('id') + "&";
                    });
                    $.ajax({
                        data: dataString, dataType: "json", type: 'POST', url: tasks_order
                    });
                }
            });
        }).fail(function () {
            alert("error");
        });
        $('title').text($(this).text().trim() + " | myApp");
    },
};
Tasks.init();