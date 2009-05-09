/**
 * Unobstrusive spinner widget using jQuery.
 *example : $(':text.uo_widget_form_input_text_spinner').uoWidgetFormInputTextSpinner({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetFormInputTextSpinner = function(customConfiguration)
  {
    // default configuration
    var configuration = {
      min: 0
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
        if ($widget.hasClass('uo_widget_form_input_text_spinner_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_input_text_spinner');
        $widget.addClass('uo_widget_form_input_text_spinner_ON');
        
        if (1>$widget.attr('maxlength'))
        {
          var maxLength = 2;
          if (undefined != configuration.max)
          {
            length   = String(configuration.max).length;
            if (length > maxLength)
            {
              maxLength = length;
            }
            if ($widget.val() > configuration.max)
            {
              $widget.val(configuration.max)
            }
          }

          if (undefined != configuration.min)
          {
            length   = String(configuration.min).length + 1;
            if (length > maxLength)
            {
              maxLength = length;
            }
            if ($widget.val() < configuration.min)
            {
              $widget.val(configuration.min)
            }
          }

          $widget.attr('maxlength', maxLength);
        }

        $widget.spinner(configuration);
      }

      init();
    });

  };

})(jQuery);