<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Session_Handler_Prepared_Statement_Library_Mysql {
  const CMF_SESSION_GET_DATA = "
    SELECT session_data
      FROM cmf_session
      WHERE session_token = :session_token
      AND session_uuid = :session_uuid
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_SESSION_INSERT = "
    INSERT INTO cmf_session
      (session_token, session_uuid, session_expires, session_data, s_id)
      VALUES
      (:session_token, :session_uuid, :session_expires, :session_data, :s_id);
  ";

  const CMF_SESSION_UPDATE = "
    UPDATE cmf_session
      SET session_expires = :session_expires,
          session_data = :session_data
      WHERE session_token = :session_token
      AND session_uuid = :session_uuid
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_SESSION_DELETE = "
    DELETE FROM cmf_session
      WHERE session_token = :session_token
      AND session_uuid = :session_uuid
      AND s_id = :s_id
      LIMIT 1
  ";

  const CMF_SESSION_PURGE = "
    DELETE FROM cmf_session
      WHERE session_expires < :now
      AND s_id = :s_id
  ";

  // xxx move to a function or something
  const CMF_SESSION_GET_UUID = "
    SELECT UUID()
      LIMIT 1
  ";
}

?>
