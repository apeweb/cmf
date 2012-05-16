<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Sort_Order extends Enum {
  const null = 0; // no sort order
  const regular = 1; // default sort order
  const natural = 2; // natcasesort
  const string = 3; // as sort's SORT_REGULAR would
  const inherit = 4; // the system/module's preference or the parent sort order used
}

?>