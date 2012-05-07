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

class Cmf_Route_Collection {
  private $_routes = array();

  // maps a route to a route handler
  public function addRoute (Cmf_Route $route) {
    $this->_routes[$route->getName()] = $route;
  }

  public function ignoreRoute ($url) {
    // xxx implement
  }

  public function getRoute ($routeName) {
    Assert::isString($routeName);

    if (array_key_exists($routeName, $this->_routes) == FALSE) {
      throw new Argument_Exception("The route '{$routeName}' does not exist");
    }

    return $this->_routes[$routeName];
  }

  public function removeRoute ($routeName) {
    Assert::isString($routeName);

    // Even if the route was set to NULL, it should still be removed so we check to make sure the key exists regardless of it's value
    if (array_key_exists($routeName, $this->_routes) == FALSE) {
      throw new Argument_Exception("The route '{$routeName}' does not exist");
    }

    unset($this->_routes[$routeName]);
  }

  public function getAllRoutes () {
    return $this->_routes;
  }
}

?>