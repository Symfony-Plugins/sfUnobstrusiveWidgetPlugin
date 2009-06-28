/**
 * Unobstrusive double list widget using jQuery.
 * example : $('select.uo_widget_form_select_many_double_list').uoWidgetFormSelectManyDoubleList({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.widget('ui.uoWidgetFormSelectManyDoubleList', 
  {
 
    _init: function()
    {
      // prevent initialize twice
      if (this.element.hasClass('uo_widget_form_select_many_double_list_ON'))
      {
        return false;
      }
      
      this.element.removeClass('uo_widget_form_select_many_double_list');
      this.element.addClass('uo_widget_form_select_many_double_list_ON');

      this.id                 = this.element.attr('id');
      this.container          = $('<div class="ui-uoWidgetFormSelectManyDoubleList ui-helper-clearfix ui-widget"></div>').insertAfter(this.element);
      this.choices            = this.element.find('option').clone();
      
      var availableTemplate = '<div class="available"><div class="ui-widget-header ui-corner-top ui-helper-clearfix">' + this.options.labels.available + '</div><select multiple="multiple" class="ui-widget-content"></select></div>';
      var actionsTemplate   = '<div class="actions"></div>';
      var selectedTemplate  = '<div class="selected"><div class="ui-widget-header ui-corner-top ui-helper-clearfix">' + this.options.labels.selected + '</div><select multiple="multiple" class="ui-widget-content"></select></div>';
      
      if (!this.options.inverse)
      {
        this.availableContainer = $(availableTemplate).appendTo(this.container);
        this.actionsContainer   = $(actionsTemplate).appendTo(this.container);
        this.selectedContainer  = $(selectedTemplate).appendTo(this.container);
      }
      else
      {
        this.selectedContainer  = $(selectedTemplate).appendTo(this.container);
        this.actionsContainer   = $(actionsTemplate).appendTo(this.container);
        this.availableContainer = $(availableTemplate).appendTo(this.container);
      }

      this._appendChoices();
      this._appendActions();

      // register events
      this._registerActionsEvents(this.actionsContainer.find('li'));
      this._registerListEvents(this.container.find('select'));
      
      this._addItem();
    },
    
    destroy: function()
    {
      this.element.removeClass('uo_widget_form_select_many_double_list_ON');
      this.element.addClass('uo_widget_form_select_many_double_list');
      
      this.container.remove();
    },
    
    _appendActions: function()
    {
      var content;
      var actions = [];

      for (var actionName in this.options.actions)
      {
        content = '';
        if (this.options.actions[actionName].enable)
        {
          if (this.options.actions.image)
          {
            content = '<img alt="' + this.options.actions[actionName].label + '" src="' + this.options.actions[actionName].label + '" />';
          }
          else
          {
            content = this.options.actions[actionName].label;
          }
          
          actions.push('<li class="' + actionName + '"><a href="#">' + content + '</a></li>');
        }
      }
      
      if (actions.length > 0)
      {
        this.actionsContainer.append('<ul>' + actions.join ('') + '</ul>');
      }
    },
    
    _appendChoices: function()
    {
      this.availableContainer.find('select').append(this.choices);
    },
    
    _addItem: function()
    {
      this.selectedContainer.find('select')
        .append(this.availableContainer.find('option:selected'))
        .find('option:selected').removeAttr('selected');
      
      this._updateValues();
      return false;
    },
    
    _removeItem: function()
    {
      this.availableContainer.find('select')
        .append(this.selectedContainer.find('option:selected'))
        .find('option:selected').removeAttr('selected');
      
      this._updateValues();
      return false;
    },
    
    _addAllItems: function()
    {
      this.selectedContainer.find('select')
        .append(this.availableContainer.find('option'))
        .find('option:selected').removeAttr('selected');
      
      this._updateValues();
      return false;
    },
    
    _removeAllItems: function()
    {
      this.availableContainer.find('select')
        .append(this.selectedContainer.find('option'))
        .find('option:selected').removeAttr('selected');
      
      this._updateValues();
      return false;
    },
    
    _updateValues: function()
    {
      var that = this;
      
      this.element.find('option:selected').removeAttr('selected');
      
      this.selectedContainer.find('select option').each(function()
      {
        // select the corresponding option from the original element
        that.element.find('option[value="' + $(this).attr('value') + '"]').attr('selected','selected');
      });
    },
  
    _registerActionsEvents: function(elements)
    {
      var that = this;
      
      if (!this.options.inverse)
      {
        $('ul', this.actionsContainer)
          .find('li.add_all a').click(function() { that._addAllItems(); return false; } ).end()
          .find('li.add a').click(function() { that._addItem(); return false; }).end()
          .find('li.remove a').click(function() { that._removeItem(); return false; }).end()
          .find('li.remove_all a').click(function() { that._removeAllItems(); return false; }).end();
      }
      else
      {
        $('ul', this.actionsContainer)
          .find('li.remove_all a').click(function() { that._addAllItems(); return false; } ).end()
          .find('li.remove a').click(function() { that._addItem(); return false; }).end()
          .find('li.add a').click(function() { that._removeItem(); return false; }).end()
          .find('li.add_all a').click(function() { that._removeAllItems(); return false; }).end();
      }
    },
    
    _registerListEvents: function(elements)
    {
      var that = this;
      
      this.availableContainer.find('select')
        .dblclick(function() { that._addItem(); return false; })
        .keypress(function(event) { if (13 == event.keyCode){ that._addItem(); return false; } });
      this.selectedContainer.find('select')
        .dblclick(function() { that._removeItem(); return false; })
        .keypress(function(event) { if (13 == event.keyCode){ that._removeItem(); return false; } });
    }

  });
  
  $.extend($.ui.uoWidgetFormSelectManyDoubleList, {
    defaults: {
      labels: {
        available: 'Available',
        selected: 'Selected'
      },
      actions: {
        add_all: {
          enable: true,
          label: ' &gt;&gt; ',
          image: false
        },
        add: {
          enable: true,
          label: ' &gt; ',
          image: false
        },
        remove: {
          enable: true,
          label: ' &lt; ',
          image: false
        },
        remove_all: {
          enable: true,
          label: ' &lt;&lt; ',
          image: false
        }
      },
      inverse: false
    }
  });

})(jQuery);