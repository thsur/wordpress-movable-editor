
(function($) {

  /**
   * Config data coming in from the server.
   */
  var config = movable_editor_config;

  /**
   * Cloned body & head of an editor's iframe.
   *
   * Set when the editor is about to get moved,
   * inserted after movement has stopped.
   */
  var iframe = {};

  /**
   * Get TinyMCE instance.
   */
  var getEditor = function () {

    if (tinyMCE.editors[config.editor_id]) {

      return tinyMCE.editors[config.editor_id];
    }
  };

  /**
   * Get content area iframe from editor.
   */
  var getIFrame = function () {

    var editor = getEditor();

    if (editor) {

      return $(editor.contentAreaContainer).find('iframe');
    }
  };

  /**
   * Clone body & head of an editor's iframe.
   */
  var copyIFrameContent = function () {

    var current = getIFrame().contents();

    iframe.head = current.find('head').clone(true);
    iframe.body = current.find('body').clone(true);
  };

  /**
   * Replace body & head of an editor's iframe.
   */
  var updateIFrameContent = function () {

    var current = getIFrame().contents();

    current.find('head').replaceWith(iframe.head);
    current.find('body').replaceWith(iframe.body);

    iframe = {};
  };

  /**
   * Hook in
   */

  $(document).ready(function () {

    var $draggable = $('#' + config.meta_box_id); // Meta box the editor is in

    $('.meta-box-sortables').on('sortstart', function (event, ui) {

      if (ui.item.attr('id') === $draggable.attr('id')) {

        copyIFrameContent();
      }
    });

    $('.meta-box-sortables').on('sortstop', function (event, ui) {

      if (ui.item.attr('id') === $draggable.attr('id')) {

        updateIFrameContent();
      }
    });
  });

})(jQuery);
