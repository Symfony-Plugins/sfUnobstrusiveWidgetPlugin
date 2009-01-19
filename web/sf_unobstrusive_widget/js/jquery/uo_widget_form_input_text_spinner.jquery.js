/**
 * Initialize an unobstrusive spinner widget using jQuery.
 * Match all INPUT TEXT with "uo_widget_form_input_type_spinner" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_input_text_spinner_config = {};
jQuery('document').ready(function(){
  $(':text.uo_widget_form_input_text_spinner').each(function()
  {
    if ($(this).hasClass('uo_widget_form_input_text_spinner_ON'))
    {
      return $(this);
    }
  
    var params  = {};
    var id      = $(this).attr('id');
    if (undefined != uo_widget_form_input_text_spinner_config[id])
    {
      params = uo_widget_form_input_text_spinner_config[id];
    }

    $(this).removeClass('uo_widget_form_input_text_spinner');
    $(this).addClass('uo_widget_form_input_text_spinner_ON');
    $(this).spinner(params);
  });
});
