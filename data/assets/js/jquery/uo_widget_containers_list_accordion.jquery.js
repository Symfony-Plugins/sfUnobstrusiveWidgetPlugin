/**
 * Unobstrusive accordion widget using jQuery.
 * example : $('ul.uo_widget_containers_list_accordion, ol.uo_widget_containers_list_accordion').uoWidgetContainersListAccordion({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetContainersListAccordion = function(customConfiguration)
  {
    // default configuration
    var configuration = {
      header: '.uo_widget_containers_list_title'
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
        if ($widget.hasClass('uo_widget_containers_list_accordion_ON'))
        {
          return $widget;
        }

        //create A element
        $('.uo_widget_containers_list_title', $widget).each(function()
        {
          var firstchild = this.firstChild;
          if ('a' != firstchild.nodeName.toLowerCase())
          {
            $(firstchild).before('<a href="#"></a>');
            $(firstchild).appendTo(this.firstChild);
          }
        });

        $widget.removeClass('uo_widget_containers_list_accordion');
        $widget.addClass('uo_widget_containers_list_accordion_ON');
        $widget.accordion(getConfiguration());
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