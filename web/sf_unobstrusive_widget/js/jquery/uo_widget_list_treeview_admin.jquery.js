/**
 * Unobstrusive  admin treeview widget using jQuery.
 * example : $('ul.uo_widget_list_treeview_admin').uoWidgetListTreeviewAdmin({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_list_treeview_admin_config = {};
(function($) {

  $.fn.uoWidgetListTreeviewAdmin = function(customConfiguration)
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
        if ($widget.hasClass('uo_widget_list_treeview_admin_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_list_treeview_admin');

        //create root if not exists
    		if ($widget.find('.root').length < 1)
        {
          $widget.before('<ul class="uo_widget_list_treeview_admin_ON"><li class="root">root</li></ul>');
          $widget = $widget.prev();
          $widget.appendTo($widget.find('li:first'));
        }
        else
        {
          $widget.addClass('uo_widget_list_treeview_admin_ON');
        }

        //create span
        $('li', $widget).each(function()
        {
          var firstchild = this.firstChild;
          if ('span' != firstchild.nodeName.toLowerCase() )
          {
            $(firstchild).before('<span></span>');
            $(firstchild).appendTo(this.firstChild);
          }
        });

        $widget.simpleTree(getConfiguration());
      }

      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = uo_widget_list_treeview_admin_config[$widget.attr('id')] || {};
        return $.extend(true, configuration, result);
      }

      init();
    });

  };

})(jQuery);