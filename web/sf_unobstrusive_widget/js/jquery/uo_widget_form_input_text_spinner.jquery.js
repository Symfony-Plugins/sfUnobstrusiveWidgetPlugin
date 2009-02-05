/**
 * Unobstrusive spinner widget using jQuery.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_input_text_spinner_config = {};
(function($) {

  $.fn.uoWidgetFormInputTextSpinner = function(customConfiguration)
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
        if ($widget.hasClass('uo_widget_form_input_text_spinner_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_input_text_spinner');
        $widget.addClass('uo_widget_form_input_text_spinner_ON');
        $widget.spinner(getConfiguration());
      }
      
      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = uo_widget_form_input_text_spinner_config[$widget.attr('id')] || {};
        return $.extend(true, configuration, result);
      }
      
      init();
    });

  };

})(jQuery);

/**
 * Initialize widget.
 * Match all INPUT TEXT with "uo_widget_form_input_type_spinner" class.
 */
jQuery(document).ready(function()
{
  $(':text.uo_widget_form_input_text_spinner').uoWidgetFormInputTextSpinner({})
});