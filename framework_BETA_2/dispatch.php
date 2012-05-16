<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx need to look into what dispatcher does

// does a request, as if somebody had typed the address in the browser
$dispatcher = new Request_Dispatcher;
$dispatcher->startPoint = new Request('/contact-us/map/');
$dispatcher->getRoute();

?>