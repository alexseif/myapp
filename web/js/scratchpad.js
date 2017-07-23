/* 
 * The following content was designed & implemented under AlexSeif.com
 */
var Pad = tinymce.init({
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
  setup: function (editor) {
    editor.on('focus', function (e) {
      this.autoSave = setInterval(10000,
              ajaxSave);
    });
    editor.on('blur', function (e) {
      clearInterval(this.autoSave);
      ajaxSave(editor);
    });
  }
});

function ajaxSave(ed) {
  var editor = tinymce.get('scratchpad-content');
  var content = editor.getContent();
  editor.setProgressState(1); // Show progress
  editor.notificationManager.open({
    timeout: 500,
    text: 'saving...',
    type: 'info'
  });
  // Save contents using some XHR call
  $.ajax({
    type: 'POST',
    data: {
//      'id': $(editor).data('id'),
      'content': content
    },
    success(data) {
      if (data) {
        data = JSON.parse(data);
        if (data.redirect !== undefined && data.redirect) {
          // data.redirect contains the string URL to redirect to
          window.location.href = data.redirect;
        }
      }
//      var tmp = JSON.parse(data);
//      $(editor).data('id', tmp.id);
      editor.setDirty(false); // Force not dirty state

      editor.notificationManager.close();
      editor.notificationManager.open({
        timeout: 500,
        text: 'saved',
        type: 'success'
      });
    },
    error(data) {
      editor.notificationManager.close();
      editor.notificationManager.open({
        timeout: 500,
        text: 'Error',
        type: 'error'
      });
    }
  });
}