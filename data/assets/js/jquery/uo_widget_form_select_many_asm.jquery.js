/**
 * Unobstrusive asm (alternative select many) widget using jQuery.
 * example : $('select.uo_widget_form_select_many_asm').uoWidgetFormSelectManyAsm({});
 *
 * original version by Ryan Cramer (http://www.ryancramer.com/journal/entries/select_multiple/)
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.widget('ui.uoWidgetFormSelectManyAsm', 
  {
 
    _init: function()
    {
      // prevent initialize twice
      if (this.element.hasClass('uo_widget_form_select_many_asm_ON'))
      {
        return false;
      }
      
      this.element.removeClass('uo_widget_form_select_many_asm');
      this.element.addClass('uo_widget_form_select_many_asm_ON');

      this.id                 = this.element.attr('id');
      this.container          = $('<div class="ui-uoWidgetFormSelectManyAsm ui-helper-clearfix ui-widget"></div>').insertAfter(this.element);
      this.choices            = this.element.find('option').clone();
      this.header             = $('<div class="ui-helper-clearfix"><select class="ui-widget-content"></select></div>').appendTo(this.container);
      this.itemsContainer     = $('<' + this.options.list_type + '></' + this.options.list_type + '>').appendTo(this.container);

      this._appendChoices();

      // register events
      this._registerListEvents(this.container.find('select'));
      this._registerOriginalWidgetEvents();
      
      if (this.options.sortable)
      {
        this._makeSortable();
      }
      
      this._updateValuesFromOriginal();
    },
    
    destroy: function()
    {
      this.element.removeClass('uo_widget_form_select_many_asm_ON');
      this.element.addClass('uo_widget_form_select_many_asm');
      
      this.container.remove();
    },
    
    _appendChoices: function()
    {
      if (this.choices.eq(0).attr('value') != '')
      {
        this.header.find('select').append('<option value="">&nbsp;</option>');
      }

      this.header.find('select').append(this.choices);
    },
    
    _addItem: function(updateValues)
    {
      var selected = this.header.find('select option:selected');

      if (selected.attr('value') != '')
      {
        
        var content;
        var actions = [];
        var action  = '';

        for (var actionName in this.options.actions)
        {
          content = '';
          if (this.options.actions[actionName].enable)
          {
            if (this.options.actions.image)
            {
              content = '<img alt="' + this.options.actions[actionName].label + '" src="' + this.options.actions[actionName].image + '" />';
            }
            else
            {
              content = this.options.actions[actionName].label;
            }
            
            var iconLinkClass = '';
            if (this.options.actions[actionName].ui_icon)
            {
              iconLinkClass      = 'class="ui-corner-all ui-icon ' + this.options.actions[actionName].ui_icon + '"';
            }
          
            actions.push('<li class="' + actionName + '"><a href="#' + actionName + '"' + iconLinkClass + '>' + content + '</a></li>');
          }
        }
      
        if (actions.length > 0)
        {
          action = '<ul>' + actions.join ('') + '</ul>';
        }
        
        var content = selected.text();
        if (this.options.sortable)
        {
          content = '<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' + content;
        }
        
        var template = '<li class="ui-element ui-state-default"><input type="hidden" value="' + selected.attr('value') + '" />' + action + '<span>' + content + '</span></li>';
        if ('top' == this.options.add_item_target)
        {
          this.itemsContainer
          .prepend(template);
        
          this._registerItemEvents(this.itemsContainer.find('li.ui-element:first'));
        }
        else
        {
          this.itemsContainer
          .append(template);
        
          this._registerItemEvents(this.itemsContainer.find('li.ui-element:last'));
        }

      }

      if (updateValues)
      {
        this._updateValues();
      }
      
      this.header.find('select option:selected').removeAttr('selected');
      if (this.options.sortable)
      {
        this._makeSortable();
      }
      
      return false;
    },
    
    _removeItem: function(element)
    {
      element.remove();
      
      this._updateValues();
      return false;
    },
    
    _updateValues: function()
    {
      var that = this;

      this.element.find('option:selected').removeAttr('selected');
      this.header.find('option:disabled').removeAttr('disabled', 'disabled');
      
      this.itemsContainer.find(':hidden').each(function()
      {
        var value = $(this).val();

        that.element
          .find('option[value="' + value + '"]').attr('selected', 'selected')
          .appendTo(that.element);
        that.header.find('option[value="' + value + '"]').attr('disabled', 'disabled');
        
      });
    },
    
    _updateValuesFromOriginal: function()
    {
      var that = this;

      this.itemsContainer.find('li.ui-element').remove();
      this.header.find('option:disabled').removeAttr('disabled', 'disabled');
      
      this.element.find('option:selected').each(function()
      {
        var value = $(this).val();

        that.header
          .find('option[value="' + value + '"]').attr('selected', 'selected').attr('disabled', 'disabled');
        that._addItem(false);
      });
    },
    
    _makeSortable: function()
    {
      that = this;

      // make any items in the selected list sortable
      // requires jQuery UI sortables, draggables, droppables
      this.itemsContainer
        .sortable('destroy');
      this.itemsContainer
        .sortable({
          items: 'li.ui-element',
          axis: 'y',
          update: function(e, data)
          {
            that._updateValues();
          }
        });
    },

    _registerListEvents: function()
    {
      var that = this;
      
      this.header.find('select')
        .change(function() { that._addItem(true); return false; })
        .keypress(function(event) { if (13 == event.keyCode){ that._addItem(true); return false; } });
    },
    
    _registerItemEvents: function(elements)
    {
      var that = this;
      
      elements
        .hover(function() { $(this).addClass('ui-state-hover'); }, function() { $(this).removeClass('ui-state-hover'); })
        .find('ul li.remove a').click(function(){ that._removeItem($(this).parents('li.ui-element')); return false; });
    },
    
    _registerOriginalWidgetEvents: function(elements)
    {
      var that = this;
      
      this.element.bind('change', function(){ that._updateValuesFromOriginal() });
    }

  });
  
  $.extend($.ui.uoWidgetFormSelectManyAsm, {
    defaults: {
      actions: {
        remove: {
          enable: true,
          label: 'Remove',
          image: false,
          ui_icon: 'ui-icon-close'
        }
      },
      list_type: 'ol',         // ol or ul
      sortable: false,         // Use the sortable feature? 
      add_item_target: 'bottom', // Where to place new selected items in list: top or bottom
      hide_when_added: false     // Hide the option when added to the list
    }
  });

})(jQuery);