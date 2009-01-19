/**
 * Initialize an unobstrusive spinner widget using jQuery.
 * Match all SELECT with "uo_widget_form_date_picker" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_date_picker_config = {};
jQuery('document').ready(function(){
  
  // default params
  var defaultParams = {
    beforeShow:           uo_widget_form_date_read_linked,
    onSelect:             uo_widget_form_date_update_linked,
    showOn:               'button',
    buttonImageOnly:      true,
    buttonImage:          '/sf_unobstrusive_widget/images/default/uo_widget_form_date_picker/calendar.gif',
    changeFirstDay:       false
  };

  $('.uo_widget_form_date_picker').each(function()
  {
    if ($(this).hasClass('uo_widget_form_date_picker_ON'))
    {
      return $(this);
    }
    
    // settings
    var params  = defaultParams;
    var id      = $(this).attr('id');
    if (undefined != uo_widget_form_date_picker_config[id])
    {
      $.extend(params, uo_widget_form_date_picker_config[id]);
    }
    
    // detect if it's a simple or a range widget
    var selectIds = [];
    var isRange   = false;
    selectIds.push(id);
    if ($(this).attr('name').lastIndexOf('[from]') > 0)
    {
      var idLastIndexOfFrom = $(this).attr('id').lastIndexOf('_from');
      if (idLastIndexOfFrom > 0)
      {
        var id = $(this).attr('id').substr(0, idLastIndexOfFrom)+'_to';
        if ($('#'+id))
        {
          isRange = true;
          selectIds.push(id);
        }
      }
    }
    if(selectIds.length > 2)
    {
      return $(this);
    }

    $(selectIds).each(function(i){
      // detect day, month and year controls
      var id            = selectIds[i];
      var idLastIndexOf = id.lastIndexOf('_');
      var idDay         = false;
      var idMonth       = false;
      var idYear        = false;

      if (idLastIndexOf > 0)
      {
        var baseId = id.substr(0, idLastIndexOf);
        if ($('#' + baseId + '_day'))
        {
          idDay = baseId + '_day';
        }
        if ($('#' + baseId + '_month'))
        {
          idMonth = baseId + '_month';
        }
        if ($('#' + baseId + '_year'))
        {
          idYear = baseId + '_year';
        }
      }

      if (idDay && idMonth && idYear)
      {
        $('#' + id).after('<input type="hidden" id="' + baseId  +'" />');
        $('#'+idMonth + ', #' + idYear).change(uo_widget_form_date_check_linked_days);
        if (isRange)
        {
          $('#'+idDay + ', #'+idMonth + ', #' + idYear).change(uo_widget_form_date_update);
        }

        // add min and max date (from year select)
        var options = $('#' + idYear).find('option');
        if (options.length > 0)
        {
          if (params.minDate == undefined)
          {
            if (uo_widget_form_date_picker_config[baseId] == undefined)
            {
              uo_widget_form_date_picker_config[baseId] = {};
            }
            uo_widget_form_date_picker_config[baseId].minDate = new Date((options.eq(0).attr('value') != '') ? options.eq(0).attr('value') : options.eq(1).attr('value'), 1 - 1, 1);
          }
          
          if (params.maxDate == undefined)
          {
            if (uo_widget_form_date_picker_config[baseId] == undefined)
            {
              uo_widget_form_date_picker_config[baseId] = {};
            }
            uo_widget_form_date_picker_config[baseId].maxDate = new Date(options.eq(options.length - 1).attr('value'), 12 - 1, 31);
          }
        }
      }

      // change class to "uo_widget_form_date_ON"
      $(this).removeClass('uo_widget_form_date_picker');
      $(this).addClass('uo_widget_form_date_picker_ON');

      // create date picker
      $('#' + baseId).datepicker(params);
    });
  });
});

// Prepare to show a date picker linked to three select controls 
function uo_widget_form_date_read_linked()
{
  var id = $(this).attr('id');
  $(this).val($('#'+id+'_month').val() + '/' + $('#' + id + '_day').val() + '/' + $('#' + id + '_year').val());

  var id = $(this).attr('id');
  var idLastIndexOf = id.lastIndexOf('_');
  var baseId        = id.substr(0, idLastIndexOf);
  
  var minDate = null;
  var maxDate = null;
  
  if (uo_widget_form_date_picker_config[id] != undefined)
  {
    minDate = uo_widget_form_date_picker_config[id].minDate != undefined ? uo_widget_form_date_picker_config[id].minDate : null;
    maxDate = uo_widget_form_date_picker_config[id].maxDate != undefined ? uo_widget_form_date_picker_config[id].maxDate : null;
  }
  
  if (idLastIndexOf && $('#' + baseId + '_from') && $('#' + baseId + '_to'))
  {
    if (id == baseId + '_to' && $('#' + baseId + '_from').datepicker("getDate") != null)
    {
      minDate = $('#' + baseId + '_from').datepicker("getDate");
    }
    if (id == baseId + '_from' && $('#' + baseId + '_to').datepicker("getDate") != null)
    {
      maxDate = $('#' + baseId + '_to').datepicker("getDate");
    }
  }
  
  return {
    minDate: minDate, 
    maxDate: maxDate
  };
}
 
// Update three select controls to match a date picker selection 
function uo_widget_form_date_update_linked(date)
{
  var id = $(this).attr('id');
  $('#'+id+'_month').val(date.substring(0, 2)); 
  $('#'+id+'_day').val(date.substring(3, 5)); 
  $('#'+id+'_year').val(date.substring(6, 10)); 
}

// Update date picker date (for date range)
function uo_widget_form_date_update()
{
  var id = $(this).attr('id');
  var idLastIndexOf = id.lastIndexOf('_');
  var baseId        = id.substr(0, idLastIndexOf);
  $('#'+baseId).val($('#'+baseId+'_month').val() + '/' + $('#'+baseId+'_day').val() + '/' + $('#'+baseId+'_year').val());
  
}
 
// Prevent selection of invalid dates through the select controls 
function uo_widget_form_date_check_linked_days()
{ 
  var id = $(this).attr('id');
  var idLastIndexOf = id.lastIndexOf('_');
  if (!idLastIndexOf)
  {
    return false;
  }
  
  var baseId      = id.substr(0, idLastIndexOf);
  var daysInMonth = 32 - new Date($('#' + baseId + '_year').val(), $('#' + baseId+'_month').val() - 1, 32).getDate();

  $('#' + baseId + '_day option').removeAttr('disabled');
  var daysInMonthIndex = daysInMonth;
  if ('' != $('#' + baseId + '_day option:first-child').val())
  {
    daysInMonthIndex--;
  }
  $('#' + baseId + '_day option:gt(' + daysInMonthIndex + ')').attr("disabled", "disabled");
   

  if ($('#'+baseId+'_day').val() > daysInMonth)
  { 
    $('#'+baseId+'_day').val(daysInMonth); 
   } 
}