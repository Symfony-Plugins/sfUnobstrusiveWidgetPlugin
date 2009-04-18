/**
 * Unobstrusive masked input widget using jQuery.
 *example : $(':text.uo_widget_form_input_text_mask').uoWidgetFormInputTextMask({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_input_text_mask_config = {};
(function($) {

  $.fn.uoWidgetFormInputTextMask = function(customConfiguration)
  {
    // default configuration
    var configuration = {
      'mask': ''
    };

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
        if ($widget.hasClass('uo_widget_form_input_text_mask_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_input_text_mask');
        $widget.addClass('uo_widget_form_input_text_mask_ON');
        
        var config = getConfiguration();
        var mask   = config.mask || '';
        $widget.mask(mask, config);
      }

      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = uo_widget_form_input_text_mask_config[$widget.attr('id')] || {};
        return $.extend(true, configuration, result);
      }

      init();
    });

  };

})(jQuery);