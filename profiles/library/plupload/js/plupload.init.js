// We only have to initialise one uploader, which reduces the amount of memory and code we have to fiddle with
// Most folks online initialise many instances, where there is really no need
$(function(){
  // Create a new instance of the uploader
	file_upload = new plupload.Uploader({
		browse_button: 'upload',
		chunk_size: '1mb'
	});

  // Keep track of files to be uploaded
  file_upload.queue = 0;

  // Initialise the uploader
	file_upload.init();

  // Create the links to add files
	//$('.image_upload').append('<a class="upload">Attach an image</a>');
	//$('.file_upload').append('<a class="upload">Attach a file</a>');

  file_upload.bindControls = function() {
    $('.upload').click(function() {
      $('.plupload input').click();

      // Remove the previously active uploader as the current
      $('.file_upload_container.current').removeClass('current');

      // Set the now active uploader to the current
      $(this).closest('.file_upload_container').addClass('current');
    });
  };

	file_upload.bind('FilesAdded', function(uploader, files) {
    var uploaderId = '';

    if (typeof $('.file_upload_container.current .file_upload').attr('id') != 'undefined') {
      uploaderId = $('.file_upload_container.current .file_upload').attr('id');
    }
    else if (typeof $('.file_upload_container.current .image_upload').attr('id') != 'undefined') {
      uploaderId = $('.file_upload_container.current .image_upload').attr('id');
    }

    // Make sure for each file added, we know which uploader added the file
    for (var i in files) {
      files[i].uploaderId = uploaderId;
    }

    file_upload.queue += 1;

    // Start uploading the files in the queue
		file_upload.start();
	});

  // Before a file uploads, make sure we pass which uploader was used to the file so that once the file has been
  // uploaded we know which array to add the uploaded file to
  file_upload.bind('BeforeUpload', function(uploader, file) {
    uploader.settings.url = $('#' + file.uploaderId + ' input[type=hidden][name=upload_url]').attr('value') + '?uploader_id=' + file.uploaderId + '&file_id=' + file.id;
  });

  // Takes the attribute ID to store which uploader was used in a hidden input, which also contains the temp filename
  // and the original filename (cleaned)
  file_upload.bind('FileUploaded', function(uploader, file, response) {
    var data = $.parseJSON(response.response);

    if (data.uploaderId != '') {
      $('#' + data.uploaderId).append('<input class="file_upload_' + data.fileId + '" type="hidden" name="files[' + data.uploaderId + '][]" value="' + data.originalFileName + '" />');
    }
    else {
      $('.image_upload').append('<input class="file_upload_' + data.fileId + '" type="hidden" name="files[]" value="' + data.originalFileName + '" />');
    }

    file_upload.queue -= 1;
  });

	// Make sure files upload before the form submits, doesn't matter what form, if files are uploading delay the post
	$('form').submit(function(e) {
    // There are files in the queue which must be uploaded first
    if (file_upload.queue > 0) {
      // When all files are uploaded submit form
      file_upload.bind('StateChanged', function() {
        if (file_upload.queue == 0) {
          $('form')[0].submit();
        }
      });

      return false;
    }
  });
});