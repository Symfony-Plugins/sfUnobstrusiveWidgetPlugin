/**
 * Unobstrusive treeview widget using jQuery.
 * example : $('ul.uo_widget_list_treeview, ul.uo_widget_form_list_treeview,').uoWidgetFormListTreeview({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetListTreeview = function(customConfiguration)
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
        if ($widget.hasClass('uo_widget_list_treeview_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_list_treeview');
        $widget.addClass('uo_widget_list_treeview_ON');

        $widget.treeview(getConfiguration());
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

  $.fn.uoWidgetFormListTreeview = function(customConfiguration)
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
        if ($widget.hasClass('uo_widget_form_list_treeview_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_list_treeview');
        $widget.addClass('uo_widget_form_list_treeview_ON');

        $widget.uoWidgetListTreeview(getConfiguration());
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