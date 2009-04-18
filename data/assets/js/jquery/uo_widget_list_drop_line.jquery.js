/**
 * Unobstrusive drop line menu widget using jQuery.
 * example : $('ul.uo_widget_list_drop_line').uoWidgetListDropLine({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_list_drop_line_config = {};
(function($) {

  $.fn.uoWidgetListDropLine = function(customConfiguration)
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
        if ($widget.hasClass('uo_widget_list_drop_line_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_list_drop_line');
        $widget.addClass('uo_widget_list_drop_line_ON');

        //create container
        var containerId = 'uo_widget_list_drop_line_container_' + $widget.attr('id');
        $widget.before('<div class="uo_widget_list_drop_line_ON_container" id="'+containerId+'"></div>');
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

        hideAll();

        $('li', $widget).each(function()
        {
          if ($(this).parents('ul:first').hasClass('uo_widget_list_drop_line_ON'))
          {
            $('a:first', $(this)).click(displaySubLMenu);
          }
        });

        // open active
        $('a.active', $widget)
          .parents('ul:first').show().end()
          .parents('li').addClass('active');
      }

      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = uo_widget_list_drop_line_config[$widget.attr('id')] || {};
        return $.extend(true, configuration, result);
      }

      /**
       * Hide all children of menu
       */
      function hideAll()
      {
        $('li ul', $widget).hide();
      }

      /**
       * Display a sub menu
       */
      function displaySubLMenu(event)
      {
        if (this == event.target)
        {
          hideAll();
          $('ul:first', $(this).parents('li:first')).animate({opacity:'toggle'},'slow');
        }
      }

      init();
    });

  };

})(jQuery);