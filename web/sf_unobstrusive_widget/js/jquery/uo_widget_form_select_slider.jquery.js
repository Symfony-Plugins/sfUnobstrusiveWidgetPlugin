/**
 * Initialize an unobstrusive slider widget using jQuery.
 * Match all SELECT with "uo_widget_form_select_slider" class.
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_select_slider_config = {};
jQuery('document').ready(function(){
  $('.uo_widget_form_select_slider').each(function()
  {
    if ($(this).hasClass('uo_widget_form_select_slider_ON'))
    {
      return $(this);
    }
  
    // detect if it's a simple or a range slider
    var selectIds   = [];
    var id          = $(this).attr('id');
    var selector    = '';
    var params      = {};

    selectIds.push(id);
    if ($(this).attr('name').lastIndexOf('[from]') > 0)
    {
      var idLastIndexOfFrom = $(this).attr('id').lastIndexOf('_from');
      if (idLastIndexOfFrom > 0)
      {
        var idTo = $(this).attr('id').substr(0, idLastIndexOfFrom)+'_to';
        if ($('#'+idTo))
        {
          selectIds.push(idTo);
        }

        $('#'+id).prevAll('span.from:first').hide();
        $('#'+idTo).prevAll('span.to:first').hide();
      }
    }

    if (selectIds.length > 2)
    {
      return $(this);
    }

    $(selectIds).each(function(i)
    {
      if ('' != selector)
      {
        selector += ',';
      }
      selector += '#'+this;

      $('#'+this).removeClass('uo_widget_form_select_slider');
      $('#'+this).addClass('uo_widget_form_select_slider_ON');
    });

    if (undefined != uo_widget_form_select_slider_config[id])
    {
      params = uo_widget_form_select_slider_config[id];
    }

    $(selector).accessibleUISlider(params);
  });
});