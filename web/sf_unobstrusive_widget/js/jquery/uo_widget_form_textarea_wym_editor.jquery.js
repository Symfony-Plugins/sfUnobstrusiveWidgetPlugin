/**
 * Unobstrusive wym editor widget using jQuery.
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
        return uo_widget_form_textarea_wym_editor_config[$widget.attr('id')] || {};
      }

      init();
    });

  };

})(jQuery);

/**
 * Initialize widget.
 * Match all TEXTAREA with "uo_widget_form_textarea_wym_editor" class.
 */
jQuery(document).ready(function()
{
  $('textarea.uo_widget_form_textarea_wym_editor').uoWidgetFormTextareaWymEditor({})
});