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

var touch = false;
$(document).ready(function () {
  if (Modernizr.touch) {
    touch = true;
  }
  $('input[type=date]').datepicker({
    dateFormat: $('.datepicker').data('date-format')
  });

  $('.btn-confirm').click(function () {
    return confirm('Are you sure you want to perform this action?');
  });
//TODO: timer
  var request = $.ajax({
    url: "http://api.sunrise-sunset.org/json",
    method: "GET",
    dataType: "jsonp",
    data: {
      lat: 31.2185647,
      lng: 29.9315472,
      formatted: 0,
      callback: 'timeOfDay'
    }
  });
//  console.log(request);
});
