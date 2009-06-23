/**
 * Unobstrusive swf upload widget using jQuery.
 *example : $(':file.uo_widget_form_input_file_swf_upload').uoWidgetFormInputFileSwfUpload({});
 *
 * @author     François Béliveau <francois.beliveau@my-labz.com>
 */
var uo_widget_form_input_file_swf_upload_count  = 0;
;(function($) {

  $.fn.uoWidgetFormInputFileSwfUpload = function(customConfiguration)
  {
    // default configuration
    var configuration = {
      labels: {
        browse: 'browse'
      },
      images: {
        browse: '/sfUnobstrusiveWidgetPlugin/images/default/uo_widget_form_input_file_swf_upload/folder_explore.png'
      },
      upload_auto: false,
      upload_url: false,
      file_post_name: 'swf_upload_file',

      // Flash file settings
      file_size_limit: '10240', //10 MB
      file_types: '*.*',
      file_types_description: 'All files',
      file_upload_limit: '0',
      file_queue_limit: '1',

      // Button Settings
      button_width: 16,
      button_height: 16,

      // Flash Settings
      flash_url : "/sfUnobstrusiveWidgetPlugin/vendor/swf_upload/Flash/swfupload.swf",

      // Debug settings
      debug: false
    };
    var handlers = {
      swfupload_loaded_handler: false,
      file_dialog_start_handler: false,
      file_queue_error_handler: false,
      file_dialog_complete_handler: false,
      upload_start_handler: false,
      upload_progress_handler: false,
      upload_error_handler: false,
      upload_success_handler: false,
      upload_complete_handler: false,
      file_queued_handler: false
    };

    // merge default and custom configuration
    for (handlerName in handlers)
    {
      if (typeof(customConfiguration[handlerName]) == 'function')
      {
        handlers[handlerName] = customConfiguration[handlerName];
      }
    }
    $.extend(true, configuration, customConfiguration);

    return this.each(function(index)
    {
      var $widget         = $(this);
      var $widgetId       = $widget.attr('id');
      var $widgetName     = $widget.attr('name');
      var $widgetFileName = false;
      var $swfUpload      = false;
      var $index          = uo_widget_form_input_file_swf_upload_count;

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

        config = getConfiguration();
        if (config.upload_url)
        {
          $widget.after(getHtmlTemplate(config))
          var newWidget   = $widget.next();
          $widget.remove();

          $widget         = newWidget;
          $widgetFileName = $('#'+$widgetId);

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
        var result = {};
        result     = $.extend(true, configuration, result);

        // Event handler settings
        result.swfupload_loaded_handler     = swfUploadLoaded,
        result.file_dialog_start_handler    = fileDialogStart;
        result.file_queue_error_handler     = fileQueueError;
        result.file_dialog_complete_handler = fileDialogComplete;
        result.upload_start_handler         = uploadStart;
        result.upload_progress_handler      = uploadProgress;
        result.upload_error_handler         = uploadError;
        result.upload_success_handler       = uploadSuccess;
        result.upload_complete_handler      = uploadComplete;

        if (undefined == result.custom_settings)
        {
          result.custom_settings = {};
        }
        result.custom_settings.progress_target   = $widgetId+'_flash',
        result.custom_settings.upload_successful = false;
        
        result.button_window_mode = SWFUpload.WINDOW_MODE.TRANSPARENT;

        result.button_placeholder_id    = $widgetId+'_browse';
        if (result.images.browse)
        {
          result.button_image_url = result.images.browse;
        }
        else
        {
          result.button_text = result.labels.browse;
        }

        if (result.upload_auto)
        {
          result.file_queued_handler = fileQueuedAutoUpload;
        }
        else
        {
          result.file_queued_handler = fileQueued;
        }

        return result
      }

      /**
       * Return widget's HTML template
       */
      function getHtmlTemplate(config)
      {
        result = '<div class="uo_widget_form_input_file_swf_upload_ON_container">'
                 + '  <div class="field">'
                 + '    <input id="'+$widgetId+'" type="text" class="text" readonly="readonly" />'
                 + '    <span id="'+$widgetId+'_browse">'+config.labels.browse+'</span>'
                 + '  </div>'
                 //This is where the file progress gets shown
                 + '  <div class="flash" id="'+$widgetId+'_flash"></div>'
                 //This is where the file ID is stored after SWFUpload uploads the file and gets the ID back from upload PHP script
                 + '  <input type="hidden" name="'+$widgetName+'" value="" />'
                 + '</div>'

        return $(result);
      }

      /**
       * Start upload
       */
      function doUpload(e)
      {
        try
        {
          $swfUpload.startUpload();
        }
        catch(ex){}

        return false;
      }

      /**
       * Called by the queue complete handler to submit the form
       */
      function uploadDone()
      {
      	try
        {
          $swfUpload.destroy();
          $widget.parents('form:first')
            .unbind('submit', doUpload)
            .submit();
      	}
        catch(ex)
        {
      		alert("Error submitting form");
      	}
      }

      /**
       * Cancel upload if allready start
       */
      function fileDialogStart()
      {
      	$swfUpload.cancelUpload();
        
        if (typeof(handlers.file_dialog_start_handler) == 'function')
        {
          handlers.file_dialog_start_handler();
        }
      }

      /**
       * Handle this error separately because we don't want to create a FileProgress element for it.
       */
      function fileQueueError(file, errorCode, message)
      {
        try
        {
          switch (errorCode)
          {
            case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
              alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
              return false;
            case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
              alert("The file you selected is too big.");
              this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
              return false;
            case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
              alert("The file you selected is empty.  Please select another file.");
              this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
              return false;
            case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
              alert("The file you choose is not an allowed file type.");
              this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
              return false;
            default:
              alert("An error occurred in the upload. Try again later.");
              this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
              return false;
          }
        }
        catch(e){}
        
        if (typeof(handlers.file_queue_error_handler) == 'function')
        {
          handlers.file_queue_error_handler(file, errorCode, message);
        }
      }

      function fileQueued(file)
      {
        $widget.parents('form:first')
          .unbind('submit', doUpload)
          .submit(doUpload);
        $widgetFileName.val(file.name);
        
        if (typeof(handlers.file_queued_handler) == 'function')
        {
          handlers.file_queued_handler(file);
        }
      }

      function fileQueuedAutoUpload(file)
      {
        $widgetFileName.val(file.name);
        doUpload();
        
        if (typeof(handlers.file_queued_handler) == 'function')
        {
          handlers.file_queued_handler(file);
        }
      }

      function uploadProgress(file, bytesLoaded, totalBytes)
      {
        try
        {
          var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);

          file.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
          var progress = new FileProgress(file, this.customSettings.progress_target);
          progress.setProgress(percent);
          progress.setStatus("Uploading...");
        }
        catch(e){}
        
        if (typeof(handlers.upload_progress_handler) == 'function')
        {
          handlers.upload_progress_handler(file, bytesLoaded, totalBytes);
        }
      }

      function uploadSuccess(file, serverData, receivedResponse)
      {
        try
        {
          file.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
          var progress = new FileProgress(file, this.customSettings.progress_target);
          progress.setComplete();
          progress.setStatus("Complete.");
          progress.toggleCancel(false);

          if (serverData === " ")
          {
            this.customSettings.upload_successful = false;
          }
          else
          {
            this.customSettings.upload_successful = true;
            $('input[name="'+$widgetName+'"]', $widget).val(serverData);
          }
        }
        catch(e){}
        
        if (typeof(handlers.upload_success_handler) == 'function')
        {
          handlers.upload_success_handler(file, serverData, receivedResponse);
        }
      }

      function uploadComplete(file)
      {
        try
        {
          if (this.customSettings.upload_successful)
          {
            var config = getConfiguration();
            if (!config.upload_auto)
            {
              uploadDone();
            }
          }
          else
          {
            file.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
            var progress = new FileProgress(file, this.customSettings.progress_target);
            progress.setError();
            progress.setStatus("File rejected");
            progress.toggleCancel(false);

            $widgetFileName.val('');

            alert("There was a problem with the upload.\nThe server did not accept it.");
          }
        }
        catch(e){}
        
        if (typeof(handlers.upload_complete_handler) == 'function')
        {
          handlers.upload_complete_handler(file);
        }
      }
      
      function uploadError(file, errorCode, message)
      {
        try
        {
          if (errorCode === SWFUpload.UPLOAD_ERROR.FILE_CANCELLED)
          {
            // Don't show cancelled error boxes
            return;
          }

          $widgetFileName.val('');

          // Handle this error separately because we don't want to create a FileProgress element for it.
          switch (errorCode)
          {
            case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
              alert("There was a configuration error.  You will not be able to upload a resume at this time.");
              this.debug("Error Code: No backend file, File name: " + file.name + ", Message: " + message);
              return;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
              alert("You may only upload 1 file.");
              this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
              return;
            case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
            case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
              break;
            default:
              alert("An error occurred in the upload. Try again later.");
              this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
              return;
          }

          file.id = "singlefile";	// This makes it so FileProgress only makes a single UI element, instead of one for each file
          var progress = new FileProgress(file, this.customSettings.progress_target);
          progress.setError();
          progress.toggleCancel(false);

          switch (errorCode)
          {
            case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
              progress.setStatus("Upload Error");
              this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
              break;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
              progress.setStatus("Upload Failed.");
              this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
              break;
            case SWFUpload.UPLOAD_ERROR.IO_ERROR:
              progress.setStatus("Server (IO) Error");
              this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
              break;
            case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
              progress.setStatus("Security Error");
              this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
              break;
            case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
              progress.setStatus("Upload Cancelled");
              this.debug("Error Code: Upload Cancelled, File name: " + file.name + ", Message: " + message);
              break;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
              progress.setStatus("Upload Stopped");
              this.debug("Error Code: Upload Stopped, File name: " + file.name + ", Message: " + message);
              break;
          }
        }
        catch(ex){}
        
        if (typeof(handlers.upload_error_handler) == 'function')
        {
          handlers.upload_error_handler(file, errorCode, message);
        }
      }

      function swfUploadLoaded()
      {
        if (typeof(handlers.swfupload_loaded_handler) == 'function')
        {
          handlers.swfupload_loaded_handler();
        }
      }

      function fileDialogComplete(numFilesSelected, numFilesQueued)
      {
        if (typeof(handlers.file_dialog_complete_handler) == 'function')
        {
          handlers.file_dialog_complete_handler(numFilesSelected, numFilesQueued);
        }
      }

      function uploadStart(file)
      {
        if (typeof(handlers.upload_start_handler) == 'function')
        {
          handlers.upload_start_handler(file);
        }
      }

      init();
    });

  };

})(jQuery);