/**
 * Unobstrusive  slider widget using jQuery.
 * example : $('select.uo_widget_form_select_slider').uoWidgetFormSelectSlider({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetFormSelectSlider = function(customConfiguration)
  {
    // default configuration
    var configuration = {
      width: 450
    };

    // merge default and custom configuration
    $.extend(true, configuration, customConfiguration);

    return this.each(function(index)
    {
      var $widget       = $(this);
      var $rangeWidget  = false;
      var $baseId       = '';

      /**
       * Initialize widget
       */
      function init()
      {
        // prevent initialize twice
        if ($widget.hasClass('uo_widget_form_select_slider_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_select_slider');
        $widget.addClass('uo_widget_form_select_slider_ON');

        var id   = $widget.attr('id') || '';
        $baseId  =  id.substr(0, id.lastIndexOf('_'));
        initRange();

        var selector = '#'+id;

        if ($rangeWidget)
        {
          $rangeWidget.removeClass('uo_widget_form_select_slider');
          $rangeWidget.addClass('uo_widget_form_select_slider_ON');

          $rangeWidget.prevAll('span.from:first').addClass('uo_widget_form_select_slider_ON');
          $rangeWidget.prevAll('span.to:first').addClass('uo_widget_form_select_slider_ON');

          selector += ', #'+$rangeWidget.attr('id');
        }

        $(selector).selectToUISlider(getConfiguration());
      }

      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = {};
        return $.extend(true, configuration, result);
      }

      /**
       * Initialize range
       */
      function initRange()
      {
        var id   = $widget.attr('id') || '';
        var name = $widget.attr('name') || '';

        if (name.lastIndexOf('[from]') > 0)
        {
          $rangeWidget = $('#' + id.replace('_from', '_to'));
          if ($rangeWidget.length < 1)
          {
            $rangeWidget = false;
          }
        }
      }

      init();
    });

  };

})(jQuery);