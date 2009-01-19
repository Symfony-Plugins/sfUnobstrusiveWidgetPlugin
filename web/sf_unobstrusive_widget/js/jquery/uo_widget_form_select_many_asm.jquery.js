/**
 * Initialize an unobstrusive asm list widget using jQuery.
 * Match all SELECT with "uo_widget_form_select_many_asm" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_select_many_asm_config = {};
jQuery('document').ready(function(){
    $('select.uo_widget_form_select_many_asm').each(function()
  {
    if ($(this).hasClass('uo_widget_form_select_many_asm_ON'))
    {
      return $(this);
    }
  
    var params  = {};
    var id      = $(this).attr('id');
    if (undefined != uo_widget_form_select_many_asm_config[id])
    {
      params = uo_widget_form_select_many_asm_config[id];
    }
    
    // optgroup not working for now ... remove them
    if ($('optgroup', this).length > 0)
    {
      var options = new Array();
      $('option', this).each(function(){
        options.push({value: $(this).attr('value'), text: $(this).text()})
      });
      
      var html = '';
      for (var i=0; i<options.length; i++)
      {
        html += '<option value="'+options[i].value+'">'+options[i].text+'</option>';
      }
      
      $(this).html(html);
    }

    $(this).removeClass('uo_widget_form_select_many_asm');
    $(this).addClass('uo_widget_form_select_many_asm_ON');

    $(this).asmSelect(params);

  });
});