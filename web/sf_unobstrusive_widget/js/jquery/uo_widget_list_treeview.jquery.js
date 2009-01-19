/**
 * Initialize an unobstrusive treeview widget using jQuery.
 * Match all UL with "uo_widget_list_treeview" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_list_treeview_config      = {};
var uo_widget_form_list_treeview_config = {};
$(document).ready(function()
{
  $('ul.uo_widget_list_treeview, ul.uo_widget_form_list_treeview').each(function(i)
  {
    if ($(this).hasClass('uo_widget_list_treeview_ON') || $(this).hasClass('uo_widget_form_list_treeview_ON'))
    {
      return $(this);
    }
  
    var id        = $(this).attr('id');
    var config    = {};
    if (undefined != uo_widget_list_treeview_config[id])
    {
      config = uo_widget_list_treeview_config[id];
    }
    else if (undefined != uo_widget_form_list_treeview_config[id])
    {
      config = uo_widget_form_list_treeview_config[id];
    }
    
    if ($(this).hasClass('uo_widget_list_treeview'))
    {
      $(this).addClass('uo_widget_list_treeview_ON');
    }
    else if ($(this).hasClass('uo_widget_form_list_treeview'))
    {
      $(this).addClass('uo_widget_form_list_treeview_ON');
    }
    
    $(this).removeClass('uo_widget_list_treeview');
    $(this).removeClass('uo_widget_form_list_treeview');

    $(this).treeview(config);
	});
});