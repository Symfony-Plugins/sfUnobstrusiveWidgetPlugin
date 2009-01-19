/**
 * Initialize an unobstrusive checklist widget using jQuery.
 * Match all UL with "uo_widget_list_auto_check" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_list_auto_check_config = {};
$(document).ready(function()
{
  $('ul.uo_widget_form_list_auto_check').each(function(i)
  {
    if ($(this).hasClass('uo_widget_form_list_auto_check_ON'))
    {
      return $(this);
    }
  
    var id        = $(this).attr('id');
    var config    = {};
    if (undefined != uo_widget_form_list_auto_check_config[id])
    {
      config = uo_widget_form_list_auto_check_config[id];
    }
    
    $(this).addClass('uo_widget_form_list_auto_check_ON');
    $(this).removeClass('uo_widget_form_list_auto_check');

    $(this).find(':checkbox').click(function(){
      if ($(this).attr('checked'))
      {
        $(this).parents('li:first').find(':checkbox').attr('checked', 'checked');
      }
      else
      {
        $(this).parents('li:first').find(':checkbox').removeAttr('checked');
      }
    });
  });
});