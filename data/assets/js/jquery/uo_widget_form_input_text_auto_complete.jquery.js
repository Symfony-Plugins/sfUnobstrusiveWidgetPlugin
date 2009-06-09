/**
 * Unobstrusive auto complete input widget using jQuery.
 *example : $(':text.uo_widget_form_input_text_auto_complete').uoWidgetFormInputTextAutoComplete({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetFormInputTextAutoComplete = function(customConfiguration)
  {
    // default configuration
    var configuration = {
      url: false,
      data: []
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
        if ($widget.hasClass('uo_widget_form_input_text_auto_complete_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_input_text_auto_complete');
        $widget.addClass('uo_widget_form_input_text_auto_complete_ON');
        
        var config = getConfiguration();
        if (config.url)
        {
          $widget.autocomplete(config.url, config);
        }
        else
        {
          $widget.autocompleteArray(config.data, config);
        }
      }

      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = {};
        return $.extend(true, configuration, result);
      }

      init();
    });

  };

})(jQuery);