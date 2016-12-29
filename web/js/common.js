$(document).ready(function () {
  $('.datepicker').datepicker({
    dateFormat: $('.datepicker').data('date-format')
  });
  $('form :input').first().focus();
});