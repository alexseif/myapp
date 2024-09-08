function timeOfDay(response) {
    var now = Date.now();
    var sunset = Date.parse(response.results.sunset);
    var sunrise = Date.parse(response.results.sunrise);
    if (now > sunrise && now < sunset) {
//  day
        console.log('day');
//    $('body').css('background-color', '#2CDDEB');
    } else {
//  night
        console.log('night');
//    $('body').css('background-color', '#27495C');
    }
}

let touch = false;
window.touch = touch;
let pageSession = {"hide": []};
var ONE_HOUR = 60 * 60 * 1000; /* ms */

function loadPageSession() {
    if (Modernizr.localstorage) {
        if ((Date.now() - pageSession.created) > ONE_HOUR) {
            clearPageSession();
        }
        pageSession = (localStorage.getItem("pageSession") !== null) ? JSON.parse(localStorage.getItem("pageSession")) : {
            hide: [],
            created: Date.now()
        };


    }
}

function savePageSession() {
    if (Modernizr.localstorage) {
        localStorage.setItem("pageSession", JSON.stringify(pageSession));
    }
}

/**
 *
 * @returns {boolean}
 */
function clearPageSession() {
    localStorage.clear();
    location.reload();
    return false;
}

/**
 *
 * @returns {boolean}
 */
function deleteMe() {
    pageSession["hide"].push($(this).parent().data('id'));
    savePageSession();
    $(this).parent().remove();
    return false;
}

function deleteMeFromPageSession() {
    for (const hideSessionKey in pageSession.hide) {
        ele = $('#' + pageSession.hide[hideSessionKey]);
        if (ele.length)
            ele.remove();
    }
}

$(document).ready(function () {
    // touch
    if (typeof Modernizr == 'object') {
        if (Modernizr.touch) {
            touch = true;
            window.touch = touch;
        }
    }

    loadPageSession();
    deleteMeFromPageSession();

    $('#bottom-bar-btn').click(function () {
        var bottomBarNav = $('#bottom-bar .nav');
        bottomBarNav.toggle();
        document.cookie = bottomBarNav.is(':visible') ? "bottom-bar-closed=0;" : "bottom-bar-closed=1;";
    });
    $('input[type=date]').datepicker({
        dateFormat: 'yy-mm-dd'
    });
//  $('.datepicker').datepicker({
//    dateFormat: 'dd/mm/yy'
//  });

    $('.btn-confirm').click(function () {
        return confirm('Are you sure you want to perform this action?');
    });

    $('.btn-modal').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        $.ajax({
            type: "GET",
            url: $(this).attr("href"),
            dataType: "json"
        }).done(function (data) {
            $('#ajax-container').html(data);
            $('.modal').modal('show');
        });
    });

    $('.btn-ajax-delete').click(function (event) {
        _self = this;
        event.preventDefault();
        event.stopPropagation();
        $.ajax({
            type: "DELETE",
            url: $(this).data("delete-url"),
            // dataType: "json",
            data: $(this).data('data')
        }).done(function () {
            $(_self).parents('tr').remove();
        });
    });

//TODO: timer
    $.ajax({
        url: "https://api.sunrise-sunset.org/json",
        method: "GET",
        dataType: "jsonp",
        data: {
            lat: 31.2185647,
            lng: 29.9315472,
            formatted: 0,
            callback: 'timeOfDay'
        }
    });
    //btn-app-post
    $('.btn-app-post').click(function () {
        $url = $(this).data('action');
        $post = $(this).data('post');
        $.ajax({
            url: $url,
            method: "POST",
            data: {'data': $post},
            success: function () {
                console.log('success');
            },
            error: function (e) {
                console.log('error: ' + e);
            }
        });
    });
    initClock();

    $('.delete-me').click(deleteMe);

    $('#clear-storage').click(clearPageSession);
});

function getBottomBarDetails() {
    $url = $('#bottom-bar').data('url');
    $.get($url, function (data) {
        $('#bottom-bar').html(data);
    });
}

//https://codepen.io/jasonleewilson/pen/gPrxwX
// START CLOCK SCRIPT

Number.prototype.pad = function (n) {
    for (var r = this.toString(); r.length < n; r = 0 + r) ;
    return r;
};

function updateClock() {
    var now = new Date();
    var sec = now.getSeconds(),
        min = now.getMinutes(),
        hou = now.getHours(),
        mo = now.getMonth(),
        dy = now.getDate(),
        yr = now.getFullYear();
    // var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var tags = ["mon", "d", "y", "h", "m", "s"],
        corr = [(mo + 1).pad(2), dy, yr, hou.pad(2), min.pad(2), sec.pad(2)];
    for (var i = 0; i < tags.length; i++) {
        if (document.getElementById(tags[i]))
            document.getElementById(tags[i]).firstChild.nodeValue = corr[i];
    }
}

function initClock() {
    updateClock();
    window.setInterval("updateClock()", 1000);
}

// END CLOCK SCRIPT