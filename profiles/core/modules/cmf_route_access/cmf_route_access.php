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

class Cmf_Route_Access {
  // xxx need a permissions module that determines whether the user has the rights to access the route, if not it can
  // xxx always then tell the route to redirect the user to a permission denied page

  // xxx use Cmf_Route_Event::filterRoutes to filter the routes the user has access to which will ultimately cause
  // a 404 to show or a different route

  // xxx need a module to handle downloads, PDF's etc, if a download then load the module to handle that download as it
  // xxx may just want to stop executing afterwards, by doing so saves processing
}

?>