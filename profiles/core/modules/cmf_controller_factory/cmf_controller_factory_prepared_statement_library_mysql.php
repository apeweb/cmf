<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Controller_Factory_Prepared_Statement_Library_Mysql {
  const CMF_CONTROLLER_GET_PATH = "
    SELECT cc_path
      FROM cmf_controllers_cache
      WHERE cc_name = :cc_name
      AND s_id = :s_id
  ";
}

?>