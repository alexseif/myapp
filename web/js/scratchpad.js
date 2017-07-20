/* 
 * The following content was designed & implemented under AlexSeif.com
 */
var scratchPad = {

  init: function () {
    $('footer').append(this.draw());
    tinymce.init({
      selector: '#scratchpad-content',
      toolbar: false,
      menubar: false,
      //TODO: generate cache
      cache_suffix: '?v=4.1.6',
      inline: true,
      force_br_newlines: false,
      force_p_newlines: false,
      forced_root_block: 'div',
      forced_root_block_attrs: {
        class: 'list-group-item'
      },
      setup: function (ed) {
        ed.on('keydown', function (e) {
          if (e.keyCode == 13) {
            scratchPad.ajaxSave();
          }
        });
      },
    });
  },
  draw: function () {
    var widgetBody = $('<div id="scratchpad-collapse" class="panel-collapse collapse"><div id="scratchpad-content" class="panel-body list-group"></div></div>');
    var widgetHeading = $('<div class="panel-heading clearfix"><button data-toggle="collapse" href="#scratchpad-collapse" class="btn btn-default btn-xs btn-block collapsed">Scratch Pad</button></div>');
    var widget = $('<div id="scratchpad-widget" class="panel panel-default"></div>');
    widget
            .append(widgetHeading)
            .append(widgetBody);
    return widget;
  },

  ajaxSave: function () {
    var editor = tinymce.get('scratchpad-content');
    editor.setProgressState(1); // Show progress
    editor.notificationManager.open({
      timeout: 500,
      text: 'saving...',
      type: 'info'
    });
    // Save contents using some XHR call
    window.setTimeout(function () {
      editor.setProgressState(0); // Hide progress
      console.log(editor.getContent());
    }, 3000);

    editor.setDirty(false); // Force not dirty state

    editor.notificationManager.close();
    editor.notificationManager.open({
      timeout: 500,
      text: 'saved',
      type: 'success'
    });
  }
}
scratchPad.init();
/*
 function ajaxLoad() {
 var ed = tinyMCE.get('content');
 
 // Do you ajax call here, window.setTimeout fakes ajax call
 ed.setProgressState(1); // Show progress
 window.setTimeout(function () {
 ed.setProgressState(0); // Hide progress
 ed.setContent('HTML content that got passed from server.');
 }, 3000);
 }
 
 function ajaxSave() {
 var ed = tinyMCE.get('content');
 
 // Do you ajax call here, window.setTimeout fakes ajax call
 window.setTimeout(function () {
 ed.setProgressState(0); // Hide progress
 alert(ed.getContent());
 }, 3000);
 }
 */
