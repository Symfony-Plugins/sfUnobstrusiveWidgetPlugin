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
      this.relatedSelectValue = this.relatedSelect.val();

      // register events
      this._registerEvents();
      
      if (this.options.update_on_document_ready)
      {
        var that = this;
        $(document).ready(function(){
          that._updateRelatedChoices();
        });
      }
      else
      {
        this._updateRelatedChoices();
      }
    },
    
    destroy: function()
    {
      this.element.removeClass('uo_widget_form_select_related_choices_ON');
      this.element.addClass('uo_widget_form_select_related_choices');
    },
  
    _updateRelatedChoices: function()
    {
      var value = this.element.val();
    
      $('option', this.relatedSelect)
        .hide()
        .attr('disabled', 'disabled');
      $('option[value=""]', this.relatedSelect)
        .show()
        .removeAttr('disabled');
      $('option:selected', this.relatedSelect).removeAttr('selected');

      if ('' != value)
      {
        if (undefined != this.options.class_prefix)
        {
          value = this.options.class_prefix + value;
        }

        $('option.' + value, this.relatedSelect)
          .show()
          .removeAttr('disabled');
        $('option.' + value + '[value="' + this.relatedSelectValue + '"]', this.relatedSelect).attr('selected', 'selected');
      }
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
      class_prefix: false,
      update_on_document_ready: true
    }
  });

})(jQuery);