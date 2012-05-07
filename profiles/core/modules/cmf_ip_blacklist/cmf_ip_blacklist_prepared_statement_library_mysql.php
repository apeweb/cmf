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

define('Cmf_Ip_Blacklist_Prepared_Statement_Library_Mysql', 'Cmf_Ip_Blacklist_Prepared_Statement_Library_Mysql');
class Cmf_Ip_Blacklist_Prepared_Statement_Library_Mysql {
  const CMF_IP_BLACKLIST_GET_UNTRUSTED = "
    SELECT COUNT(*)
      FROM cmf_ip_table
      WHERE ip_address = INET_ATON(:ip_address)
      AND ip_trusted = '0'
      AND ip_banned = '1'
      AND ip_active = '1'
      AND ip_deleted = '0'
      AND s_id = :s_id
      LIMIT 1
  ";

  // xxx add

  // xxx delete

  // xxx mark inactive

  // xxx mark active

  // xxx mark trusted

  // xxx mark untrusted

  // xxx mark banned

  // xxx mark unbanned
}

?>
