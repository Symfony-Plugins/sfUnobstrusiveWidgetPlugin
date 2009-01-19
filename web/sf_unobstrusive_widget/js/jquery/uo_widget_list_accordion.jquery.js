/**
 * Initialize an unobstrusive accordion widget using jQuery.
 * Match all DIV with "uo_widget_list_accordion" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_list_accordion_config      = {};
$(document).ready(function()
{
  $('ul.uo_widget_list_accordion').each(function(i)
  {
    if ($(this).hasClass('uo_widget_list_accordion_ON'))
    {
      return $(this);
    }
  
    var id        = $(this).attr('id');
    var config    = {};
    if (undefined != uo_widget_list_accordion_config[id])
    {
      config = uo_widget_list_accordion_config[id];
    }
    
    //create A element
    $('.uo_widget_accordion_title', this).each(function()
    {
      var firstchild = this.firstChild;
      if ('a' != firstchild.nodeName.toLowerCase())
      {
        $(firstchild).before('<a href="#"></a>');
        $(firstchild).appendTo(this.firstChild);
      }
    });
    
    $(this).addClass('uo_widget_list_accordion_ON');
    $(this).removeClass('uo_widget_list_accordion');
    
    config.active = '.uo_widget_accordion_active',
    config.header = '.uo_widget_accordion_title',
      
    $(this).accordion(config);
	});
});