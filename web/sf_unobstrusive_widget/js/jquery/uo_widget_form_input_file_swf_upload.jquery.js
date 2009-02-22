/**
 * Unobstrusive swf upload widget using jQuery.
 *example : $(':file.uo_widget_form_input_file_swf_upload').uoWidgetFormInputFileSwfUpload({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_input_file_swf_upload_config = {};
var uo_widget_form_input_file_swf_upload_count  = 0;
(function($) {

  $.fn.uoWidgetFormInputFileSwfUpload = function(customConfiguration)
  {
    // default configuration
    var configuration = {
      upload_url: '/test_dev.php/plugin/upload',
      file_post_name: 'upload_file',

      // Flash file settings
      file_size_limit: '10240', //10 MB
      file_types: '*.*',
      file_types_description: 'All files',
      file_upload_limit: '0',
      file_queue_limit: '1',

      // Event handler settings
      swfupload_loaded_handler : swfUploadLoaded,

        //file_dialog_start_handler : fileDialogStart,		// I don't need to override this handler
        file_queued_handler : fileQueued,
        file_queue_error_handler : fileQueueError,
        file_dialog_complete_handler : fileDialogComplete,
        //upload_start_handler : uploadStart,	// I could do some client/JavaScript validation here, but I don't need to.
        upload_progress_handler : uploadProgress,
        upload_error_handler : uploadError,
        upload_success_handler : uploadSuccess,
                              upload_complete_handler : uploadComplete,
      
      
      // Flash Settings
      flash_url : "/sf_unobstrusive_widget/vendor/swf_upload/swf/swfupload_f9.swf",

      // Debug settings
      debug: true
    };

    var template = '<div class="uo_widget_form_input_file_swf_upload_ON_container" id="%%CONTAINNER_ID%%">'
                 + '  <div>'
                 + '    <input type="text" id="%%INPUT_TEXT_ID%%" disabled="true" />'
                 + '    <span id="%%SPAN_BUTTON_ID%%"></span>'
                 + '  </div>'
                 + '  <div class="flash" id="%%UPLOAD_PROGRESS_ID%%"></div>'
                 + '  <input type="hidden" name="%%INPUT_HIDDEN_FILE_NAME%%" id="%%INPUT_HIDDEN_FILE_ID%%" value="" />'
                 + '</div>'

    // merge default and custom configuration
    $.extend(true, configuration, customConfiguration);

    return this.each(function(index)
    {
      var $widget    = $(this);
      var $swfUpload = false;
      var $index     = uo_widget_form_input_file_swf_upload_count;

      /**
       * Initialize widget
       */
      function init()
      {
        // prevent initialize twice
        if ($widget.hasClass('uo_widget_form_input_file_swf_upload_ON'))
        {
          return $widget;
        }

        if (typeof(SWFUpload) != "function")
        {
          alert('Unable to initialize SWFUpload widget: SWFUpload is not defined');
          return $widget;
        }

        $widget.removeClass('uo_widget_form_input_file_swf_upload');
        $widget.addClass('uo_widget_form_input_file_swf_upload_ON');

        $widget.after(getHtmlTemplate($widget, $index))
        var newWidget = $widget.prev();
        $widget.remove();
        $widget = newWidget;
        
        
        var config = getConfiguration();
        if (config.upload_url)
        {
          $swfUpload = new SWFUpload(config);
          uo_widget_form_input_file_swf_upload_count++;
        }
        else
        {
          alert('Unable to initialize SWFUpload widget: invalid upload url');
        }
      }

      /**
       * Return widget's specific configuration
       */
      function getConfiguration()
      {
        var result = uo_widget_form_input_file_swf_upload_config[$widget.attr('id')] || {};
        result.swfupload_element_id = 'swf_upload_container_' + $index; // setting for the graceful degradation plugin
        //result.degraded_element_id : "degradedUI",
        return $.extend(true, configuration, result);
      }
      
      /**
       * Return widget's HTML template
       */
      function getHtmlTemplate(widget, index)
      {
        result = template.replace(/%%CONTAINNER_ID%%/, 'swf_upload_container_' + index);
        result = result.replace(/%%INPUT_HIDDEN_FILE_ID%%/, widget.attr('id'));
        result = result.replace(/%%INPUT_HIDDEN_FILE_NAME%%/, widget.attr('name'));
        result = result.replace(/%%INPUT_TEXT_ID%%/, 'txtFileName' + index);
        result = result.replace(/%%UPLOAD_PROGRESS_ID%%/, 'fsUploadProgress' + index);
        result = result.replace(/%%SPAN_BUTTON_ID%%/, 'spanButtonPlaceholder' + index);

        return result;
      }

      init();
    });

  };

})(jQuery);
