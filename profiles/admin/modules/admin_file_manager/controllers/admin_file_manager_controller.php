<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_File_Manager_Controller extends Controller {
  // xxx this is a mess as it was taken from the example provided by plupload, it requires a rewrite
  public static function upload () {
    // Intentionally delay to prevent flooding
    sleep(1);

    // Make sure only admin can upload files
    if (Session::valueExists('admin_username') == FALSE || Session::getValue('admin_username') == '') {
      echo '{"error" : {"code": 100, "message": "Not authorised."}}';
      exit;
    }

    $authorisedUsers = new Cmf_Authorisation;
    $authorisedUsers->grantRoleAccess('Administrate Website');
    $authorisedUsers->grantGroupAccess('Administrators');
    if ($authorisedUsers->isUserAuthorised(Session::getValue('admin_username')) == FALSE) {
      echo '{"error" : {"code": 100, "message": "Not authorised."}}';
      exit;
    }

    // We don't want the template engine to render anything so we set the template to an empty file (a bit of a hack)
    // what should realistically happen is there should be a module file that turns off the template engine, and
    // runs functions such as the purging of temporary files
    Cmf_Template_Engine::setMasterTemplate('profiles/shared/views/empty' . PHP_EXT);

    Response_Buffer::setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
    Response_Buffer::setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT');
    Response_Buffer::setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
    Response_Buffer::setHeader('Cache-Control', 'post-check=0, pre-check=0', FALSE);
    Response_Buffer::setHeader('Pragma', 'no-cache');

    // Cleanup any temp files from previous uploads
    Cmf_File_Storage::purgeTempFiles();

    $targetDir = Config::getValue('site', 'storage', 'temp', 'path');

    // Create target dir if it doesn't already exist (it should)
    if (file_exists($targetDir) == FALSE) {
      mkdir($targetDir);
    }

    // Because the framework has no mechanism for getting data for both post and get at the moment, the following will have to make do
    $chunk = isset($_REQUEST['chunk']) ? intval($_REQUEST['chunk']) : 0;
    $chunks = isset($_REQUEST['chunks']) ? intval($_REQUEST['chunks']) : 0;
    $fileName = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
    $uploaderId = isset($_REQUEST['uploader_id']) ? $_REQUEST['uploader_id'] : '';
    $fileId = isset($_REQUEST['file_id']) ? $_REQUEST['file_id'] : '';

    if (empty($fileName) == TRUE) {
      echo '{"error" : {"code": 101, "message": "Nothing to upload."}}';
      exit;
    }

    // Clean the filename
    $fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

    // Make sure the fileName is unique but only if chunking is disabled
    if ($chunks < 2 && file_exists($targetDir . $fileName)) {
      $ext = strrpos($fileName, '.');
      $fileNameA = substr($fileName, 0, $ext);
      $fileNameB = substr($fileName, $ext);

      $count = 1;
      while (file_exists($targetDir . $fileNameA . '_' . $count . $fileNameB)) {
        $count++;
      }

      $fileName = $fileNameA . '_' . $count . $fileNameB;
    }

    // This is the full filePath
    $filePath = $targetDir . $fileName;

    // Look for the content type header
    $contentType = NULL;
    if (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
      $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
    }
    if (isset($_SERVER['CONTENT_TYPE'])) {
      $contentType = $_SERVER['CONTENT_TYPE'];
    }

    // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
    if (strpos($contentType, 'multipart') !== FALSE) {
      if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Open temp file
        $out = fopen("{$filePath}.part", $chunk == 0 ? 'wb' : 'ab');
        if ($out) {
          // Read binary input stream and append it to temp file
          $in = fopen($_FILES['file']['tmp_name'], 'rb');

          if ($in) {
            while ($buffer = fread($in, 4096))
              fwrite($out, $buffer);
          }
          else {
            echo '{"error" : {"code": 101, "message": "Failed to open input stream."}}';
            exit;
          }
          fclose($in);
          fclose($out);
          @unlink($_FILES['file']['tmp_name']);
        }
        else {
          echo '{"error" : {"code": 102, "message": "Failed to open output stream."}}';
          exit;
        }
      }
      else {
        echo '{"error" : {"code": 103, "message": "Failed to move uploaded file."}}';
        exit;
      }
    }
    else {
      // Open temp file
      $out = fopen("{$filePath}.part", $chunk == 0 ? 'wb' : 'ab');
      if ($out) {
        // Read binary input stream and append it to temp file
        $in = fopen('php://input', 'rb');

        if ($in) {
          while ($buffer = fread($in, 4096)) {
            fwrite($out, $buffer);
          }
        }
        else {
          echo '{"error" : {"code": 101, "message": "Failed to open input stream."}}';
          Cmf_Application::terminate();
        }

        fclose($in);
        fclose($out);
      }
      else {
        echo '{"error" : {"code": 103, "message": "Failed to open output stream."}}';
        exit;
      }
    }

    // Check if file has been uploaded
    if (!$chunks || $chunk == $chunks - 1) {
      // We don't care about the original filename, that is stored in the DB
      $newFileName = uniqid(php_uname('n'), TRUE);
      rename("{$filePath}.part", $targetDir . $newFileName);
    }

    // Get the original filename
    $fileName = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';

    // Store the new filename in the session if the file has finished uploading
    if (isset($newFileName) == TRUE) {
      $fileUploads = array();

      if (Session::valueExists('file_uploads')) {
        $fileUploads = unserialize(Session::getValue('file_uploads'));
      }
      
      $fileUploads[$uploaderId][$fileId] = array(
        'original_file_name' => $fileName,
        'stored_file_name' => $newFileName
      );
      Session::setValue('file_uploads', serialize($fileUploads));
    }

    echo '{"originalFileName" : "' . htmlspecialchars($fileName) . '", "uploaderId" : "' . htmlspecialchars($uploaderId) . '", "fileId" : "' . htmlspecialchars($fileId) . '"}';

    // As this isn't a full template page, we terminate the application to prevent the execution from carrying out
    //Cmf_Application::terminate();
    // xxx need to find out why using Cmf_Application::terminate() breaks things here
    exit;
  }

  /*
  public static function uploadTest () {
    Admin_Controller::shared();

    $targetDir = Config::getValue('site', 'storage', 'temp', 'path');

    if (View_Data::valueExists('body', 'js') == TRUE) {
      $js = View_Data::getValue('body', 'js');
    }
    else {
      $js = new Cmf_Js_Control;
    }

    $js->addJs('/profiles/library/plupload/js/plupload.js');
    $js->addJs('/profiles/library/plupload/js/plupload.html5.js');
    $js->addJs('/profiles/library/plupload/js/plupload.html4.js');
    $js->addJs('/profiles/library/plupload/js/plupload.init.js');
    $js->addJs('/profiles/admin/modules/admin_file_manager/js/admin_file_manager.js');
    
    View_Data::setValue('body', 'js', $js);

    $content = <<< HTML_END
      <div id="main_content" class="first last">
        <form method="post" action="" style="clear:both;display:block">
          <div class="thumbs file_upload_container">
            <span class="fileinput-button form-button add image_upload" id="thumbs"></span>
          </div>

          <!--
          <div class="thumbs file_upload_container">
            <h1>Thumbs</h1>
            <div class="image_upload" id="thumbs">
              <input type="hidden" name="upload_url" value="/admin/admin_file_manager/upload/" />
            </div>
            <div class="file_list"></div>
          </div>
          -->

          <fieldset class="files file_upload_container">
            <h1>Files</h1>

            <span class="file_upload" id="files">
              <h1 class="upload">Upload</h1>
              <input type="hidden" name="upload_url" value="/admin/admin_file_manager/upload/" />
            </span>
            <div class="file_list"></div>

          </fieldset>
          <input type="submit" name="action" value="Save" />
        </form>
      </div>
HTML_END;

    $fileUploads = array();
    if (Session::valueExists('file_uploads')) {
      $fileUploads = unserialize(Session::getValue('file_uploads'));
    }
    echo '<!--';
    var_dump($fileUploads);
    echo '-->';

    View_Data::setValue('content', $content);
  }*/
}

?>