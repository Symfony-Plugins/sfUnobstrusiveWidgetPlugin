/**
 * Unobstrusive drop line menu widget using jQuery.
 * example : $('ul.uo_widget_list_drop_line').uoWidgetListDropLine({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.widget('ui.uoWidgetListDropLine', 
  {
 
    _init: function()
    {
      // prevent initialize twice
      if (this.element.hasClass('uo_widget_list_drop_line_ON'))
      {
        return false;
      }
      
      this.element.removeClass('uo_widget_list_drop_line');
      this.element.addClass('uo_widget_list_drop_line_ON');

      this.id                 = this.element.attr('id');
      this.container          = $('<div class="ui-uoWidgetListDropLine ui-helper-clearfix ui-widget"></div>').insertAfter(this.element);
      this.element.appendTo(this.container);
      
      this.showTimer          = 0;
      this.hideTimer          = 0;
      this.currentElement     = false;
      this.activated          = false;

      // register events
      this._registerListEvents(this.container.find('select'));
      
      this._showCurrents();
    },
    
    destroy: function()
    {
      this.element.removeClass('uo_widget_list_drop_line_ON');
      this.element.addClass('uo_widget_list_drop_line');
    },
    
    _showCurrents: function()
    {
      this._hideAll();
      
      $('.active', this.element)
        .parents('ul').show().end()
        .find('ul:first').show();
    },
    
    _hideAll: function(element)
    {
      clearTimeout(this.showTimer);
      clearTimeout(this.hideTimer);
      
      if (undefined != element)
      {
        $('ul', this.element).not(element.parents()).end().not($('ul:first', element)).hide();
      }
      else
      {
        $('ul', this.element).hide();
      }
    },
    
    _hide: function(element)
    {
      var that       = this;
      this.activated = false;
      clearTimeout(this.showTimer);

      this.hideTimer = setTimeout(
        function()
        {
          if (that.activated)
          {
            if (that.options.animate)
            {
              $('ul', element).fadeOut(that.options.fade_out_speed);
            }
            else
            {
              $('ul:first', element).hide();
            }
          }
          else
          {
            that._showCurrents();
          }
        }, 
        that.options.time_before_hide
      );
    },
    
    _show: function(element)
    {
      var that       = this;
      this.activated = true;
      clearTimeout(this.hideTimer);
      
      this._hideAll($(element));
      
      $(element).parents('ul').show();
      
      this.showTimer = setTimeout(
        function()
        {
          if (that.options.animate)
          {
            $('ul:first', element).fadeIn(that.options.fade_in_speed);
          }
          else
          {
            $('ul:first', element).show();
          }
        }, 
        that.options.time_before_show
      );
    },
    
    _cancelHide: function()
    {
      clearTimeout(this.hideTimer);
    },
    
    _registerListEvents: function(elements)
    {
      var that = this;
      
      //create A element
      $('li', this.element).each(function()
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
      
      $('li a', this.element)
        .hover(function(){ that._show($(this).parent()) }, function(){ that._hide($(this).parent()) })
        .focus(function(){ that._show($(this).parent()) })
        .blur(function(){ that._hide($(this).parent()) });
      
      $('ul', this.element)
        .mouseover(function(){ that._cancelHide() })
        .mouseleave(function(){ that._hide($(this)) });
    }

  });
  
  $.extend($.ui.uoWidgetListDropLine, {
    defaults: {
      time_before_show: 200,
      time_before_hide: 200,
      animate: true,
      fade_in_speed: 'fast',
      fade_out_speed: 'fast'
    }
  });

})(jQuery);