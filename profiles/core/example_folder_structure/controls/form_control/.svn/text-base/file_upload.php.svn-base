<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

require_once(ROOT . 'framework/control/form_control/abstract.php');
require_once(ROOT . 'framework/file/attribute.php');
require_once(ROOT . 'framework/network.php');

define('File_Upload', NULL);

class File_Upload extends Form_Control {
  #Region "protected variables"
  // the original name of the file on the client machine
  protected $_fileName = '';

  // the file attributes
  protected $_attribute = NULL;

  // input name
  protected $_name = '';

  // whether a file was uploaded successfully
  protected $_hasFile = FALSE;

  // <input/> element attributes
  protected $_disabled = FALSE;
  protected $_readOnly = FALSE;
  #End Region

  #Region "private variables"
  private $_request = Request;
  #End Region

  #Region "magic methods"
  public function __construct () {
    $this->_request = new Request;
  }

  public function __get ($variableName) {
    switch ($variableName) {
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip': // title attribute

      // i18n attributes
      case 'lang':
      case 'direction':

      // framework attributes
      case 'enabled':

      case 'accessKey':
      case 'readOnly':
      case 'disabled':
      case 'tabIndex':
      case 'fileName':
      case 'attribute':
      case 'name':
        return $this->{'_' . $variableName};

      case 'hasFile':
        return $this->hasFile();

      default:
        // legacy error reporting
        trigger_error("Undefined variable: '" . $variableName . "()'", E_USER_NOTICE);
    }
  }

  public function __call ($methodName, $arguments) {
    switch ($methodName) {
      // basic attributes
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip':

      // i18n attributes
      case 'lang':
      case 'direction':

      // framework attributes
      case 'enabled':

      case 'name':
      case 'accessKey':
      case 'readOnly':
      case 'disabled':
      case 'tabIndex':
      case 'fileName':
      case 'attribute':
        if (count($arguments) > 0) {
          throw new Exception ("Call must assign a value to '" . $methodName . "()' or use its value.");
        }

        return $this->{'_' . $methodName};

      default:
        // legacy error reporting
        trigger_error("Call to undefined function '" . $methodName . "()'", E_USER_ERROR);
    }
  }

  public function __set ($variableName, $value) {
    switch ($variableName) {
      // basic attributes
      case 'cssClass':
      case 'id':
      case 'style':
      case 'toolTip':

      // i18n attributes
      case 'lang':
      case 'direction':

      // html attributes
      case 'name':
      case 'accessKey':
        if (is_object($value) == FALSE && is_array($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'string' in '" . $variableName . "' is not valid.");
        }

      // framework attributes
      case 'enabled':

      // html attributes
      case 'readOnly':
      case 'disabled':
        if (is_bool($value) == TRUE || (is_numeric($value) == TRUE && is_string($value) == FALSE)) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'boolean' in '" . $variableName . "' is not valid.");
        }

      // html attributes
      case 'tabIndex':
        if (is_numeric($value) == TRUE && is_string($value) == FALSE) {
          $this->{'_' . $variableName} = $value;
          return;
        }
        else {
          throw new Exception ("Conversion from '" . gettype($value) . "' to type 'integer' in '" . $variableName . "' is not valid.");
        }

      // read only variables
      case 'fileName':
      case 'attribute':
      case 'hasFile':
        throw new Exception ("Property '" . $variableName . "' is read only.");

      default:
        // legacy error reporting
        throw new Exception ("'" . $variableName . "' is not a member of '" . __CLASS__ . "'");
    }
  }

  public function __toString () {
    $this->_build();
    return $this->_control;
  }
  #End Region

  #Region "protected methods"
  protected function _build () {
    if ($this->_enabled == TRUE) {
      $this->_control = '<input type="file"';

      // core attibutes
      if (trim($this->_cssClass) != '') {
        $this->_control .= ' class="' . $this->_cssClass . '"';
      }
      if (trim($this->_id) != '') {
        $this->_control .= ' id="' . $this->_id . '"';
      }
      if (trim($this->_style) != '') {
        $this->_control .= ' style="' . $this->_style . '"';
      }
      if (trim($this->_toolTip) != '') {
        $this->_control .= ' title="' . $this->_toolTip . '"';
      }

      // i18n attributes
      if (trim($this->_lang) != '') {
        $this->_control .= ' lang="' . $this->_lang . '" xml:lang="' . $this->_lang . '"';
      }
      if (trim($this->_direction) != '') {
        $this->_control .= ' dir="' . $this->_direction . '"';
      }

      // all others
      if (trim($this->_name) != '') {
        $this->_control .= ' name="' . $this->_name . '"';
      }

      if ($this->_disabled == TRUE) {
        $this->_control .= ' disabled="disabled"';
      }

      if ($this->_readOnly == TRUE) {
        $this->_control .= ' readonly="readonly"';
      }

      if ($this->_tabIndex > -1) {
        $this->_control .= ' tabindex="' . $this->_tabIndex . '"';
      }

      if (trim($this->_accessKey) != '') {
        $this->_control .= ' accesskey="' . $this->_accessKey . '"';
      }

      $this->_control .= ' />';
    }
  }
  #End Region

  #Region "public methods"
  public function hasFile () {
    if ($this->_request->method == 'POST' && isset($_FILES) == TRUE && isset($_FILES[$this->name]) == TRUE) {
      // check if PHP has found an error
      if ($_FILES[$this->name]['error'] !== UPLOAD_ERR_OK) {
        switch ($_FILES[$this->name]['error']) {
          case UPLOAD_ERR_INI_SIZE:
            throw new File_Upload_Error_Exception('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
            break;
          case UPLOAD_ERR_FORM_SIZE:
            throw new File_Upload_Error_Exception('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
            break;
          case UPLOAD_ERR_PARTIAL:
            throw new File_Upload_Error_Exception('The uploaded file was only partially uploaded.');
            break;
          // not really an exception
          //case UPLOAD_ERR_NO_FILE:
          //  throw new File_Upload_Error_Exception('No file was uploaded.');
          //  break;
          case UPLOAD_ERR_NO_TMP_DIR:
            throw new File_Upload_Error_Exception('Missing a temporary folder.');
            break;
          case UPLOAD_ERR_CANT_WRITE:
            throw new File_Upload_Error_Exception('Failed to write file to disk.');
            break;
          case UPLOAD_ERR_EXTENSION:
            throw new File_Upload_Error_Exception('File upload stopped by extension.');
            break;
        }
      }
      // stops empty files, or files with no filenames from being uploaded
      elseif (is_array($_FILES[$this->name]) == FALSE) {
        throw new File_Upload_Error_Exception('Failed to upload file.');
      }
      elseif (trim($_FILES[$this->name]['name']) == '') {
        throw new File_Upload_Error_Exception('The uploaded file filename is invalid.');
      }
      elseif (trim($_FILES[$this->name]['tmp_name']) == '') {
        throw new File_Upload_Error_Exception('Failed to upload file.');
      }
      elseif ($_FILES[$this->name]['size'] < 1) {
        throw new File_Upload_Error_Exception('No file was uploaded.');
      }
      else {
        $this->_hasFile = TRUE;

        $this->_fileName = $_FILES[$this->name]['name'];
        $this->_attribute = new File_Attribute;
        $this->_attribute->mimeType = $_FILES[$this->name]['type'];
        $this->_attribute->size = $_FILES[$this->name]['size'];
      }
    }

    return $this->_hasFile;
  }

  public function saveAs ($fileName) {
    if ($this->_hasFile == TRUE) {
      if (@move_uploaded_file($_FILES[$this->_name]['tmp_name'], $fileName) == TRUE) {
        return TRUE;
      }
      else {
        if (file_exists(dirname($fileName)) == FALSE) {
          throw new Directory_Not_Found_Exception ($php_errormsg);
        }
        elseif (is_writable(dirname($fileName)) == FALSE) {
          throw new Directory_Not_Writable_Exception ($php_errormsg);
        }
        elseif (file_exists($fileName) == TRUE && is_writable($fileName) == FALSE) {
          throw new File_Not_Writable_Exception ($php_errormsg);
        }
        else {
          throw new Exception ($php_errormsg);
        }
      }
    }

    return FALSE;
  }
  #End Region
}

?>