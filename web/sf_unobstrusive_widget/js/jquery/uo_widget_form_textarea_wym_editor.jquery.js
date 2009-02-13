/**
 * Unobstrusive wym editor widget using jQuery.
 * example : $('textarea.uo_widget_form_textarea_wym_editor').uoWidgetFormTextareaWymEditor({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_textarea_wym_editor_config = {};
(function($) {

  $.fn.uoWidgetFormTextareaWymEditor = function(customConfiguration)
  {
    // default configuration
    var configuration = {};

    // merge default and custom configuration
    $.extend(true, configuration, customConfiguration);

    return this.each(function(index)
    {
      var $widget = $(this);

      /**
       * Initialize widget
       */
      function init()
      {
        // prevent initialize twice
        if ($widget.hasClass('uo_widget_form_textarea_wym_editor_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_textarea_wym_editor');
        $widget.addClass('uo_widget_form_textarea_wym_editor_ON');
        $widget.wymeditor(getConfiguration());
      }

      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = uo_widget_form_select_many_asm_config[$widget.attr('id')] || {};
        return $.extend(true, configuration, result);
      }

      init();
    });

  };

})(jQuery);