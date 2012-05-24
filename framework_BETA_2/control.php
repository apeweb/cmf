<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx remove id and cssClass from control and move them to a Html_Element_Control as they no longer belong here
abstract class Control {
  public $id = ''; // The id of the control
  public $cssClass = ''; // The CSS classes that apply to this control

  public $weight = 0; // The order of which the item should appear

  public $preRenderCallbacks = array(); // What callbacks should be called to manipulate the data before it is rendered
  public $renderCallback = ''; // What callback should be called to convert the data into HTML
  public $postRenderCallbacks = array(); // What callbacks should be called to manipulate the HTML
  public $wrapperCallbacks = array(); // What callbacks should be called to wrap additional HTML

  public $children = array(); // Child controls

  protected $_processed = FALSE; // Whether the data has been converted into HTML

  public $_prefix = '';
  public $_content = '';
  public $_postfix = '';

  protected $_timeRendered = 0; // How many times has the control been rendered
  protected $_timesToRender = 1; // How many times should the control be rendered

  public function setPrefix ($prefix) {
    $this->_prefix = $prefix;
  }

  public function getPrefix () {
    return $this->_prefix;
  }

  public function setContent ($content) {
    $this->_content = $content;
  }

  public function getContent () {
    return $this->_content;
  }

  public function setPostfix ($postfix) {
    $this->_postfix = $postfix;
  }

  public function getPostfix () {
    return $this->_postfix;
  }

  public function __toString () {
    if ($this->_timesToRender == $this->_timeRendered) {
      // xxx throw new exception
      return NULL;
    }

    if ($this->isProcessed() == FALSE) {
      $this->process();
    }

    ++$this->_timeRendered;

    return $this->_content;
  }

  public function isProcessed () {
    return $this->_processed;
  }

  public function process ($reprocess = FALSE) {
    if ($this->isProcessed() == TRUE && $reprocess == FALSE) {
      // xxx throw new exception
    }

    $this->_content = '';

    // xxx check if $this->preRenderCallbacks is an array
    foreach ($this->preRenderCallbacks as $callback) {
      call_user_func($callback, $this);
    }

    // This should call what needs to render the children if they should be rendered
    call_user_func($this->renderCallback, $this);

    // xxx check if $this->postRenderCallbacks is an array
    foreach ($this->postRenderCallbacks as $callback) {
      call_user_func($callback, $this);
    }
    
    // xxx check if $this->wrapperCallbacks is an array
    foreach ($this->wrapperCallbacks as $callback) {
      call_user_func($callback, $this);
    }

    $this->_processed = TRUE;

    return $this;
  }
}

?>