var touch = false;
$(document).ready(function () {
  if (Modernizr.touch) {
    touch = true;
  }
  $('.datepicker').datepicker({
    dateFormat: $('.datepicker').data('date-format')
  });
  
  $('.btn-delete').click(function () {
    return confirm('Are you sure you want to delete?');
  });
});