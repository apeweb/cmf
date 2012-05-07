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

class Array_Helper {
  /**
   * Takes a flat array and makes it hierarchical like so:
   * array('parent', 'child', 'childOfChild', 'childOfChildValue');
   * to:
   * array(
   *   'parent' => array(
   *     'child' => array(
   *       'childOfChild' => 'childOfChildValue'
   *     )
   *   )
   */
  static public function flatToTree ($flat) {
    $tree = array();
    $temp =& $tree;
    $levels = count($flat);

    if ($levels == 1) {
      return $flat;
    }

    foreach ($flat as $item) {
      --$levels;
      if ($levels > 0) {
        $temp[$item] = array();
        $temp =& $temp[$item];
      }
      else {
        $temp = $item;
      }
    }

    return $tree;
  }

  static function array_merge_recursive_simple () {
    if (func_num_args() < 2) {
      throw new InvalidArgumentException('Two or more arguments are required');
    }

    $arrays = func_get_args();
    $merged = array();

    while ($arrays) {
      $array = array_shift($arrays);

      if (is_array($array) == FALSE) {
        throw new InvalidArgumentException('Cannot merge non array argument');
      }

      if ($array == FALSE) {
        continue;
      }

      foreach ($array as $key => $value) {
        if (is_string($key) == TRUE) {
          if (is_array($value) == TRUE && array_key_exists($key, $merged) == TRUE && is_array($merged[$key]) == TRUE) {
            $merged[$key] = self::array_merge_recursive_simple($merged[$key], $value);
          }
          else {
            $merged[$key] = $value;
          }
        }
        else {
          $merged[] = $value;
        }
      }
    }

    return $merged;
  }
}

?>