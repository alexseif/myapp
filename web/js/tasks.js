// Tasks Object
var Tasks = {
    isFocus: (window.location.pathname.indexOf('focus') !== -1) ? true : false,
    day: {
        completed: 0,
        time: 480,
        remaining:0
    },    

    init: function(){
        this.bindEvents();
    },

    bindEvents: function(){
        $('.task-list input[type="checkbox"]').change(this.updateTask);
        $('[data-toggle="popover"]').popover({html: true, container: "body"});
        if (!touch) {
            $("#tasks").sortable({
                connectWith: ".task-list",
                items: "li:not(.completed)",
                update: this.updateOrder
            });
        }
        if(this.isFocus){
            $('<ul class="list-group task-list" id="focus"></ul>').prependTo('.container');
            this.drawFocus();
            if (!touch) {
                $("#focus").sortable({
                    connectWith: ".task-list",
                    items: "li:not(.completed)",
                    update: this.updateOrder
                });
            }
        }
    },

    updateTask: function(){
        var taskEl = this;
        var isCompleted = $(taskEl).is(':checked') ? 1 : 0;
        //TODO: handle errors
        $.ajax({
            type: "POST",
            url: tasks_path + $(taskEl).data('taskid')+"/edit",
            dataType: "json",
            data: {
                "id": $(taskEl).data('taskid'),
                "completed": isCompleted
            }
        }).done(function (){
            if(isCompleted){
                $(taskEl).parent().parent().addClass('completed');
            }else{
                $(taskEl).parent().parent().removeClass('completed');
            }
            if(self.isFocus){
                self.drawFocus();
            }
        });
    },

    drawFocus: function(){
        self = this;
        completed = $('.completed');
        $('#completed').append(completed);
        $('li:not(.completed)').prependTo('#tasks');

        this.day.completed = 0;
        completed.each(function () {
            self.day.completed += ($(this).data("time")) ? $(this).data("time") : 0;
        });

        this.day.remaining = this.day.time - this.day.completed;
        while (this.day.remaining > 0) {
            task = $('#tasks li:first');
            if (task.length) {
                this.day.remaining -= (task.data("time")) ? task.data("time") : 0;
                $('#focus').append(task);
            } else {
                break;
            }
        }
        this.setFocusTaskHeight();
        this.focusTitle();
    },

    focusTitle: function(){
        $('title').text($('#focus li:first label').text().trim() + "| myApp");
    },

//Update Tasks size to fit screen
    setFocusTaskHeight: function(){
        $('#focus li').css('height', ((1 - ($('#completed').height() / $(window).height())) * 100 / $('#focus li').length + 'vh'));
        $('#completed li').css('height', 'auto');
        $('#tasks li').css('height', 'auto');
    },


//Update order of tasks based on sorting
    updateOrder: function () {
        var dataString = "";
        $('.task-list li:not(.completed)').each(function () {
            dataString += "tasks[][id]=" + $(this).data('id') + "&";
        });
        $.ajax({
            data: dataString,
            dataType: "json",
            type: 'POST',
            url: tasks_order
        }).done(function(){
            if(self.isFocus){
                self.drawFocus();
            }
        });
    }
}
Tasks.init();