/**
 * Unobstrusive multiselect widget using jQuery.
 * example : $('select.uo_widget_form_select_many_multiselect').uoWidgetFormSelectManyMultiselect({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetFormSelectManyMultiselect = function(customConfiguration)
  {
    // default configuration
    var configuration = {
      sortable: false
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
        if ($widget.hasClass('uo_widget_form_select_many_multiselect_ON'))
        {
          return $widget;
        }

        // optgroup not working for now ... remove them
        if ($('optgroup', $widget).length > 0)
        {
          $('option', $widget).prependTo($widget);
          $('optgroup', $widget).remove();
        }

        $widget.removeClass('uo_widget_form_select_many_multiselect');
        $widget.addClass('uo_widget_form_select_many_multiselect_ON');
        $widget.multiselect(getConfiguration());
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