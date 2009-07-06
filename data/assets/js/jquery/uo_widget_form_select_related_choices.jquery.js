/**
 * Unobstrusive related select's choices widget using jQuery.
 * example : $('select.uo_widget_form_select_related_choices').uoWidgetFormSelectRelatedChoices({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
;(function($) {

  $.widget('ui.uoWidgetFormSelectRelatedChoices', 
  {
 
    _init: function()
    {
      // prevent initialize twice
      if (this.element.hasClass('uo_widget_form_select_related_choices_ON'))
      {
        return false;
      }
      
      this.element.removeClass('uo_widget_form_select_related_choices');
      this.element.addClass('uo_widget_form_select_related_choices_ON');

      this.id                 = this.element.attr('id');
      this.relatedSelect      = $(this.options.related_select);

      // register events
      this._registerEvents();
      
      this._updateRelatedChoices();
    },
    
    destroy: function()
    {
      this.element.removeClass('uo_widget_form_select_related_choices_ON');
      this.element.addClass('uo_widget_form_select_related_choices');
    },
  
    _updateRelatedChoices: function()
    {
      var value = this.element.val();
    
      $('option', this.relatedSelect).hide();
      $('option[value=""]', this.relatedSelect).show();
      
      if ('' != value)
      {
        if (undefined != this.options.class_prefix)
        {
          value = this.options.class_prefix + value;
        }

        $('option.' + value, this.relatedSelect).show();
      }
      
      $('option:selected', this.relatedSelect).removeAttr('selected');
    },
    
    _registerEvents: function(elements)
    {
      var that = this;
      this.element.change(function() { that._updateRelatedChoices(); });
    }

  });
  
  $.extend($.ui.uoWidgetFormSelectManyDoubleList, {
    defaults: {
      related_select: false, // selector || element
      class_prefix: false
    }
  });

})(jQuery);