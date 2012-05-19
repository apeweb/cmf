<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Products_Controller extends Controller {
  private static $_arguments = array();

  public static function manage () {
    Admin_Controller::shared();

    // <tr><td><input type='checkbox' name='toggle' /></td><td>1</td><td>GQUFAX</td><td>Canon EOS Rebel T2i</td><td>&pound;9.99</td><td>22</td><td>22/01/2012 9:53</td><td class='actions'></td></tr>

    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_products' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  public static function add ($arguments) {
    Admin_Controller::shared();

    self::$_arguments = $arguments;

    // Step 1 (part B)
    if (self::_subAction() == 'kind') {
      self::_verifyProductKind();
    }
    // Step 1 (part A)
    elseif (Session::valueExists('product_kind') == FALSE
      || isset(self::$_arguments['prd_id']) == FALSE
      || ((ctype_digit(self::$_arguments['prd_id']) == TRUE && self::$_arguments['prd_id'] < 1) || self::$_arguments['prd_id'] != 'new'))
    {
      self::_showProductKinds();
    }
    // Step 2
    elseif (self::_subAction() == 'type') {
      self::_showProductTypes();
    }
    // Step 3
    elseif (self::_subAction() == 'information') {
      if (Request::method() == 'GET') {
        self::_showProductForm();
      }
      else {
        self::_handleProductFormPostback();
      }
    }
    // Unknow error (show step 1)
    else {
      self::_showProductKinds();
    }
  }

  public static function edit () {
    // xxx validate prd_id
    // xxx set product_kind and prd_id in session
  }

  // Step 1 (part A)
  private static function _showProductKinds () {
    if (Session::valueExists('product_kind') == TRUE) {
      Session::deleteValue('product_kind');
    }
    
    if (Session::valueExists('product_types') == TRUE) {
      Session::deleteValue('product_types');
    }

    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_product_add_step1' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  // Step 1 (part B)
  private static function _verifyProductKind () {
    // xxx validate product kind, if wrong then redirect back to the product kinds page with error message
    if (TRUE) {
      Session::setValue('product_kind', self::$_arguments['product_kind']);
      Response::redirect('/admin/products/add/new/type/', 302);
      exit;
    }
    else {
      Response::redirect('/admin/products/add/', 302);
      exit;
    }
  }

  // Step 2
  private static function _showProductTypes () {
    if (Request::method() == 'POST' && Session::valueExists('product_kind') == TRUE) {
      // xxx validate product type

      // Set product type
      if (isset($_POST['product_types']) == TRUE && is_array($_POST['product_types']) == TRUE) {
        $type = $_POST['product_types'];
      }
      else {
        $type = '';
      }
      Session::setValue('product_type', $type);

      // Redirect to next step
      Response::redirect(dirname(Request::path()) . '/information/', 302);
      exit;
    }

    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_product_add_step2' . PHP_EXT;

    View_Data::setValue('content', $content);
  }

  // Step 3
  private static function _showProductForm () {
    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_product_add_step3' . PHP_EXT;

    View_Data::setValue('content', $content);
    View_Data::setValue('prd_id', self::$_arguments['prd_id']);
    View_Data::setValue('product_kind_name', ucwords(str_replace('_', ' ', Session::getValue('product_kind'))));

    if (View_Data::valueExists('body', 'js') == TRUE) {
      $js = View_Data::getValue('body', 'js');
    }
    else {
      $js = new Cmf_Js_Control;
    }

    // xxx fix the '/' so that we can install in subfolders
    $js->addJs('/' . Cmf_Module_Cache::getModulePath('plupload') . 'js/plupload.js');
    $js->addJs('/' . Cmf_Module_Cache::getModulePath('plupload') . 'js/plupload.html5.js');
    $js->addJs('/' . Cmf_Module_Cache::getModulePath('plupload') . 'js/plupload.html4.js');
    $js->addJs('/' . Cmf_Module_Cache::getModulePath('plupload') . 'js/plupload.init.js');
    $js->addJs('/' . Cmf_Module_Cache::getModulePath('admin_file_manager') . 'js/admin_file_manager.js');
    $js->addJs('/' . Cmf_Module_Cache::getModulePath('admin_products') . 'js/admin_products.js');

    View_Data::setValue('body', 'js', $js);
  }

  private static function _handleProductFormPostBack () {
    //echo '<pre>';
    //var_dump($_POST);
    //echo '</pre>';
    self::_showProductForm();
  }

  private static function _subAction () {
    if (isset(self::$_arguments['sub_action']) == TRUE) {
      return self::$_arguments['sub_action'];
    }
    return '';
  }

  // manage product types
  public static function types () {
    Admin_Controller::shared();

    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_product_types' . PHP_EXT;

    View_Data::setValue('content', $content);
  }

  // manage product attributes
  public static function attributes () {
    Admin_Controller::shared();
  }

  // creates an image preview for the file upload
  public static function imagePreview () {
    $fileUploads = array();
    $tempImagePath = '';
    $ext = '';

    Cmf_Template_Engine::setMasterTemplate('profiles/shared/views/empty' . PHP_EXT);

    $uploaderId = Request::queryString('uploader_id');
    $fileId = Request::queryString('file_id');

    if ($uploaderId == '' || $fileId == '') {
      self::_resizeImage('', '', 100, 100);
    }

    if (Session::valueExists('file_uploads')) {
      $fileUploads = unserialize(Session::getValue('file_uploads'));
    }

    if (isset($fileUploads[$uploaderId][$fileId]['stored_file_name'])) {
      $tempImagePath = Config::getValue('site', 'storage', 'temp', 'path') . $fileUploads[$uploaderId][$fileId]['stored_file_name'];
    }

    if (isset($fileUploads[$uploaderId][$fileId]['original_file_name'])) {
      $ext = strtolower(substr(strrchr($fileUploads[$uploaderId][$fileId]['original_file_name'], '.'), 1));
    }

    if (($tempImagePath != '' && is_file($tempImagePath) == FALSE) || $ext == '') {
      $tempImagePath = '';
    }

    if ($tempImagePath == '') {
      self::_resizeImage('', '', 100, 100);
    }

    self::_resizeImage($tempImagePath, $ext, 100, 100);
  }

  // xxx this whole thing needs moving to its own function
  private static function _resizeImage ($szSource, $szExtension, $iResizeWidth, $iResizeHeight) {
    switch ($szExtension) {
      case 'jpg':
      case 'jpg':
        $pSource = imagecreatefromjpeg($szSource);
        break;

      case 'gif':
        $pSource = imagecreatefromgif($szSource);
        break;

      case 'png':
        $pSource = imagecreatefrompng($szSource);
        break;

      default:
        // xxx get no preview available image
        $szExtension = 'png';
        // xxx tmp exit
        exit;
    }

    // work out the new size
    list($iWidth, $iHeight) = getimagesize($szSource);
    $ratio = max($iResizeWidth/$iWidth, $iResizeHeight/$iHeight);
    $iHeight = $iResizeHeight / $ratio;
    $x = ($iWidth - $iResizeWidth / $ratio) / 2;
    $iWidth = $iResizeWidth / $ratio;

    // copy the image for so that we can resample it
    $pResized = imagecreatetruecolor($iResizeWidth, $iResizeHeight);

    // enable transparency
    if ($szExtension == 'gif' || $szExtension == 'png') {
      imagecolortransparent($pResized, imagecolorallocatealpha($pResized, 0, 0, 0, 127));
      imagealphablending($pResized, FALSE);
      imagesavealpha($pResized, TRUE);
    }

    // resize and crop the image
    if (!imagecopyresampled($pResized, $pSource, 0, 0, $x, 0, $iResizeWidth, $iResizeHeight, $iWidth, $iHeight)) {
      // to avoid a loop, if there was an error here we just exit
      exit;
    }

    // output the image
    switch ($szExtension) {
      case 'jpg':
      case 'jpeg':
        header('content-type: image/jpeg');
        imagejpeg($pResized);
        break;

      case 'gif':
        header('content-type: image/gif');
        imagegif($pResized);
        break;

      case 'png':
        header('content-type: image/png');
        imagepng($pResized);
        break;
    }

    exit;
  }
}

?>