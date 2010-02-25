/**
 * jQuery UI autocomplete.
 *
 * @licence     Dual licensed under the MIT (MIT-LICENSE.txt) and GPL (GPL-LICENSE.txt) licenses
 * @author      François Béliveau <francois.beliveau@my-labz.com>
 * @package     unobstrusive.widget
 * @version     0.10
 * @depends :
 *              - ui.core.js (version 1.7.2)
 */
;(function($)
{

  $.widget('uo.uoAutocomplete',
  {

    _init: function()
    {
      this.id      = this.element.attr('id');
      this.timeout = 0;
      this.cache   = {};
      this.keys    = {
        'backspace': 8,
        'tab':       9,
        'return':    13,
        'esc':       27,
        'left':      37,
        'up':        38,
        'right':     39,
        'down':      40,
        'coma':      188
      };

      this.dropdown = $('<div class="token-input-dropdown ui-widget-content ui-corner-bottom"></div>')
        .insertAfter(this.element)
        .hide();
      this.dropdownSelectedItem = null;

      this.element.addClass('ui-widget ui-widget-content ui-corner-all')

      this._register();
    },

    destroy: function()
    {
      this.dropdown.remove();
    },

    _register: function()
    {
      var self = this;

      this.element
        .focus(function(event){ self._onFocus(event);} )
        .blur(function(event){ self._onBlur(event);} )
        .keydown(function(event){ self._onKeydown(event);} );
    },

    _onFocus: function(event)
    {
      this._showDropdownHint();
    },

    _onBlur: function(event)
    {
      this._hideDropdown();
    },

    _onKeydown: function(event)
    {
      var
        keyCode = event.keyCode,
        self    = this;

      switch (keyCode)
      {
        case this.keys.up:
        case this.keys.left:
        case this.keys.right:
        case this.keys.down:
          var item = null;
          if(keyCode ==  this.keys.down || keyCode == this.keys.right)
          {
            item = $(this.dropdownSelectedItem).next();
          }
          else
          {
            item = $(this.dropdownSelectedItem).prev();
          }

          if(item.length)
          {
            this._selectDropdownItem(item);
          }
          return false;

        case this.keys.backspace:
          if (this.element.val().length == 1)
          {
            this._hideDropdown();
          }
          else
          {
            // set a timeout just long enough to let this function finish.
            setTimeout(function(){ self._search(false); }, 5);
          }
          break;

        case this.keys.tab:
        case this.keys.return:
        case this.keys.coma:
          break;

        case this.keys.esc:
          break;

        default:
          if (this._isPrintableChar(keyCode))
          {
            // set a timeout just long enough to let this function finish.
            setTimeout(function(){ self._search(false); }, 5);
          }
          break;
      }
    },

    _isPrintableChar: function (keycode)
    {
      if (
        (keycode >= 48 && keycode <= 90)      // 0-1a-z
        || (keycode >= 96 && keycode <= 111)  // numpad 0-9 + - / * .
        || (keycode >= 186 && keycode <= 192) // ; = , - . / ^
        || (keycode >= 219 && keycode <= 222) // ( \ ) '
      )
      {
        return true;
      }
      else
      {
        return false;
      }
    },

    _search: function (immediate)
    {
      var query = this.element.val().toLowerCase();
      if (query && query.length)
      {
        if (query.length >= this.options.search_min_chars)
        {
          this._showDropdownSearching();
          if (immediate)
          {
            this._doSearch(query);
          }
          else
          {
            clearTimeout(this.timeout);
            var self    = this;
            this.timeout = setTimeout(function(){ self._doSearch(query); }, self.options.search_delay);
          }
        }
        else
        {
          this._hideDropdown();
        }
      }
    },

    _doSearch: function (query)
    {
      var
        self = this,
        params = {};
      params[this.options.query_param] = query;

      $.ajax({
        type: 'POST',
        url: this.options.query_url,
        dataType: this.options.content_type,
        cache: this.options.cache_ajax,
        success: function(data, textStatus){ self._parseSearchResponse(query, data); }
      });
    },

    _parseSearchResponse: function(query, data)
    {
      if ($.isFunction(this.options.on_search_event))
      {
        data = this.options.on_search_event.call(this, query, data);
      }

      this._populateDropdown(query, data);
      this._selectDropdownItem($('li:first', this.dropdown));
    },

    _highlightTerm: function (queries, content)
    {
      var result  = '';
      for (var i=0; i < queries.length; i++)
      {
        content.replace(new RegExp('(?![^&;]+;)(?!<[^<>]*)(' + queries[i] + ')(?![^<>]*>)(?![^&;]+;)', 'gi'), '<span class="ui-state-highlight">$1</span>');
      }
      return content;
    },

    _getElementFromEvent: function (event, elementType)
    {
      var
        target  = $(event.target),
        element = null;

      if (target.is(elementType))
      {
        element = target;
      }
      else if (target.parent(elementType).length)
      {
        element = target.parent(elementType + ':first');
      }

      return element;
    },

    _hideDropdown: function ()
    {
      this.dropdown
        .hide()
        .empty();
      this.dropdownSelectedItem = null;
    },

    _showDropdownHint: function ()
    {
      if (this.options.text_hint)
      {
        this._showDropdown('<p>' + this.options.text_hint + '</p>');
      }
    },

    _showDropdownSearching: function ()
    {
      if (this.options.text_searching)
      {
        this._showDropdown('<p>' + this.options.text_searching + '</p>');
      }
    },

    _showDropdownNoResults: function ()
    {
      if (this.options.text_no_results)
      {
        this._showDropdown('<p>' + this.options.text_no_results + '</p>');
      }
    },

    _showDropdown: function (content)
    {
      this.dropdown
        .html(content)
        .show();
    },

    _populateDropdown: function (query, data)
    {
      this.dropdown.empty();

      var
        self       = this,
        parsedData = [];
      switch (this.options.content_type)
      {
        case 'text':
          parsedData = this._parseTextResult(query, data);
          break;
        case 'json':
          parsedData = this._parseJsonResult(query, data);
          break;
      }

      if (parsedData.length)
      {
        var content = this._highlightTerm(query.split(' '), parsedData.join('</li><li class="ui-state-default">'));
        this._showDropdown('<ul class="ui-corner-bottom"><li class="ui-state-default">' + content + '</li></ul>');

        $('ul:first', this.dropdown)
          .mouseover(function (event){ self._selectDropdownItem(self._getElementFromEvent(event, 'li')); })
          .click(function (event)
          {
            self.element.val(self._getElementFromEvent(event, 'li').text());
            self._hideDropdown();
            return false;
          })
          .mousedown(function (event){ return false; })
          .hide()
          .slideDown('fast');
      }
      else
      {
        this._showDropdownNoResults();
      }
    },

    _parseJsonResult: function (query, data)
    {
      var list = [];
      for (var i in data)
      {
        if (data.hasOwnProperty(i))
        {
          var content = this.options.content_json_template;
          for (var property in data[i])
          {
            content = content.replace(new RegExp('%' + property + '%', 'g'), data[i][property]);
          }

          list.push(content);
        }
      }

      return list;
    },

    _parseTextResult: function (query, data)
    {
      var
        list = [],
        rows = data.split(this.options.content_text_line_separator);
      for (var i=0; i < rows.length; i++)
      {
        var row = $.trim(rows[i]);
        if (row)
        {
          var
            cells   = row.split(this.options.content_text_cell_separator),
            content = this.options.content_text_template;
          for (var y=0; y < cells.length; y++)
          {
            content = content.replace(new RegExp('%text_' + y + '%', 'g'), cells[y]);
          }

          list.push(content);
        }
      }

      return list;
    },

    // Highlight an item in the results dropdown
    _selectDropdownItem: function (item)
    {
      if (item)
      {
        if (this.dropdownSelectedItem)
        {
          this._deSelectDropdownItem($(this.dropdownSelectedItem));
        }

        item.addClass('ui-state-hover');
        this.dropdownSelectedItem = item.get(0);
      }
    },

    // Remove highlighting from an item in the results dropdown
    _deSelectDropdownItem: function (item)
    {
      item.removeClass('ui-state-hover');
      this.dropdownSelectedItem = null;
    },

  });

  $.extend($.uo.uoAutocomplete, {
    version: '0.10',
    defaults: {
      multiple: false,

      container: null, // if empty, create one automaticaly

      cache_ajax: true,

      content_type: 'text', // text or json
      content_json_value: 'id', // the JSON data to submit
      content_json_template: '%id% : %text%',
      content_text_template: '%text_0%',
      content_text_line_separator: "\n",
      content_text_cell_separator: "|",

      query_url: false,
      query_param: 'search_for',

      text_hint: 'Type in a search term',
      text_no_results: 'No results',
      text_searching: 'Searching...',

      search_delay: 300,
      search_min_chars: 1,
      search_token_limit: null,
      search_url: null,

      on_search_event: null,
      on_result_event: null,
      on_result_select_event: null,
    }
  });

})(jQuery);
