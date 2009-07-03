/**
 * Unobstrusive  button set widget using jQuery.
 * example : $('select.uo_widget_form_select_button_set').uoWidgetFormSelectButtonSet({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetFormSelectButtonSet = function(customConfiguration)
  {
    // default configuration
    var configuration = {
    };

    // merge default and custom configuration
    $.extend(true, configuration, customConfiguration);

    return this.each(function(index)
    {
      var $widget       = $(this);

      /**
       * Initialize widget
       */
      function init()
      {
        // prevent initialize twice
        if ($widget.hasClass('uo_widget_form_select_button_set_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_select_button_set');
        $widget.addClass('uo_widget_form_select_button_set_ON');

        $widget.selectbuttonset(getConfiguration());
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