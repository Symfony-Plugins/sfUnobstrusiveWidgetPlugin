/**
 * Unobstrusive drop down menu widget using jQuery.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_list_dropdown_config = {};
(function($) {

  $.fn.uoWidgetListDropdown = function(customConfiguration)
  {
    // default configuration
    var configuration = {};

    // merge default and custom configuration
    $.extend(true, configuration, customConfiguration);

    return this.each(function(index)
    {
      var $widget    = $(this);
      var $container = false;

      /**
       * Initialize widget
       */
      function init()
      {
        // prevent initialize twice
        if ($widget.hasClass('uo_widget_list_dropdown_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_list_dropdown');
        $widget.addClass('uo_widget_list_dropdown_ON');
        
        //create container
        var containerId = 'uo_widget_list_dropdown_container_' + $widget.attr('id');
        $widget.before('<div class="uo_widget_list_dropdown_ON_container" id="'+containerId+'"></div>');
        $container = $widget.prev();
        $widget.appendTo($container);
        
        //fix float style
        $container.append('<div style="clear:both"></div>');
        
        //create A element
        $('li', $widget).each(function()
        {
          var firstchild = this.firstChild;
          if ('a' != firstchild.nodeName.toLowerCase())
          {
            $(firstchild).before('<a href="#"></a>');
            $(firstchild).appendTo(this.firstChild);
            $(this.firstChild).click(function()
            { 
              return false;
            });
          }
        });
        
        ddsmoothmenu.init(getConfiguration());
      }
      
      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = uo_widget_list_dropdown_config[$widget.attr('id')] || {};
        
        //disable shadow on IE6
        var strChUserAgent  = navigator.userAgent;
        var intSplitStart   = strChUserAgent.indexOf("(",0);
        var intSplitEnd     = strChUserAgent.indexOf(")",0);
        var strChMid        = strChUserAgent.substring(intSplitStart, intSplitEnd);
        if (strChMid.indexOf("MSIE 6") != -1)
        {
          ddsmoothmenu.shadow = {enabled:false};
        }
        
        result.mainmenuid     = $container.attr('id');
        result.contentsource  = 'markup';
        
        return $.extend(true, configuration, result);
      }

      init();
    });

  };

})(jQuery);

/**
 * Initialize widget.
 * Match all UL with "uo_widget_list_dropdown" class.
 */
jQuery(document).ready(function()
{
  $('ul.uo_widget_list_dropdown').uoWidgetListDropdown({})
});