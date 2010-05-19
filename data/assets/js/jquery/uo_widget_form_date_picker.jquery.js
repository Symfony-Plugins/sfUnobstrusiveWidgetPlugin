/**
 * Unobstrusive date picker widget using jQuery.
 * example : $('select.uo_widget_form_date_picker, :text.uo_widget_form_date_picker').uoWidgetFormDatePicker({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.fn.uoWidgetFormDatePicker = function(customConfiguration)
  {
    // default configuration
    var configuration = {
      showOn:          'button',
      buttonImageOnly: true,
      buttonImage:     '/sfUnobstrusiveWidgetPlugin/images/default/uo_widget_form_date_picker/calendar.gif',
      changeFirstDay:  false
    };

    // merge default and custom configuration
    $.extend(true, configuration, customConfiguration);

    return this.each(function(index)
    {
      var $widget       = $(this);
      var $rangeWidget  = false;
      var $baseId       = '';
      var $baseRangeId  = '';
      var $widgets      = {};
      var $rangeWidgets = {};

      /**
       * Initialize widget
       */
      function init()
      {
        // prevent initialize twice
        if ($widget.hasClass('uo_widget_form_date_picker_ON'))
        {
          return $widget;
        }

        var id   = $widget.attr('id') || '';
        $baseId  =  id.substr(0, id.lastIndexOf('_'));
        $widgets = initWidgets($widget, $baseId);
        initRange();

        if ($rangeWidget.length > 0)
        {
          for (var property in $widgets)
          {
            $widgets[property].change(dateUpdate);
          }

          for (var property in $rangeWidgets)
          {
            $rangeWidgets[property].change(dateUpdate);
          }
        }

        $widget.removeClass('uo_widget_form_date_picker');
        $widget.addClass('uo_widget_form_date_picker_ON');
        $('#' + $baseId).datepicker(getConfiguration());
        if ($widget.attr('disabled'))
        {
          $('#' + $baseId.replace('_from', '_to')).datepicker('disable');
        }

        if ($rangeWidget)
        {
          $rangeWidget.removeClass('uo_widget_form_date_picker');
          $rangeWidget.addClass('uo_widget_form_date_picker_ON');
          $('#' + $baseId.replace('_from', '_to')).datepicker(getConfiguration());
          if ($rangeWidget.attr('disabled'))
          {
            $('#' + $baseId.replace('_from', '_to')).datepicker('disable');
          }
        }
      }

      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = {};

        if (undefined == result.beforeShow)
        {
          result.beforeShow = readLinked;
        }

        if (undefined == result.onSelect)
        {
          result.onSelect = updateLinked;
        }

        return $.extend(true, configuration, result);
      }

      /**
       * Initialize range
       */
      function initRange()
      {
        var id   = $widget.attr('id') || '';
        var name = $widget.attr('name') || '';

        if (name.lastIndexOf('[from]') > 0)
        {
          $rangeWidget = $('#' + id.replace('_from', '_to'));
          if ($rangeWidget.length < 1)
          {
            $rangeWidget = false;
          }
          else
          {
            $baseRangeId  = $baseId.replace('_from', '_to');
            $rangeWidgets = initWidgets($rangeWidget, $baseRangeId);
          }
        }
      }

      /**
       * Initialize widgets
       */
      function initWidgets(elm, baseId)
      {
        var result   = {};
        var id       = elm.attr('id') || '';
        if (id == baseId)
        {
          return result;
        }

        var day   = $('#' + baseId + '_day');
        var month = $('#' + baseId + '_month');
        var year  = $('#' + baseId + '_year');

        if (day.length > 0 && month.length > 0 && year.length > 0)
        {
          result.day   = day;
          result.month = month;
          result.year  = year;

          elm.after('<input type="hidden" id="' + baseId  +'" />');
          result.month.change(checkLinkedDays);
          result.year.change(checkLinkedDays);
        }

        return result;
      }

      /**
       * Prepare to show a date picker linked to 3 controls
       * beforeshow
       */
      function readLinked(input, inst)
      {
        var objects = false;
        var minDate = null;
        var maxDate = null;
        if ($(this).attr('id') == $baseId)
        {
          objects = $widgets;
        }
        else if ($(this).attr('id') == $baseRangeId)
        {
          objects = $rangeWidgets;
        }
        
        if (objects)
        {
          var
              currentDay   = objects.day.val(),
              currentMonth = objects.month.val(),
              currentYear  = objects.year.val();

            if (!currentDay)
            {
              currentDay = $('option[value!=""]:first', '#jst_registration_born_at_day').attr('value');
            }
            
            if (!currentMonth)
            {
              currentMonth = $('option[value!=""]:first', '#jst_registration_born_at_month').attr('value');
            }
            
            if (!currentYear)
            {
              currentYear = $('option[value!=""]:first', '#jst_registration_born_at_year').attr('value');
            }

          if (currentDay && currentMonth && currentYear)
          {
            $(input).datepicker('setDate', new Date(currentYear, currentMonth -1, currentDay));
          }

          if ($('option', objects.year).length > 0)
          {
            minDate = new Date(($('option:eq(0)', objects.year).attr('value') != '') ? $('option:eq(0)', objects.year).attr('value') : $('option:eq(1)', objects.year).attr('value'), 1 - 1, 1);
            maxDate = new Date($('option:last', objects.year).attr('value'), 12 - 1, 31);
          }

          if ($rangeWidget)
          {
            if ($(this).attr('id') == $baseRangeId && $('#' + $baseId).datepicker("getDate") != null)
            {
              minDate = $('#' + $baseId).datepicker("getDate");
            }
            if ($(this).attr('id') == $baseId && $('#' + $baseRangeId).datepicker("getDate") != null)
            {
              maxDate = $('#' + $baseRangeId).datepicker("getDate");
            }
          }
        }
        
        if (inst.settings.maxDate && !maxDate)
        {
          maxDate = inst.settings.maxDate;
        }
        if (inst.settings.minDate && !minDate)
        {
          minDate = inst.settings.minDate;
        }
        
        if (minDate > maxDate)
        {
          minDate = maxDate;
        }

        return { minDate: minDate, maxDate: maxDate };
      }

      /**
       * Update 3 controls to match a date picker selection
       */
      function updateLinked(dateText, inst)
      {
        var objects = false;
        if ($(this).attr('id') == $baseId)
        {
          objects = $widgets;
        }
        else if ($(this).attr('id') == $baseRangeId)
        {
          objects = $rangeWidgets;
        }

        if (objects)
        {
          objects.month.val(inst.selectedMonth + 1);
          objects.day.val(inst.selectedDay);
          objects.year.val(inst.selectedYear);
        }
      }

      /**
       * Update date picker date (for date range)
       */
      function dateUpdate()
      {
        var id      = $(this).attr('id') || '';
        var baseId  = id.substr(0, id.lastIndexOf('_'));
        var objects = false;
        if (baseId == $baseId)
        {
          objects = $widgets;
        }
        else if (baseId == $baseRangeId)
        {
          objects = $rangeWidgets;
        }
        
        $(input).datepicker('setDate', new Date(objects.year.val(), objects.month.val() -1, objects.day.val()));
      }

      /**
       * Prevent selection of invalid dates through the controls
       */
      function checkLinkedDays()
      {
        var id      = $(this).attr('id') || '';
        var baseId  = id.substr(0, id.lastIndexOf('_'));
        var objects = false;
        if (baseId == $baseId)
        {
          objects = $widgets;
        }
        else if (baseId == $baseRangeId)
        {
          objects = $rangeWidgets;
        }

        var daysInMonth = 32 - new Date(objects.year.val(), objects.month.val() - 1, 32).getDate();

        $('option', objects.day).removeAttr('disabled');
        var daysInMonthIndex = daysInMonth;
        if ('' != $('option:first-child', objects.day).val())
        {
          daysInMonthIndex--;
        }
        $('option:gt(' + daysInMonthIndex + ')', objects.day).attr("disabled", "disabled");


        if (objects.day.val() > daysInMonth)
        {
          objects.day.val(daysInMonth);
        }
      }

      init();
    });

  };

})(jQuery);
