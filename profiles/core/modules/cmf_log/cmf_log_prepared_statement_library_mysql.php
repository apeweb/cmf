<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Log_Prepared_Statement_Library_Mysql {
    const CMF_LOG_ADD = "
    INSERT INTO cmf_system_log
      (s_id, log_title, log_message, log_type, log_level, log_dump)
      VALUES
      (:s_id, :log_title, :log_message, :log_type, :log_level, :log_dump)
  ";

  const CMF_LOG_GET_ALL = "
    SELECT *
      FROM cmf_system_log
      WHERE s_id = :s_id
      ORDER BY log_date DESC
  ";

  const CMF_LOG_GET = "
    SELECT *
      FROM cmf_system_log
      WHERE s_id = :s_id
      ORDER BY log_date DESC
      LIMIT :limit
  ";
}

?>
