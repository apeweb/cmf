$(function(){
  // Show prices for product attributes
  $('.attribute-price').hide();

  $('#priced_by_attributes').click(function(){
    if (this.checked) {
      $('.attribute-price').show();
    }
    else {
      // Don't need to wipe the settings as PHP will flag this in the DB so we can save the settings
      $('.attribute-price').hide();
    }
  });

  // Override currency converter?
  $('.currencies').hide();

  $('#automatically_convert_currency').click(function(){
    if (this.checked) {
      // Don't need to wipe the settings as PHP will flag this in the DB so we can save the settings
      $('.currencies').hide();
    }
    else {
      $('.currencies').show();
    }
  });

  // Enable or disable stock control
  if ($('#stock_control_enabled').is(':checked')) {
    $('.stock-controlled').show();
  }
  else {
    $('.stock-controlled').hide();
  }

  $('#stock_control_enabled').click(function() {
    if (this.checked) {
      $('.stock-controlled').show();
    }
    else {
      $('.stock-controlled').hide();
    }
  });

  // Whether quantity can be controlled by customer
  if ($('#quantity_selectable').is(':checked')) {
    $('.quantity-option').show();
  }
  else {
    $('.quantity-option').hide();
  }

  $('#quantity_selectable').click(function() {
    if (this.checked) {
      $('.quantity-option').show();
    }
    else {
      $('.quantity-option').hide();
    }
  });

  // Whether shipping is required or not
  if ($('#require_shipping').is(':checked')) {
    $('#require_user_address').attr('checked', 'checked');
    $('#require_user_address').attr('disabled','disabled');
    $('.shipping_options').show();
  }
  else {
    $('#require_user_address').removeAttr('disabled');
    $('.shipping_options').hide();
  }

  $('#require_shipping').click(function() {
    if (this.checked) {
      $('#require_user_address').attr('checked', 'checked');
      $('#require_user_address').attr('disabled','disabled');
      $('.shipping_options').show();
    }
    else {
      $('#require_user_address').removeAttr('disabled');
      $('.shipping_options').hide();
    }
  });

  if ($('#accept_returns').is(':checked')) {
    $('.return_period').show();
  }
  else {
    $('.return_period').hide();
  }

  $('#accept_returns').click(function() {
    if (this.checked) {
      $('.return_period').show();
    }
    else {
      $('.return_period').hide();
    }
  });

  file_upload.bind('FilesAdded', function(uploader, files) {
    // xxx fix issue where path may change
    $.get('/profiles/admin/modules/admin_products/js/photo.tpl.htm', function(data) {
      for (var i in files) {
        $('#product_image_table .product_images').append(
          data.replace('$fileName', files[i].name.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'))
              .replace('$id', files[i].id)
        );
      }

      addTableRowDragHandles();

      $('.pushed .product_image_delete').click(function() {
        // Remove the file from the list of files selected
        $(this).closest('.product_image').remove();

        // Remove the hidden file object
        $('.file_upload_' + files[i].id).remove();

        // if the file hasn't finished uploading yet, remove it when it has
        file_upload.bind('FileUploaded', function() {
          $('.file_upload_' + files[i].id).remove();
        });
      });
    });
  });

  file_upload.bind('FileUploaded', function(uploader, file, info) {
    var response = jQuery.parseJSON(info.response);
    $('#' + file.id + ' .gallery_image img').attr('src', '/admin/admin_products/imagePreview/?file_id=' + file.id + '&uploader_id=' + file.uploaderId);
  });
});