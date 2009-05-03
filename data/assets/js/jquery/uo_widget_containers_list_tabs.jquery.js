/**
 * Unobstrusive tab widget using jQuery.
 * example : $('ul.uo_widget_containers_list_tabs, ol.uo_widget_containers_list_tabs').uoWidgetContainersListTabs({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetContainersListTabs = function(customConfiguration)
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
        if ($widget.hasClass('uo_widget_containers_tab_accordion_ON'))
        {
          return $widget;
        }

        //create A element
        var widgetId = $widget.attr('id');
        $('.uo_widget_containers_list_title', $widget).each(function(i)
        {
          var firstchild = this.firstChild;
          if ('a' != firstchild.nodeName.toLowerCase())
          {
            $(firstchild).before('<a href="#'+widgetId+'_'+i+'"></a>');
            $(firstchild).appendTo(this.firstChild);
          }
        });
        
        $('div.uo_widget_containers_list_content', $widget).each(function(i)
        {
          $(this).attr('id', widgetId+'_'+i);
        });

        $widget.removeClass('uo_widget_containers_list_tab');
        $widget.addClass('uo_widget_containers_list_tab_ON');
        
        $widget.before('<div class="uo_widget_containers_list_tab_container_ON"></div>');
        $widget.appendTo($widget.prev());
        $widget.find('div.uo_widget_containers_list_content').appendTo($widget.parent());
        
        $widget.parent().tabs(getConfiguration());
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