/*
 * Unobstrusive treeview widget for jQuery UI
 * Base on the excellent work of Titkov Anton, ElSoft company (http://elsoft.tomsk.ru)
 *
 * Copyright (c) 2009 François béliveau (http://my-labz.com)
 * Dual licensed under the MIT (MIT-LICENSE.txt) and GPL (GPL-LICENSE.txt) licenses.
 *
 * Version 0.1.0
 */
;(function($)
{
  $.widget("uo.uoTreeview",
  {

    _init: function()
    {
      this._applyFix();

      this.dragging         = false;
      this.removingElements = [];
      var self              = this;
      var json              = this.options.json ? this.options.json : this._getJSON(this.element);
      var ul                = this._createBrunch(json).addClass(this.options.class_root);
      var id                = this.element.attr('id');
      
      if (id)
      {
        ul.attr('id', id);
      }
      
      $('ul', ul).hide();
      this.element.replaceWith(ul);
      this.element = ul;
      ul.data('tree', this);
      
      this._setNodeEvents(this.element);
      (this.options.expand && $('li', this.element).each(function() { if ($(this).is(self.options.expand || !self.options.expand_enabled)) self.expand(this); }));
      (this.options.hidden &&	this.element.hide());
    },
    
    _applyFix: function()
    {
      if (this.options.fix_ie_drag && $.ui.draggable && !$.ui.draggable.prototype.__mouseCapture)
      {
        $.ui.draggable.prototype.__mouseCapture = true;
        var d                                   = $.ui.draggable.prototype._mouseCapture;
        $.ui.draggable.prototype._mouseCapture  = function(event)
        {
          var result = d.call(this, event);
          (result && $.browser.msie && event.stopPropagation());
          return result;
        }
      }
    },
    
    after: function(json, node)
    {
      return this._change(json, node, 'after');
    },
    
    before: function(json, node)
    {
      return this._change(json, node, 'before');
    },
    
    append: function(json, node)
    {
      return this._change(json, node, 'append');
    },
    
    remove: function(node)
    {
      if (this.dragging && this.dragging[0] == this._getLI(node)[0])
      {
        this.removeDragging = true;
      }
      else this._remove(node);
    },
    
    title: function(node, title)
    {
      if (title)
      {
        this._setTitle(node, title);
      }
      else
      {
        this._getTitle(node);
      }
    },
    
    _remove: function(node)
    {
      var ul = this._getLI(node).parent('ul');
      if ($('>li', ul).length == 1)
      {
        ul.remove();
      }
      else
      {
        this._getLI(node).remove();
      }
    },
    
    getJSON: function(node)
    {
      if (node == undefined)
      {
        node = this.element;
      }
      else
      {
        node = this._getLI(node);
      }
      return this._getJSON(node);
    },
    
    getSelect: function()
    {
      var select = $('.' + this.options.class_selected, this.element);
      if (select.length)
      {
        return select;
      }
      else return null;
    },
    
    nodeName: function(node)
    {
      return (node.length ? node.attr('nodeName') : $(node).attr('nodeName')).toLowerCase();
    },

    select : function(node, multiselect)
    {
      return this._select(node, multiselect, null);
    },
    
    isNode : function(node)
    {
      var li = this._getLI(node);
      return (li.hasClass(this.options.class_node));
    },
    
    isList : function(node)
    {
      var li = this._getLI(node);
      return (li.hasClass(this.options.class_item));
    },
    
    isExpand : function(node)
    {
      var li = this._getLI(node);
      return (li.hasClass(this.options.class_expanded));
    },
    
    isCollapse : function(node)
    {
      var li = this._getLI(node);
      return (!li.hasClass(this.options.class_expanded));
    },
    
    expand : function(node)
    {
      var self = this;
      var li   = this._getLI(node);
      var ajax = /^\{url\:[\s\S]*?\}$/i.test($("li>span." + this.options.class_title, li).text());

      if (ajax)
      {
        var url = $("li>span." + this.options.class_title, li).text().replace(/^\{url\:([\s\S]*?)\}$/gim, '$1');
        $('ul>li', li).html("<div class=\"loading\">" + this.options.ajax_message_loading + "</div>");
        var child_ul = $('ul', li);
        $.ajax({
          url: url, 
          success: function(data)
          {
            child_ul.empty();
            $(data).each(function()
            {
              (this.localName && child_ul.append(self._createBrunch(self.getJSON(this))));
            });
            self._setNodeEvents(child_ul);
          }
        });
      }
      if (this.isList(li) || this.isExpand(li) || !this._trigger('expand', null, this._ui({}, li)))
      {
        return false;
      }

      var parents = li.parents().map(function()
      {
        if (self.nodeName(this) == 'li')
        {
          return this;
        }
      });

      if (!this.options.expand_multiple)
      {
        var expanded = $('>li.' + this.options.class_expanded + ':visible', this.element);
        expanded.each(function()
        {
          var el = this, col = true;
          parents.each(function()
          {
            if (this == el)
            {
              col = false;
            }
          });
          (col && self.collapse(this));
        });
      }

      if (!li.hasClass(this.options.class_expanded))
      {
        li.addClass(this.options.class_expanded);
        parents.map(function()
        {
          (!$(this).hasClass(self.options.class_expanded) && $(this).addClass(self.options.class_expanded) && self._show($('>ul', this)));
        });
        this._show($('>ul', li));
      }
      return true;
    },
    
    collapse : function(node)
    {
      var li = this._getLI(node);
      if (this.isList(li))
      {
        return false;
      }
      if (!this._trigger('collapse', null, this._ui({}, li)))
      {
        return false;
      }
      if (this.isExpand(li))
      {
        this._hide($('>ul', li));
        li.removeClass(this.options.class_expanded);
      }
      return true;
    },
    
    toggle : function(node)
    {
      var li = this._getLI(node);
      if (this.isList(li))
      {
        return false;
      }
      
      if (this.isExpand(li))
      {
        return this.collapse(li);
      }
      
      return this.expand(li);
    },
    
    _setNodeType : function(node, type)
    {
      var li          = this._getLI(node);
      var removeClass = type == 'node' ? this.options.class_item : this.options.class_node;
      var addClass    = type == 'node' ? this.options.class_node : this.options.class_item;
      li.removeClass(removeClass);
      if (!li.hasClass(addClass))
      {
        li.addClass(addClass);
      }
    },
    
    _change : function(json, node, changeMode)
    {
      var li = this._createBrunch(json);
      if (node == undefined)
      {
        node = this.element;
        changeMode = 'append';
      }
      else
      {
        node = this._getLI(node);
      }
      if (node.length == undefined)
      {
        node = $(node);
      }

      switch (changeMode)
      {
        case 'before':
          node.before(li);
          break;
        case 'after':
          node.after(li);
          break;
        case 'append': 
          var ul = this._getUL(node);
          if (!ul.length)
          {
            ul = this._getUL(node.append('<ul></ul>'));
          }
          ul.append(li);
          break;
        default: ;
      }
      
      this._setNodeEvents(li);
      return li;
    },
    
    _select : function(node, multiselect, event)
    {
      var span = this._getSPAN(node);
      if (!this._trigger('select', event, this._ui({}, node)))
      {
        return false;
      }
      if (!multiselect || !this.options.select_multiple)
      {
        $('.' + this.options.class_selected, this.element).removeClass(this.options.class_selected);
      }
      span.addClass(this.options.class_selected);
    },
    
    _show : function(el)
    {
      if ($.effects)
      {
        el.show(this.options.expand_effect, this.options.expand_effect_options, this.options.expand_speed, this.options.expand_callback);
      }
      else
      {
        el.show(this.options.expand_speed, this.options.expand_callback);
      }
    },
    
    _hide : function(el)
    {
      if ($.effects)
      {
        el.hide(this.options.collapse_effect, this.options.collapse_effect_options, this.options.collapse_speed, this.options.collapse_callback);
      }
      else
      {
        el.hide(this.options.collapse_speed, this.options.collapse_callback);
      }
    },
    
    _getUL : function(node)
    {
      node = node.length ? node : $(node);
      if (this.nodeName(node) == 'span')
      {
        return $('>ul', node.parent());
      }
      else if (this.nodeName(node) == 'li')
      {
        return $('>ul', node);
      }
      
      return node;
    },
    
    _getLI : function(node)
    {
      node = node.length ? node : $(node);
      if (this.nodeName(node) == 'span')
      {
        return node.parent();
      }
      else if (this.nodeName(node) == 'ul')
      {
        return node.parent();
      }
      
      return node;
    },
    
    _getSPAN : function(node)
    {
      node = node.length ? node : $(node);
      if (this.nodeName(node) == 'li')
      {
        return $('>span.' + this.options.class_title + ':eq(0)', node);
      }
      else if (this.nodeName(node) == 'ul')
      {
        return $('span.' + this.options.class_title + ':eq(0)', node.parent());
      }
      
      return node;
    },
    
    _ui : function(ui, el)
    {
      ui = ui ? ui : {};
      el = el.length == undefined ? el : $(el);
      return {
        draggable : ui.draggable ? ui.draggable : el,
        droppable : ui.draggable ? el : null,
        helper : ui.helper,
        position : ui.position,
        offset : ui.offset,
        item : ui.draggable ? null : el,
        overState : ui.overState,
        target : this,
        sender : ui.draggable ? ui.draggable.data('tree') : null
      };
    },
    
    _createBrunch : function(json)
    {
      if (typeof(json) == 'string')
      {
        json = this._evalJSON(json);
      }
      var brunch = $(this._createLI(json));
      $('>ul', $('.' + this.options.class_expand, brunch)).show();
      return brunch;
    },
    
    // select all child elements of parent by selector in the tree. Need for mulpiple draggable and droppable rules
    _getAllElements : function(parent, selector)
    {
      parent = parent.length ? parent[0] : parent;
      return elements = $(selector, this.element).map(function()
      {
        if ($(this).is('li'))
        {
          var i = $(this).parents().add(this).map(function(){ if (this == parent) return this; });
          if (i.length)
          {
            return this;
          }
        }
      });
    },
    
    // select all child elements of parent by options[n].element in the tree and exclude from result elements for other options. Need for mulpiple draggable and droppable rules
    _getElements : function(parent, options, n)
    {
      var elements = this._getAllElements(parent, options[n].element);
      for (var i = 0; i < options.length; i++)
      {
        if (i != n && options[i].element != '*')
        {
          var excludeElements = this._getAllElements(parent, options[i].element);
          elements = elements.not(excludeElements);
        }
      }
      return elements;
    },
    
    _createDDNodeOptions : function(options, type)
    {
      var self        = this;
      var result      = options;
      var createEvent = function(tree, eventName, treeEvent)
      {
        return function(event, ui)
        {
          if (!treeEvent && !$(this).trigger('_tree_' + event, ui))
          {
            return false;
          }
          
          if (event.type == 'dragstart')
          {
            tree.dragging = tree._getLI(this);
          }
          else if (event.type == 'dragstop' && tree.removeDragging)
          {
            tree._remove(tree.dragging);
            tree.dragging = tree.removeDragging = false;
          }
          var _ui = tree._ui(ui, this);
          return tree._trigger(eventName, event, _ui);
        }
      }
      var ddEvents = (type == 'drag' ? this.options.drag_events : (type == 'drop' ? this.options.drop_events : []));
      
      for (var i = 0; i < ddEvents.length; i++)
      {
        var event = ddEvents[i];
        if (result[event] == undefined && this.options[event])
        {
          result[event] = createEvent(this, event, true);
        }
        else
        {
          result['_tree_' + event] = result[event];
          result[event]            = createEvent(this, event);
        }
      }

      if (type == 'drop')
      {
        var createAccept = function(tree, accept)
        {
          if (accept == undefined)
          {
            accept = '*';
          }
          var _accept = $.isFunction(accept) ? accept : function(d)
          {
            return d.is(accept);
          };
          
          return function(el)
          {
            var el_tree   = $(el).data('tree');
            var from_self = el_tree ? el_tree.element[0] == tree.element[0] : false;
            
            if (from_self && !tree.options.drop_accept_from_self)
            {
              return false;
            }
            else if (!from_self && !tree.options.drop_accept_from)
            {
              return false;
            }
            else if (!from_self && tree.options.drop_accept_from && tree.options.drop_accept_from != '*')
            {
              var child  = false;
              var parent = $(tree.options.drop_accept_from);
              if (!parent.length)
              {
                return false;
              }
              $(el).parents().map(function()
              {
                if (parent[0] == this)
                {
                  child = true;
                }
              });
              if (!child)
              {
                return false;
              }
            }
            return _accept(el);
          }
        }
        result.accept = createAccept(this, result.accept);
      }
      else if (type == 'drag')
      {
        result.handle = result.handle ? result.handle : '>span.' + this.options.class_title;
      }
      return result;
    },
    
    // bind events and make droppable and draggable elements in brunch el
    _setNodeEvents : function(el)
    {
      var self = this;

      if (this.options.drop_enabled)
      {
        var droppable = this.options.droppable.length != undefined ? this.options.droppable : [this.options.droppable];
        for (var i = 0; i < droppable.length; i++)
        {
          var elements = this._getElements(el, droppable, i);
          if (elements.length)
          {
            var options = this._createDDNodeOptions(droppable[i], 'drop');
            $('>span.' + this.options.class_title, elements).droppable(options).data('tree' , this);
          }
        }
      }

      if (this.options.drag_enabled)
      {
        var draggable = this.options.draggable.length != undefined ? this.options.draggable : [this.options.draggable];
        for (var i = 0; i < draggable.length; i++)
        {
          var elements = this._getElements(el, draggable, i);
          if (elements.length)
          {
            var options = this._createDDNodeOptions(draggable[i], 'drag');
            elements.draggable(options).data('tree' , this);
          }
        }
      }

      var span = $('span.' + this.options.class_title, el);
      
      // !!! not sure about that
      /*var events      = $(this.options.events).not(this.options.select_event, this.options.expand_event, this.options.collapse_event);
      var createEvent = function(eventName)
      {
        return function(event)
        {
          self._trigger(eventName, event, self._ui({}, self._getLI(this)));
        }
      }

      $(events).each(function()
      {
        span.bind(this, createEvent(this));
      });*/

      if (this.options.expand_enabled)
      {
        if (this.options.expand_event && this.options.expand_event == this.options.collapse_event)
        {
          span.bind(this.options.expand_event, function(event)
          {
            if (self.isCollapse(this))
            {
              return self.expand(this);
            }
            else if (self.isExpand(this))
            {
              return self.collapse(this);
            }
          })
        }
        else
        {
          (this.options.expand_event && span.bind(this.options.expand_event, function(event) { return self.expand(this); }));
          (this.options.collapse_event && span.bind(this.options.collapse_event, function(event) { return self.collapse(this); }));
        }
      }
      
      if (this.options.select_enabled)
      {
        (this.options.select_event && span.bind(this.options.select_event, function(event) { return self._select(this, self.options.select_multiple_key ? event[self.options.select_multiple_key] : true, event); }));
        span.disableSelection();
        (this.options.createbrunch && this._trigger('createbrunch', null, this._ui({}, this._getLI(el))));
      }
    },
    
    _evalJSON: function(json)
    {
      return eval('(' + json + ')');
    },
    
    _getTitle : function(node)
    {
      var title = $('>span.' + this.options.class_title, node);
      var html  = '';

      if (title.length)
      {
        html = title.html().replace(new RegExp('/<span class="?' + this.options.class_title_image + '"?[^>]*>[\s\S]*?<\/span>/gi'), '');
      }
      else
      {
        node = node.length ? node : $(node);
        html = node.html().replace(/<ul[^>]*>[\s\S]*<\/ul>/gi, '').replace(/\s*style="[^"]*"/gi, '');
      }

      return $.trim(html.replace(/\n/g, '').replace(/<a[^>]*>/gi, '').replace(/<\/a>/gi, ''));
    },
    
    _setTitle: function(node, title)
    {
      this._getSPAN(node).html(title);
    },
    
    _getJSON: function(node, its_child)
    {
      var json = '';
      var nodeName = this.nodeName(node);
      if (nodeName == 'li')
      {
        json = '{';
        var title = this._getTitle(node);
        json += "'title' : '" + title + "'";
        var id = node.attr('id');
        if (id) json += ", 'id' : '" + id + "'";
        var className = $.trim(node.attr('className').replace(/ui-[^\s]*/gim, ''));
        if (className) json += ", 'className' : '" + className + "'";
        var img = $('>span.' + this.options.class_title +'>span.' + this.options.class_title_image + '>img', node);
        if (img.length) json += ", 'img' : '" + img.attr('src') + "'";
        var url = $('>span.' + this.options.class_title + '>a:eq(0)', node);
        if (!url.length) url = $('>a:eq(0)', node);
        if (url.length) json += ", 'url' : '" + url.attr('href') + "'";
        var expand = node.hasClass('.' + this.options.class_expanded);
        json += ", 'expand' : '" + expand + "'";
        
      }
      else if (nodeName == 'ul')
      {
        json += its_child ? ", 'children' : [" : "[";
      }

      var child = node.children(nodeName == 'ul' ? 'li' : (nodeName == 'li' ? 'ul' : 'xyz'));
      if (child.length > 0)
      {
        for (var i = 0; i < child.length; )
        {
          json += this._getJSON($(child.get(i)), true);
          if (++i != child.length)
          { 
            json += ',';
          }
        }
      }
      json += (nodeName == 'ul' ? ']' : (nodeName == 'li' ? '}' : ''));
      return json;
    },

    _createLI: function(obj)
    {
      if (obj.length)
      {
        return this._createUL(obj);
      }
      
      var html = '<li ';
      if (obj.id)
      {
        html += 'id="' + obj.id + '"';
      }
      html += 'class="';
      html += obj.children ? this.options.class_node : this.options.class_item;
      html += obj.expand ? ' ' + this.options.class_expand : '';
      if (obj.className)
      {
        html += ' ' + obj.className;
      }
      html += '"><span class="' + this.options.class_expand_control + '"/><span class="' + this.options.class_title +'"><span class="' + this.options.class_title_image + '">';
      if (obj.img)
      {
        html += '<img href="' + obj.img + '"/>';
      }
      html += '</span>';
      if (obj.title)
      {
        if (obj.url)
        {
          html += '<a href="' + obj.url + '">' + obj.title + '</a>';
        }
        else
        {
          html += obj.title;
        }
      }
      html += '</span>';
      if (obj.children)
      {
        html += this._createUL(obj.children);
      }
      html += '</li>';
      return html;
    },
    
    _createUL: function(obj)
    {
      var html = '<ul>';
      if (obj.length != undefined)
      {
        for (var i = 0; i < obj.length; i++)
        {
          html += this._createLI(obj[i]);
        }
      }
      else
      {
        html += this._createLI(obj);
      }
      html += '</ul>';
      return html;
    }
    
  });

  $.extend($.uo.uoTreeview,
  {
    version: '0.1.0',
    defaults:
    {
      // classname
      class_root: 'uo-treeview ui-widget',
      class_node: 'uo-treeview-node',
      class_item: 'uo-treeview-list',
      class_title: 'uo-treeview-title',
      class_title_image: 'uo-treeview-title-img',
      class_selected: 'ui-state-highlight',
      class_expanded: 'uo-treeview-expanded',
      class_expand: 'uo-treeview-expand',
      class_expand_control: 'uo-treeview-expand-control',
      // ui-icon-folder-collapsed, ui-icon-folder-open, ui-icon-document
      // expand / collapse
      expand_enabled: true,
      expand: false, // if not expand enabled all node will be expand
      expand_multiple: true,
      expand_event: 'dblclick',
      expand_effect: 'blind',
      expand_effect_options: {},
      expand_speed: 500,
      expand_callback: false,
      collapse_event: 'dblclick',
      collapse_effect : 'blind',
      collapse_effect_options : {},
      collapse_speed : 500,
      collapse_callback : false,
      // select
      select_enabled: true,
      select_event: 'click',
      select_multiple: false,
      select_multiple_key: 'ctrlKey',
      //fix
      fix_ie_drag: true,
      // events
      events: ['click', 'dblclick', 'mousedown', 'mouseup', 'mouseenter', 'mouseleave'],
      // ajax
      ajax_message_loading: 'Loading...',
      json : null,
      // drag options: object or array of object
      drag_enabled: true,
      drag_events: ['start', 'drag', 'stop'],
      draggable:
      {
        element: '*',
        handle: '>span.uo-treeview-title',
        helper: 'clone',
        revert: 'invalid',
        distance: 2
      },
      // drop options: object or array of object
      drop_enabled: true,
      drop_events: ['activate', 'deactivate', 'stop', 'over', 'out', 'drop', 'overtop', 'overbottom', 'overright', 'overleft', 'overcenter', 'outtop', 'outbottom', 'outright', 'outleft', 'outcenter'],
      drop_accept_from_self: true,
      drop_accept_from: false,
      droppable:
      [
        {
          element: 'li.uo-treeview-node',
          tolerance: 'around',
          aroundTop: '25%',
          aroundBottom: '25%',
          aroundLeft: 0,
          aroundRight: 0
        },
        {
          element : 'li.uo-treeview-list',
          tolerance : 'around',
          aroundTop : '50%',
          aroundBottom : '50%',
          aroundLeft : 0,
          aroundRight : 0
        }
      ]
    }
  });

})(jQuery);