<?php

/**
 * Copyright (c) 2011 Ape Web Ltd.  All rights reserved.
 *
 * The use and distribution terms for this software are contained in the file
 * named license.txt, which can be found in the root of this distribution.
 * By using this software in any fashion, you are agreeing to be bound by the
 * terms of this license.
 *
 * You must not remove this notice, or any other, from this software.
 */

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

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

  protected $_prefix = '';
  protected $_content = '';
  protected $_postfix = '';

  protected $_timeRendered = 0; // How many times has the control been rendered
  protected $_timesToRender = 1; // How many times should the control be rendered

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