import './shortcut';
import $ from 'jquery';
import { GoogleCharts } from '../../node_modules/google-charts';

// Shortcut keys
shortcut.add("alt+T", function () {
    location.href = "/tasks/new";
});
shortcut.add("alt+D", function () {
    location.href = "/days/new";
});
shortcut.add("alt+F", function () {
    location.href = "/focus";
});

// Google Charts
GoogleCharts.load(drawChart);

function taskCompletionByDay() {
    var data = GoogleCharts.api.visualization.arrayToDataTable([
        ['Day', 'Tasks'],
        ...window.tskCnt.map(t => [t.day.slice(0, 3).toUpperCase(), t.tasks])
    ]);
    var options = {
        legend: 'none',
        pieSliceText: 'label',
        pieHole: 0.4,
        backgroundColor: 'transparent',
        pieSliceBorderColor: '#222222',
    };
    var chart = new GoogleCharts.api.visualization.PieChart(document.getElementById('taskCompletionGraph'));
    chart.draw(data, options);
}

function tasksByPriority() {
    var data = GoogleCharts.api.visualization.arrayToDataTable([
        ['TaskType', 'TaskCount'],
        ...window.piechart.map(p => [p.key, p.count])
    ]);
    var options = {
        legend: 'none',
        pieSliceText: 'value',
        backgroundColor: 'transparent',
        pieSliceBorderColor: '#222222',
        slices: {
            0: { color: '#f39c12' },
            1: { color: '#f39c12' },
            2: { color: '#e74c3c' },
            3: { color: '#375a7f' },
            4: { color: '#464545' }
        }
    };
    var chart = new GoogleCharts.api.visualization.PieChart(document.getElementById('tasksByPriority'));
    chart.draw(data, options);
}

function drawChart() {
    taskCompletionByDay();
    tasksByPriority();
}

$(window).resize(function () {
    drawChart();
});

$(document).ready(function () {
    $('.holidays-list .card').each(function () {
        var holiday = this;
        var timestamp = $(holiday).data('date');
        $('.days-list .card').each(function () {
            if (timestamp >= $(this).data('date')) {
                $(holiday).insertAfter(this);
            }
        });
    });
    var i = 0;
    $('.days-list .card').each(function () {
        var breaker = $('<div class="w-100"></div>');
        i++;
        if (0 === (i % 2)) {
            $(breaker).insertAfter(this);
        }
    });
});