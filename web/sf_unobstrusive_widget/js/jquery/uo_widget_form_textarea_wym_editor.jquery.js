/**
 * Initialize an unobstrusive wym editor widget using jQuery.
 * Match all TEXTAREA with "uo_widget_form_textarea_wym_editor" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */

var uo_widget_form_textarea_wym_editor_config = {};
jQuery('document').ready(function(){
  $('textarea.uo_widget_form_textarea_wym_editor').each(function()
  {
    if ($(this).hasClass('uo_widget_form_textarea_wym_editor_ON'))
    {
      return $(this);
    }

    var params  = {};
    var id      = $(this).attr('id');
    if (undefined != uo_widget_form_textarea_wym_editor_config[id])
    {
      params = uo_widget_form_textarea_wym_editor_config[id];
    }

    $(this).removeClass('uo_widget_form_textarea_wym_editor');
    $(this).addClass('uo_widget_form_textarea_wym_editor_ON');

    $(this).wymeditor(params);
  });
});