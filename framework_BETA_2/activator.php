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

/***************************

DO NOT DELETE, PART OF THE MVC THAT WILL BE WORKED ON

***********************/




// needs to find files

// see http://code.google.com/p/kohana-mptt/source/browse/trunk/Dispatch.php
// and http://forum.kohanaframework.org/discussion/88/how-do-you-call-a-controller-method-in-a-view/p1

// dispatch is the wrong word used, should be activator
//$article_list=Activator::controller('article')->method('list',5);
//$article_list=Activator::controller('article')->method('list',array(5,'last');

/**
 * Syntax should be 
 * $articleList = Dispatch::controller('article');
 * $articleList->list(5, 'last');
 */

// needs to find files

class Activator {
  static public function createInstance ($controller, $action) {
    echo $controller, '/' . $action;
    exit;
  }
}

?>
