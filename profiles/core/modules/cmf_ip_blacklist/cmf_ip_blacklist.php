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
 * This module can be disabled if a firewall is already in place that has been configured to
 * block IP addresses
 */
class Cmf_Ip_Blacklist {
  const Prepared_Statement_Library = 'cmf_ip_blacklist_prepared_statement_library';
  
  static public function install () {
    Config::setValue(CMF_REGISTRY, 'security', 'ip_blacklist', 'transparent', FALSE);
    Config::setValue(CMF_REGISTRY, 'security', 'ip_blacklist', 'reverse_proxy', FALSE);
    Config::setValue(CMF_REGISTRY, 'security', 'ip_blacklist', 'reverse_proxy_addresses', '');
  }

  static public function filterRequest () {
    // If we are not transparently blocking IP's, deal with the block in this module
    if (Config::getValue('security', 'ip_blacklist', 'transparent') == FALSE) {
      self::_forbidVisitor(self::_visitorIpAddress());
    }
  }

  static private function _forbidVisitor ($ipAddress) {
    if (self::isBlacklisted($ipAddress) == TRUE) {
      Response_Buffer::addContent('Sorry, the IP address ' . self::_visitorIpAddress() . ' has been banned.');
      Response_Buffer::setStatusCode(403);
      Response_Buffer::flush();
      Response::end();
    }
  }

  static public function isBlacklisted ($ipAddress) {
    if (Environment::isCommandLine() == TRUE) {
      return FALSE;
    }

    $query = Cmf_Database::call('cmf_ip_blacklist_get_untrusted', self::Prepared_Statement_Library);
    $query->bindValue(':ip_address', $ipAddress);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    if (intval($query->fetchColumn()) > 0) {
      return TRUE;
    }

    return FALSE;
  }

  static private function _visitorIpAddress () {
    static $ipAddress;

    if (isset($ipAddress) == FALSE) {
      if (Environment::isCommandLine() == FALSE) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
      }
      else {
        $ipAddress = '127.0.0.1';
      }

      if (Config::getValue('security', 'ip_blacklist', 'reverse_proxy') == TRUE) {
        $reverseProxyHeader = Config::getValue('security', 'ip_blacklist', 'reverse_proxy_header');

        if (empty($_SERVER[$reverseProxyHeader]) == FALSE) {
          // If an array of known reverse proxy IPs is provided, then trust
          // the XFF header if request really comes from one of them.
          $reverseProxyAddresses = Config::getValue('security', 'ip_blacklist', 'reverse_proxy_addresses');

          if ($reverseProxyAddresses == NULL) {
            $reverseProxyAddresses = array();
          }

          // Turn XFF header into an array.
          $forwarded = explode(',', $_SERVER[$reverseProxyHeader]);

          // Trim the forwarded IPs; they may have been delimited by commas and spaces.
          $forwarded = array_map('trim', $forwarded);

          // Tack direct client IP onto end of forwarded array.
          $forwarded[] = $ipAddress;

          // Eliminate all trusted IPs.
          $untrusted = array_diff($forwarded, $reverseProxyAddresses);

          // The right-most IP is the most specific we can trust.
          $ipAddress = array_pop($untrusted);
        }
      }
    }

    return $ipAddress;
  }
}

?>