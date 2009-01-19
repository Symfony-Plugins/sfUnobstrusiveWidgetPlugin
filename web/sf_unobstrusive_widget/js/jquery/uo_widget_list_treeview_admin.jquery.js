/**
 * Initialize an unobstrusive admin treeview widget using jQuery.
 * Match all UL with "uo_widget_list_treeview_admin" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_list_treeview_admin_config = {};
$(document).ready(function()
{
  $('ul.uo_widget_list_treeview_admin').each(function(i)
  {
    if ($(this).hasClass('uo_widget_list_treeview_admin_ON'))
    {
      return $(this);
    }
  
    var treeview  = $(this);
    var id        = $(this).attr('id');
    var config    = {};
    if (undefined != uo_widget_list_treeview_admin_config[id])
    {
      config = uo_widget_list_treeview_admin_config[id];
    }

    //create root if not exists
		if (treeview.find('.root').length < 1)
    {
      treeview.before('<ul class="uo_widget_list_treeview_admin_ON"><li class="root">root</li></ul>');
      treeview = treeview.prev();
      $(this).appendTo(treeview.find('li:first'));
    }
    else
    {
      treeview.addClass('uo_widget_list_treeview_admin_ON');
    }
    
    //create span
    $('li', this).each(function(){
      
      var firstchild = this.firstChild;
      if ('span' != firstchild.nodeName.toLowerCase() )
      {
        $(firstchild).before('<span></span>');
        $(firstchild).appendTo(this.firstChild);
      }
    });

    $(this).removeClass('uo_widget_list_treeview_admin');

    treeview.simpleTree(config);
	});
});