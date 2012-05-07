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

define('Route_Collection', 'Route_Collection');
class Route_Collection {
	protected static $_routes = array();

	public static function mapRoute ($name, $url, array $patterns = array(), array $defaultValues = array()) {
    $route = new Route($url);
    $route->constraints($patterns);
    $route->defaults($defaultValues);
		Route_Collection::$_routes[$name] = $route;
	}

	public static function getRoute ($name) {
		if (array_key_exists($name, self::$_routes) == FALSE) {
			// xxx throw exception
		}

		return Route_Collection::$_routes[$name];
	}

	public static function getAllRoutes () {
		return Route::$_routes;
	}

  // returns all matching routes that adhrere to the ignored routes
  public static function getAllMatchingRoutes () {

  }

  // returns the first matching route
  public static function getMatchingRoute () {

  }

	public static function getRouteName (Route $route) {
		return array_search($route, Route_Collection::$_routes);
	}

  public static function ignoreRoute () {

  }
}

?>