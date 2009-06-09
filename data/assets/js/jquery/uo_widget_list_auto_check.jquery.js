/**
 * Unobstrusive checklist widget with auto check capability using jQuery.
 * example : $('ul.uo_widget_form_list_auto_check, ol.uo_widget_form_list_auto_check').uoWidgetFormListAutoCheck({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetFormListAutoCheck = function(customConfiguration)
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
        if ($widget.hasClass('uo_widget_form_list_auto_check_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_list_auto_check');
        $widget.addClass('uo_widget_form_list_auto_check_ON');

        $widget.find(':checkbox').click(function()
        {
          if ($(this).attr('checked'))
          {
            $(this).parents('li:first').find(':checkbox').attr('checked', 'checked');
            $(this).parents('li').each(function(index)
            {
              $(this).find(':checkbox:first').attr('checked', 'checked');
            });
          }
          else
          {
            $(this).parents('li:first').find(':checkbox').removeAttr('checked');
          }
        });
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