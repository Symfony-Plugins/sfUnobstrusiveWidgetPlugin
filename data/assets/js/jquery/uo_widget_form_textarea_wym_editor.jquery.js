/**
 * Unobstrusive wym editor widget using jQuery.
 * example : $('textarea.uo_widget_form_textarea_wym_editor').uoWidgetFormTextareaWymEditor({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetFormTextareaWymEditor = function(customConfiguration)
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
        if ($widget.hasClass('uo_widget_form_textarea_wym_editor_ON'))
        {
          return $widget;
        }
        
        if (undefined == $widget.wymeditor)
        {
          alert('wymeditor is undefined');
          return $widget;
        }

        $widget.removeClass('uo_widget_form_textarea_wym_editor');
        $widget.addClass('uo_widget_form_textarea_wym_editor_ON');
        $widget.wymeditor(getConfiguration());
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