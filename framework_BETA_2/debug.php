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

class Debug {
  private static $_log = array();
  private static $_logEnabled = NULL;

	public static function vars ($var) {
		$variables = func_get_args();
		$output = array();
		foreach ($variables as $var) {
			$output[] = Debug::_dumpHelper($var, 1024);
		}
		return '<pre class="debug">' . implode("\n", $output) . '</pre>';
	}

	public static function dump($var, $length = 128) {
		return Debug::_dumpHelper($var, $length);
	}

	protected static function _dumpHelper(&$var, $length = 128, $level = 0) {
		if ($var === NULL) {
			return 'NULL';
		}
		elseif (is_bool($var) == TRUE) {
			return 'bool ' . ($var ? 'TRUE' : 'FALSE');
		}
		elseif (is_float($var) == TRUE) {
			return 'float ' . $var;
		}
		elseif (is_resource($var) == TRUE) {
			if (($type = get_resource_type($var)) === 'stream' && $meta = stream_get_meta_data($var)) {
				if (isset($meta['uri']) == TRUE) {
					$file = $meta['uri'];

					if (function_exists('stream_is_local') == TRUE) {
						if (stream_is_local($file) == TRUE) {
							$file = Path::normalisePath($file);
						}
					}

					return 'resource<span>(' . $type . ')</span> ' . htmlspecialchars($file, ENT_NOQUOTES);
				}
			}
			else {
				return 'resource<span>(' . $type . ')</span>';
			}
		}
		elseif (is_string($var) == TRUE) {
			$str = htmlspecialchars($var, ENT_NOQUOTES);
			return 'string<span>('.strlen($var).')</span> " '. $str . '"';
		}
		elseif (is_array($var) == TRUE) {
			$output = array();

			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);

			static $marker;

			if ($marker === NULL) {
				// Make a unique marker
				$marker = uniqid("\x00");
			}

			if (empty($var) == FALSE) {
        if (isset($var[$marker]) == TRUE) {
  				$output[] = "(\n$space$s*RECURSION*\n$space)";
  			}
  			elseif ($level < 5) {
  				$output[] = "<span>(";

  				$var[$marker] = TRUE;
  				foreach ($var as $key => &$val) {
  					if ($key === $marker) continue;
  					if (is_int($key) == FALSE) {
  						$key = '"' . htmlspecialchars($key, ENT_NOQUOTES) . '"';
  					}

  					$output[] = "$space$s$key => " . Debug::_dumpHelper($val, $length, $level + 1);
  				}
  				unset($var[$marker]);

  				$output[] = "$space)</span>";
  			}
  			else {
  				// Depth too great
  				$output[] = "(\n$space$s...\n$space)";
  			}
      }

			return 'array<span>(' . count($var) . ')</span> ' . implode("\n", $output);
		}
		elseif (is_object($var) == TRUE) {
			// Copy the object as an array
			$array = (array) $var;

			$output = array();

			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);

			$hash = spl_object_hash($var);

			// Objects that are being dumped
			static $objects = array();

			if (empty($var) == FALSE) {
  			if (isset($objects[$hash])) {
  				$output[] = "{\n$space$s*RECURSION*\n$space}";
  			}
  			elseif ($level < 10) {
  				$output[] = "<code>{";

  				$objects[$hash] = TRUE;
  				foreach ($array as $key => & $val) {
  					if ($key[0] === "\x00") {
  						// Determine if the access is protected or protected
  						$access = (($key[1] === '*') ? 'protected' : 'private');

  						// Remove the access level from the variable name
  						$key = substr($key, strrpos($key, "\x00") + 1);
  					}
  					else {
  						$access = 'public';
  					}

  					$output[] = "$space$s$access $key => " . Debug::_dumpHelper($val, $length, $level + 1);
  				}
  				unset($objects[$hash]);

  				$output[] = "$space}</code>";
  			}
  			else {
  				// Depth too great
  				$output[] = "{\n$space$s...\n$space}";
  			}
      }

			return 'object <span>' . get_class($var) . '(' . count($array) . ')</span> ' . implode("\n", $output);
		}
		else {
			return gettype($var) . htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES);
		}

    return '';
	}

  /**
   * Force Debug whether or not to log
   * @param $enabled bool logging enabled if TRUE or disabled if FALSE
   * @return bool the original value
   */
  public static function logEnabled ($enabled) {
    self::$_logEnabled ^= $enabled ^= self::$_logEnabled ^= $enabled;
    return $enabled;
  }

  public static function logMessage ($title, $message) {
    // If FALSE don't log
    if (self::$_logEnabled === FALSE) {
      return;
    }

    // If NULL then keep checking to see if the value changes as this value would normally be set
    if (self::$_logEnabled === NULL) {
      $inProduction = Config::getValue('site', 'inProduction');
      if ($inProduction !== NULL) {
        self::$_logEnabled = ($inProduction == FALSE);
      }
    }

    // If TRUE then log
    if (self::$_logEnabled == TRUE) {
      self::$_log[] = array('title' => $title, 'message' => $message);
    }
  }

  public static function getLog () {
    if (self::$_logEnabled === FALSE) {
      return array();
    }

    // If NULL then keep checking to see if the value changes as this value would normally be set
    if (self::$_logEnabled === NULL) {
      $inProduction = Config::getValue('site', 'inProduction');
      if ($inProduction !== NULL) {
        self::$_logEnabled = ($inProduction == FALSE);
      }
    }

    if (self::$_logEnabled == TRUE) {
      return self::$_log;
    }

    return array();
  }
}

?>