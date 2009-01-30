/**
 * Unobstrusive accordion widget using jQuery.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_list_accordion_config = {};
(function($) {

  $.fn.uoWidgetListAccordion = function(customConfiguration)
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
        if ($widget.hasClass('uo_widget_list_accordion_ON'))
        {
          return $widget;
        }

        //create A element
        $('.uo_widget_accordion_title', $widget).each(function()
        {
          var firstchild = this.firstChild;
          if ('a' != firstchild.nodeName.toLowerCase())
          {
            $(firstchild).before('<a href="#"></a>');
            $(firstchild).appendTo(this.firstChild);
          }
        });

        $widget.removeClass('uo_widget_list_accordion');
        $widget.addClass('uo_widget_list_accordion_ON');
        $widget.accordion(getConfiguration());
      }
      
      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var config    = uo_widget_list_accordion_config[$widget.attr('id')] || {};
        config.active = '.uo_widget_accordion_active';
        config.header = '.uo_widget_accordion_title';
        
        return config;
      }

      init();
    });

  };

})(jQuery);

/**
 * Initialize widget.
 * Match all UL or OL with "uo_widget_list_accordion" class.
 */
jQuery(document).ready(function()
{
  $('ul.uo_widget_list_accordion, ol.uo_widget_list_accordion').uoWidgetListAccordion({})
});