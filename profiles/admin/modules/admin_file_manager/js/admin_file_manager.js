$(function(){
  $('.image_upload').append('<span class="upload first">Add Photos...</span>');
  $('.file_upload').append('<span class="upload first">Add Files...</span>');
  $('.file_upload,.image_upload').append('<input type="hidden" name="upload_url" value="/admin/admin_file_manager/upload/" />');

  file_upload.bindControls();

  // files added
  file_upload.bind('FilesAdded', function(uploader, files) {
    // Uncomment to update the link to show files have been added
    //$('.file_upload_container.current .image_upload span').text('Attach another image');
    //$('.file_upload_container.current .file_upload span').text('Attach another file');

    /* an example of how to display the uploaded files...
    for (var i in files) {
      $('.file_upload_container.current .file_list').append('<span class="file"><br/><span id="' + files[i].id + '">' + files[i].name.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</span> <a class="remove">remove</a></span>');
    }

    // files removed
    $('.remove').click(function() {
      // Remove the file from the list of files selected
      $(this).closest('.file').remove();

      // Remove the hidden file object
      $('.file_upload_' + files[i].id).remove();

      // if the file hasn't finished uploading yet, remove it when it has
      file_upload.bind('FileUploaded', function() {
        $('.file_upload_' + files[i].id).remove();
      });

      // xxx if no files are in the list of uploaded files, change links back to original text
    });
    */
  });

  // form submitted
  $('form').submit(function() {
    if (file_upload.queue > 0) {
      alert('Please wait while we finish uploading the files you have selected, once the files have uploaded the page will automatically reload');
    }
  });
});