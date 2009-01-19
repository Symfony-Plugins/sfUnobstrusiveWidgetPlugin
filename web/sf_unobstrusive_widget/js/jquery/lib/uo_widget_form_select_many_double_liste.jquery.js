jQuery.fn.uoWidgetFormSelectManyDoubleList = function(settings)
{
  settings = jQuery.extend({
			addLabel:    ' &gt; ',	// text of the "add" button
			removeLabel: ' &lt; '	  // text of the "remove" button
		}, settings);
	
  var id          = $(this).attr('id');
	var leftId      = id + '_left';
	var rightId     = id + '_right';
	var btnAddId    = id + '_add';
	var btnRemoveId = id + '_remove';
	var parent      = $(this).parent();
	var opts        = $(this).children().clone();
	
	var combo = '';
	combo    += '<div class="uo_widget_form_select_many_double_list_container">';
	combo    += '<select id="' + leftId + '" class="left" multiple="multiple"></select>';
	combo    += '<div class="button_container">';
	combo    += '<input type="button" class="add" id="' + btnAddId + '" value="' + settings.addLabel + '" />';
	combo    += '<input type="button" class="remove" id="' + btnRemoveId + '" value="' + settings.removeLabel + '" />';
	combo    += '</div>';
	combo    += '<select id="' + rightId + '" class="right" multiple="multiple"></select>';
	combo    += '</div>';		
		
	$(this).after(combo);

	// find the combo box in the DOM and append a copy of the options from the original element to the left side
	parent.find('#' + leftId).append(opts);

	// double-click moves an item to the other list
	$('.uo_widget_form_select_many_double_list_container select.left').dblclick(function()
	{
		$(this).parent().find('input.add').click();
	});
		
	$('.uo_widget_form_select_many_double_list_container select.right').dblclick(function()
	{
		$(this).parent().find('input.remove').click();
	});

	// add/remove buttons
	$('.uo_widget_form_select_many_double_list_container input.add').click(function()
	{
	  var id            = $(this).attr('id');
    var idLastIndexOf = id.lastIndexOf('_');
    if (!idLastIndexOf)
    {
      return false;
    }

    var baseId   = id.substr(0, idLastIndexOf);
		var leftOpts = $('#' + baseId + '_left').find('option:selected');
		$('#' + baseId + '_right').append(leftOpts).find('option:selected').removeAttr('selected');
		

		$('#' + baseId).find('option:selected').removeAttr('selected');
		$('#' + baseId + '_right').find('option').each(function()
		{
		  // select the corresponding option from the original element
		  var v = $(this).attr('value');
	    $('#' + baseId).find('option[value="' + v + '"]').attr('selected','selected');
    });
	});
	
	$('.uo_widget_form_select_many_double_list_container input.remove').click(function()
	{
	  var id            = $(this).attr('id');
    var idLastIndexOf = id.lastIndexOf('_');
    if (!idLastIndexOf)
    {
      return false;
    }

    var baseId   = id.substr(0, idLastIndexOf);
		var rightOpts = $('#' + baseId + '_right').find('option:selected');
		$('#' + baseId + '_left').append(rightOpts).find('option:selected').removeAttr('selected');
		
		$('#' + baseId).find('option:selected').removeAttr('selected');
		$('#' + baseId + '_right').find('option').each(function()
		{
		  // select the corresponding option from the original element
		  var v = $(this).attr('value');
	    $('#' + baseId).find('option[value="' + v + '"]').attr('selected','selected');
    });
	});		
		
	// add any items that were already selected
	$('#' + btnAddId).click();
	
	return this;
};