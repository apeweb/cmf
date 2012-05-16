<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Backtrace {
  public static function summary ($provideObject = TRUE) {
    $backtrace = debug_backtrace($provideObject);
    $printBacktrace = array();

    array_shift($backtrace);

    foreach ($backtrace as $traceId => $trace) {
      $printBacktrace[] = sprintf('#%d %s called at [%s:%d]', $traceId, $trace['function'], $trace['file'], $trace['line']);
    }

    return $printBacktrace;
  }

  /**
   * Method provided to be extended in future versions
   */
  public static function extended ($provideObject = TRUE) {
    $backtrace = debug_backtrace($provideObject);

    array_shift($backtrace);

    return $backtrace;
  }

	public static function source ($file, $lineNumber, $padding = 5) {
		if ($file == '' || is_readable($file) == FALSE) {
      return '';
		}

		// Open the file and set the line position
		$file = fopen($file, 'r');
    if ($file == FALSE) {
      return '';
    }

		// Set the reading range
		$range = array('start' => $lineNumber - $padding, 'end' => $lineNumber + $padding);

		// Set the zero-padding amount for line numbers
		$format = '% '. strlen($range['end']) . 'd';

    $line = 0;
		$source = '';
		while (($row = fgets($file)) !== FALSE) {
			// Increment the line number
			if (++$line > $range['end'])
				break;

			if ($line >= $range['start']) {
				// Make the row safe for output
				$row = htmlspecialchars($row, ENT_NOQUOTES);

				// Trim whitespace and sanitize the row
				$row = '<span class="number">' . sprintf($format, $line) . '</span> '.$row;

				if ($lineNumber > 0 && $line == $lineNumber) {
					// Apply highlighting to this row
					$row = '<span class="line highlight">' . $row . '</span>';
				}
				else {
					$row = '<span class="line">' . $row . '</span>';
				}

				// Add to the captured source
				$source .= $row;
			}
		}

		// Close the file
		fclose($file);

		return '<pre class="source"><code>' . $source . '</code></pre>';
	}

  public static function steps (array $trace = NULL) {
		if ($trace === NULL) {
			// Start a new trace
			$trace = debug_backtrace();
      array_shift($trace);
		}

		// Non-standard function calls
		$statements = array('include', 'include_once', 'require', 'require_once');

		$output = array();
		foreach ($trace as $step) {
			if (isset($step['function']) == FALSE) {
				// Invalid trace step
				continue;
			}

			if (isset($step['file']) == TRUE && isset($step['line']) == TRUE) {
				// Include the source of this step
				$source = Backtrace::source($step['file'], $step['line']);
			}

			if (isset($step['file']) == TRUE) {
				$file = $step['file'];

				if (isset($step['line'])) {
					$line = $step['line'];
				}
			}

			// function()
			$function = $step['function'];

			if (in_array($step['function'], $statements)) {
				if (empty($step['args']) == TRUE) {
					// No arguments
					$args = array();
				}
				else {
					// Sanitize the file path
					$args = array($step['args'][0]);
				}
			}
			elseif (isset($step['args']) == TRUE) {
				if (function_exists($step['function']) == FALSE || strpos($step['function'], '{closure}') !== FALSE) {
					// Introspection on closures or language constructs in a stack trace is impossible
					$params = NULL;
				}
				else {
					if (isset($step['class']) == TRUE) {
						if (method_exists($step['class'], $step['function'])) {
							$reflection = new ReflectionMethod($step['class'], $step['function']);
						}
						else {
							$reflection = new ReflectionMethod($step['class'], '__call');
						}
					}
					else {
						$reflection = new ReflectionFunction($step['function']);
					}

					// Get the function parameters
					$params = $reflection->getParameters();
				}

				$args = array();

				foreach ($step['args'] as $i => $arg) {
					if (isset($params[$i])) {
						// Assign the argument by the parameter name
						$args[$params[$i]->name] = $arg;
					}
					else {
						// Assign the argument by number
						$args[$i] = $arg;
					}
				}
			}

			if (isset($step['class']) == TRUE) {
				// Class->method() or Class::method()
				$function = $step['class'] . $step['type'] . $step['function'];
			}

			$output[] = array(
				'function' => $function,
				'args' => isset($args) ? $args : NULL,
				'file' => isset($file) ? $file : NULL,
				'line' => isset($line) ? $line : NULL,
				'source' => isset($source) ? $source : NULL,
			);

			unset($function, $args, $file, $line, $source);
		}

		return $output;
	}
}

?>