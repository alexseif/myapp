function TasksObj() {
    this.containerSel = '.container';
    this.completedSel = '.completed';
}

// Task Checked / Unchecked
TasksObj.prototype.change = function () {
    $li = $(this).parent().parent();
    $ul = $li.parent();
    $ajaxValue = ($(this).is(":checked")) ? 1 : 0;
    $.ajax({
        type: "POST",
        url: tasks_path + $(this).data('taskid') + "/edit",
        dataType: "json",
        data: {
            "completed": $ajaxValue
        }
    }).done(function () {
        if ($ajaxValue) {
            $($li).addClass('completed');
        } else {
            $($li).removeClass('completed');
        }
        if (this.isFocus()) {
            this.focus.draw();
        }
    });
}
// Tasks Initiate
TasksObj.prototype.init = function () {
    this.setFocusPage();
    // Checking Tasks
    $('.task-list input[type="checkbox"]').change(this.change);
    //Popover
    $('[data-toggle="popover"]').popover({html: true});
    if (this.isFocus) {
        this.focus.init();
        console.log('Focus Page');
    } else {
        console.log('Not focus page');
    }
}

function FocusObj() {
    TasksObj.call();
    this.day = 480;
    this.completed = 0;
    this.lowest = 480;
    this.remaining = 480;
}
//Delegation / inheritance
FocusObj.prototype = Object.create(TasksObj.prototype);
FocusObj.prototype.constructor = FocusObj;

//Update HTML title with first available task
FocusObj.prototype.updateTitle = function () {
    //TODO: Enhance
    $list = $('.task-list input[type="checkbox"]');
    $newTitle = true;
    $i = 0;
    while ($newTitle) {
        if (!$($list[$i]).is(':checked')) {
            $text = $($list[$i]).parent().text().trim();
            $newTitle = false;
        }
        $i++;
    }
    $('title').text($text + "| myApp");
}

//Update Tasks size to fit screen
FocusObj.prototype.drawSize = function () {
    $('#focus li').css('height', ((1 - ($('#completed').height() / $(window).height())) * 100 / $('#focus li').length + 'vh'));
    $('#completed li').css('height', 'auto');
    $('#tasks li').css('height', 'auto');
}

//Set Focus tasks in focus UL 
FocusObj.prototype.draw = function () {
    completed = $('.completed');
    $('#completed').append(completed);
    $('li:not(.completed)').prependTo('#tasks');

    this.completed = 0;
    completed.each(function () {
        this.completed += ($(this).data("time")) ? $(this).data("time") : 0;
    });

    this.remaining = this.day - this.completed;
    while (this.remaining > 0) {
        task = $('#tasks li:first');
        if (task.length) {
            this.remaining -= (task.data("time")) ? task.data("time") : 0;
            $('#focus').append(task);
        } else {
            break;
        }
    }
    this.title();
    this.drawSize();
}
//Update order of tasks based on sorting
FocusObj.prototype.updateOrder = function () {
    var dataString = "";
    $('.task-list li').each(function () {
        dataString += "tasks[][id]=" + $(this).data('id') + "&";
    });
    $.ajax({
        data: dataString,
        dataType: "json",
        type: 'POST',
        url: tasks_order
    }).done(drawFocusTasks());
}
// Inititate Focus tasks
FocusObj.prototype.init = function () {
    this.ul.prependTo(this.containerSel);
    // Disabling on mobile devices
    // Maybe I should use screen width
    if (!touch) {
        $("#focus, #tasks").sortable({
            connectWith: ".task-list",
            items: "li:not(.completed)",
            update: this.updateOrder
        });
    }
    this.draw();
}

// Old code needs refactoring
var tasks = {
    focusPage: false,
    setFocusPage: function () {
        $path = window.location.pathname;
        this.focusPage = ($path.indexOf('focus') !== -1);
    },
    isFocus: function () {
        return this.focusPage;
    },
    focus: {
        ul: $('<ul class="list-group task-list" id="focus"></ul>'),
    }
};