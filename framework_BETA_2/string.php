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
 * Provides a long list of string manipulation methods
 */
class String {
  public static function asRegex ($string, $caseSensitive = TRUE) {
    $regex = '#^' . preg_quote($string, '#') . '$#';
    if ($caseSensitive == FALSE) {
      $regex .= 'i';
    }
    return $regex;
  }

  /**
   * Replace one string (or part of a string) with another
   */
  public static function replace ($search, $replace, $subject, &$count) {
    return str_replace($search, $replace, $subject, $count);
  }

  /**
   * Covert all newline characters to a replacement string
   */
  public static function nl2br ($string, $replacement = '<br />') {
    $transformations = array(
      "\r\n" => $replacement,
      "\r" => $replacement,
      "\n" => $replacement
    );
    return self::transform($string, $transformations);
  }

  /**
   * Pass an array of transformations to take place
   */
  public static function transform ($string, $transformations) {
    if (is_array($transformations) == FALSE) {
      throw new InvalidArgumentException();
    }
    return strtr($str, $transformations);
  }

  /**
   * Convert an array to string
   */
  public static function implode ($glue, $pieces) {
    return implode($glue, $pieces);
  }

  public static function toBool ($string) {
    if (trim(strtolower($string)) == 'true') {
      return TRUE;
    }
    elseif (trim(strtolower($string)) == 'false') {
      return FALSE;
    }
    elseif (trim(trim(strtolower($string)), '0') == '.') { // ie 000000.0000000
      return FALSE;
    }

    return (bool) $string;
  }

  // see http://php.net/manual/en/function.uniqid.php
//  public static function uuid() {
//
//        $pr_bits = false;
//        if (is_a ( $this, 'uuid' )) {
//            if (is_resource ( $this->urand )) {
//                $pr_bits .= @fread ( $this->urand, 16 );
//            }
//        }
//        if (! $pr_bits) {
//            $fp = @fopen ( '/dev/urandom', 'rb' );
//            if ($fp !== false) {
//                $pr_bits .= @fread ( $fp, 16 );
//                @fclose ( $fp );
//            } else {
//                // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
//                $pr_bits = "";
//                for($cnt = 0; $cnt < 16; $cnt ++) {
//                    $pr_bits .= chr ( mt_rand ( 0, 255 ) );
//                }
//            }
//        }
//        $time_low = bin2hex ( substr ( $pr_bits, 0, 4 ) );
//        $time_mid = bin2hex ( substr ( $pr_bits, 4, 2 ) );
//        $time_hi_and_version = bin2hex ( substr ( $pr_bits, 6, 2 ) );
//        $clock_seq_hi_and_reserved = bin2hex ( substr ( $pr_bits, 8, 2 ) );
//        $node = bin2hex ( substr ( $pr_bits, 10, 6 ) );
//
//        /**
//         * Set the four most significant bits (bits 12 through 15) of the
//         * time_hi_and_version field to the 4-bit version number from
//         * Section 4.1.3.
//         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
//         */
//        $time_hi_and_version = hexdec ( $time_hi_and_version );
//        $time_hi_and_version = $time_hi_and_version >> 4;
//        $time_hi_and_version = $time_hi_and_version | 0x4000;
//
//        /**
//         * Set the two most significant bits (bits 6 and 7) of the
//         * clock_seq_hi_and_reserved to zero and one, respectively.
//         */
//        $clock_seq_hi_and_reserved = hexdec ( $clock_seq_hi_and_reserved );
//        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
//        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;
//
//        return sprintf ( '%08s-%04s-%04x-%04x-%012s', $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node );
//    }
}

?>