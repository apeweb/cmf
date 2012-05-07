<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

define('Template_Controller', 'Template_Controller');
abstract class Template_Controller extends Controller {
  protected $_page = '';
  protected $_autoRender = TRUE;

  public function __construct () {
    parent::__construct();

    $this->_page = new View($this->_page);

    if ($this->_autoRender == TRUE) {
			Application::attachObserver(Application::terminate(), array($this, 'render'));
		}
  }

  public function render () {
		Response_Buffer::buffer($this->_page);
    Response_Buffer::flush();
	}
}

?>