/**
 * Unobstrusive many value input widget using jQuery.
 * example : $(':text.uo_widget_form_input_text_many_ajax_search').uoWidgetFormInputTextManyAjaxSearch({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.widget('ui.uoWidgetFormInputTextManyAjaxSearch', 
  {
 
    _init: function()
    {
      // prevent initialize twice
      if (this.element.hasClass('uo_widget_form_input_text_many_ajax_search_ON'))
      {
        return false;
      }
      
      this.element.removeClass('uo_widget_form_input_text_many_ajax_search');
      this.element.addClass('uo_widget_form_input_text_many_ajax_search_ON');
    
      //this.element.hide();
      this.id            = this.element.attr('id');
      this.container     = $('<div class="ui-uoWidgetFormInputTextManyAjaxSearch ui-helper-clearfix ui-widget"></div>').insertAfter(this.element);
      this.header        = $('<div class="actions ui-widget-header ui-helper-clearfix"><input type="text" class="search ui-widget-content ui-corner-all"/></div>').appendTo(this.container);
      this.itemContainer = $('<ul></ul>').appendTo(this.container);
    
      this._appendData();
      if (this.options.auto_load)
      {
        this._autoLoad();
      }
    
      // register events
      this._registerSearchEvents(this.header.find(':text'));

      // enable drag n drop
      if (this.options.draggable_items)
      {
        var that = this;
        $(".ui-uoWidgetFormInputTextManyAjaxSearch ul").addClass('sortable').sortable({
          placeholder:     'ui-state-highlight',
          tolerance:       'pointer',
          stop:          function (event, ui){
            that._rebuildValue();
        }
        });
      }
    },
    
    destroy: function()
    {
      this.element.removeClass('uo_widget_form_input_text_many_ajax_search_ON');
      this.element.addClass('uo_widget_form_input_text_many_ajax_search');
      
      this.container.remove();
    },
    
    _appendData: function()
    {
      if (!this.options.template)
      {
        return false;
      }
    
      for (var value in this.options.data)
      {
        this._addItem(this._getItemFromData(this.options.data[value]), value, false);
      }
    },
    
    _autoLoad: function()
    {
      var widgetValue    = this.element.val();
      var widgetValues   = widgetValue.split(this.options.separator);
      for(var i=0; i<widgetValues.length; i++)
      {
        if (0 == $(':hidden[value="'+widgetValues[i]+'"]', this.itemContainer).length)
        {
          this._doSearch(widgetValues[i], false);
        }
      }
    },
    
    _doSearch: function(value, addValue)
    {
      if (!this.options.url || '' == value)
      {
        return false;
      }
      
      var that   = this;
      var search = {}
      var callback;
      var type;

      if (this.options.json)
      {
        if (!this.options.template)
        {
          return false;
        }
        callback = function(data, textStatus){ that._searchAsJsonHandler(data, textStatus, value, addValue); };
        type     = 'json';
      }
      else
      {
        callback = function(data, textStatus){ that._searchAsHtmlHandler(data, textStatus, value, addValue); };
        type     = 'html';
      }
      
      search[this.options.post_name] = value;
      jQuery.post(this.options.url, search, callback, type);
    },
    
    _searchAsJsonHandler: function(data, textStatus, value, addValue)
    {
      if ('' == data)
      {
        return false;
      }
    
      this._addItem(this._getItemFromData(data), value, addValue);
    },
    
    _searchAsHtmlHandler: function(data, textStatus, value, addValue)
    {
      if ('' == data)
      {
        return false;
      }

      this._addItem(data, value, addValue);
    },
    
    _getItemFromData: function(data)
    {
      var item = this.options.template;
      var itemRegExp;
      
      for (var itemVarName in data)
      {
        itemRegExp = new RegExp(itemVarName, "g");
        item       = item.replace(itemRegExp, data[itemVarName])
      }
      
      return item;
    },
    
    _addItem: function(item, value, addValue)
    {
      var that = this;

      that.itemContainer.append('<li><input type="hidden" value="'+value+'" />'+item+'</li>');

      $('li:last', that.itemContainer).find(that.options.remove_item_selector).click(function(){
        $(this).parents('li:first').fadeOut('slow', function(){ that._removeItem(this); });
        return false;
      });
      
      if (!that.autoloading && addValue)
      {
        var widgetValue = that.element.val();
        if ('' != widgetValue)
        {
          widgetValue += that.options.separator;
        }
        
        that.element.val(widgetValue+value);
      }
    },

    /** Rebuild the input value (hidden from user) with the current
     *  selected elements
     */
    _rebuildValue: function ()
    {
      var newValue = new Array();
      this.itemContainer.find("li input[type=hidden]").each(function (i,e){
        newValue[newValue.length] = $(e).val();
      });
      this.element.val(newValue.join(','));
    },

    _removeItem: function(element)
    {
      $(element).remove();
      this._rebuildValue();
    },
    
    _isValueExists: function(value)
    {
      // check if value allready sets in widget
      var widgetValue    = this.element.val();
      var widgetValues   = widgetValue.split(this.options.separator);
      for(var i=0; i<widgetValues.length; i++)
      {
        if (widgetValues[i] == value)
        {
          return true;
        }
      }
      
      return false;
    },
  
    _registerSearchEvents: function(elements)
    {
      var that = this;
      elements.keypress(function(event)
      {
        if (13 == event.keyCode)
        {
          var element = $(this);
          var value   = element.val();
          
          if ('' != value && !that._isValueExists(value))
          {
            // launch ajax request
            that._doSearch(value, true);
            element.val('');
          }
          
          return false; // desactivate form submission
        }
      });
    }

  });
  
  $.extend($.ui.uoWidgetFormInputTextManyAjaxSearch, {
    defaults: {
      template: false,
      url: false,
      json: true,
      draggable_items: false,
      separator: ',',
      post_name: 'search',
      auto_load: true,
      remove_item_selector: '.remove',
      data: {}
    }
  });

})(jQuery);