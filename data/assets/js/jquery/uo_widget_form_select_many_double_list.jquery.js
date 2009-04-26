/**
 * Unobstrusive double list widget using jQuery.
 * example : $('select.uo_widget_form_select_many_double_list').uoWidgetFormSelectManyDoubleList({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
(function($) {

  $.fn.uoWidgetFormSelectManyDoubleList = function(customConfiguration)
  {
    // default configuration
    var configuration = {
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
      }
    };

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
        if ($widget.hasClass('uo_widget_form_select_many_double_list_ON'))
        {
          return $widget;
        }

        $widget.removeClass('uo_widget_form_select_many_double_list');
        $widget.addClass('uo_widget_form_select_many_double_list_ON');

        createContainer();
        createActions();
        doAdd();
      }

      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = {};
        return $.extend(true, configuration, result);
      }

      /**
       * Create container
       */
      function createContainer()
      {
        var container = $('<div class="uo_widget_form_select_many_double_list_container"><div class="actions_container"></div></div>');
        var options   = $widget.find('option').clone();

        $('div.actions_container', container)
          .before('<select class="left" multiple="multiple"></select>')
          .after('<select class="right" multiple="multiple"></select>');
        $widget.after(container);

        $widget.next().find('select.left').append(options);

        $widget.next().find('select.left').dblclick(doAdd);
        $widget.next().find('select.right').dblclick(doRemove);
      }

      /**
       * Create actions
       */
      function createActions()
      {
        var actions = $('<ul class="actions"></ul>');
        var config  = getConfiguration();
        for (var actionName in config.actions)
        {
          if (config.actions[actionName].enable)
          {
            if (config.actions[actionName].image)
            {
              actions.append('<li class="'+actionName+'"><input type="image" src="'+config.actions[actionName].image+'" value="'+config.actions[actionName].label+'" /></li>');
            }
            else
            {
              actions.append('<li class="'+actionName+'"><input type="button" value="'+config.actions[actionName].label+'" /></li>');
            }
          }
        }

        $('div.actions_container', $widget.next())
          .append(actions)
          .find('li.add_all input').click(doAddAll).end()
          .find('li.add input').click(doAdd).end()
          .find('li.remove input').click(doRemove).end()
          .find('li.remove_all input').click(doRemoveAll).end();
      }

      function doAddAll()
      {
        var options = $widget.next().find('select.left option');
        $widget.next().find('select.right').append(options).find('option:selected').removeAttr('selected');
        updateWidgetValues();
      }

      function doAdd()
      {
        var options = $widget.next().find('select.left option:selected');
        $widget.next().find('select.right').append(options).find('option:selected').removeAttr('selected');
        updateWidgetValues();
      }

      function doRemove()
      {
        var options = $widget.next().find('select.right option:selected');
        $widget.next().find('select.left').append(options).find('option:selected').removeAttr('selected');
        updateWidgetValues();
      }

      function doRemoveAll()
      {
        var options = $widget.next().find('select.right option');
        $widget.next().find('select.left').append(options).find('option:selected').removeAttr('selected');
        updateWidgetValues();
      }

      function updateWidgetValues()
      {
        $widget.find('option:selected').removeAttr('selected');
        $widget.next().find('select.right').find('option').each(function()
        {
          // select the corresponding option from the original element
          $widget.find('option[value="' + $(this).attr('value') + '"]').attr('selected','selected');
        });
      }

      init();
    });

  };

})(jQuery);