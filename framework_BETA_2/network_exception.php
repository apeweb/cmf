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

class Network_Exception extends Base_Exception {}

    /**
     * If a 404 is triggered then the application will take over things and
     * display a 404 page, this should be moved to somewhere more appropriate,
     * maybe in the exception handler?
     * throw new Network_Error(Network_Error::Page_Not_Found); // shows 404 page? and runs Network_Error::notifyObservers(Network_Error::Page_Not_Found);
     * need to move the renderPageNotFoundPage to somewhere else too
     */
    //Network_Error::attachObserver(Network_Error::Page_Not_Found, 'Application::renderPageNotFoundPage');

?>