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

/**
 * LOG_EMERG 	(0) system is unusable
 * LOG_ALERT 	action must be taken immediately
 * LOG_CRIT 	critical conditions
 * LOG_ERR 	(3) error conditions
 * LOG_WARNING 	(4) warning conditions
 * LOG_NOTICE 	normal, but significant, condition
 * LOG_INFO 	(6) informational message
 * LOG_DEBUG 	debug-level message
 */
class Log_Level extends Enum {
  const information = LOG_INFO; // On Windows systems, this is represented as a message of the information type in the event log.
  const warning = LOG_WARNING; // On Windows systems, this is represented as a message of the warning type in the event log.
  const error = LOG_ERR; // On Windows systems, this is represented as a message of the warning type in the event log.
  const attack = LOG_EMERG; // On Windows systems, this is represented as a message of the error type in the event log.
}

?>