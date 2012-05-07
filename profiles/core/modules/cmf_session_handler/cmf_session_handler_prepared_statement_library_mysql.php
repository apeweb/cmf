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

// xxx add support for s_id

class Cmf_Session_Handler_Prepared_Statement_Library_Mysql {
  const CMF_SESSION_GET_DATA = "
    SELECT session_data
      FROM cmf_session
      WHERE session_token = :session_token
      LIMIT 1
  ";

  const CMF_SESSION_INSERT = "
    INSERT INTO cmf_session
      (session_token, session_expires)
      VALUES
      (:session_token, :session_expires);
  ";

  const CMF_SESSION_UPDATE = "
    UPDATE cmf_session
      SET session_expires = :session_expires,
          session_data = :session_data
      WHERE session_token = :session_token
      LIMIT 1
  ";

  const CMF_SESSION_UPDATE_TOKEN = "
    UPDATE cmf_session
      SET session_token = :new_session_token
      WHERE session_token = :old_session_token
      LIMIT 1
  ";

  const CMF_SESSION_DELETE = "
    DELETE FROM cmf_session
      WHERE session_token = :session_token
      LIMIT 1
  ";

  const CMF_SESSION_PURGE = "
    DELETE FROM cmf_session
      WHERE session_expires < :now
  ";

  const CMF_SESSION_GET_TOKEN_COUNT = "
    SELECT COUNT(*), UUID()
      FROM cmf_session
      WHERE session_token = :session_token
      LIMIT 1
  ";
}

?>
