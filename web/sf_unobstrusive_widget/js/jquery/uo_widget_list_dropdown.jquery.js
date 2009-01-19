/**
 * Initialize an unobstrusive treeview widget using jQuery.
 * Match all UL with "uo_widget_list_dropdown" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_list_dropdown_config = {};
$(document).ready(function()
{
  $('ul.uo_widget_list_dropdown').each(function(i)
  {
    if ($(this).hasClass('uo_widget_list_dropdown_ON'))
    {
      return $(this);
    }
  
    var id        = $(this).attr('id');
    var config    = {};
    if (undefined != uo_widget_list_dropdown_config[id])
    {
      config = uo_widget_list_dropdown_config[id];
    }
    
    //create container
    var containerId = 'uo_widget_list_dropdown_container_' + id;
    $(this).before('<div class="uo_widget_list_dropdown_ON_container" id="'+containerId+'"></div>');
    var container = $(this).prev();
    $(this).appendTo(container);
    
    //fix float style
    container.append('<div style="clear:both"></div>');
    
    //create A element
    $('li', this).each(function(){
      
      var firstchild = this.firstChild;
      if ('a' != firstchild.nodeName.toLowerCase())
      {
        $(firstchild).before('<a href="#"></a>');
        $(firstchild).appendTo(this.firstChild);
        $(this.firstChild).click(function()
        { 
          return false;
        });
      }
    });
    
    $(this).addClass('uo_widget_list_dropdown_ON');
    $(this).removeClass('uo_widget_list_dropdown');

    //disable shadow on IE6
    var strChUserAgent  = navigator.userAgent;
    var intSplitStart   = strChUserAgent.indexOf("(",0);
    var intSplitEnd     = strChUserAgent.indexOf(")",0);
    var strChMid        = strChUserAgent.substring(intSplitStart, intSplitEnd);
    if (strChMid.indexOf("MSIE 6") != -1)
    {
      ddsmoothmenu.shadow = {enabled:false};
    }
    
    config.mainmenuid     = containerId;
    config.contentsource  = 'markup';
    ddsmoothmenu.init(config);
	});
});