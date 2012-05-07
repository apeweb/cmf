<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * Basic benchmarking, for more advanced benchmarking a PHP extension such as
 * xdebug is recommended, don't forget that even if you include benchmarking, it is disabled
 * by default and you will need to enable it
 */
class Benchmark {
  static private $_markers = array();
  static private $_enabled = FALSE;

  static public function enable () {
    self::$_enabled = TRUE;
  }

  static public function disable () {
    self::$_enabled = FALSE;
  }

  static public function start ($markerName) {
    // Prevent a massive array being created if benchmarking disabled
    if (self::$_enabled == FALSE) {
      return;
    }

    if (isset(self::$_markers[$markerName]) == FALSE) {
			self::$_markers[$markerName] = array();
		}

    $markerValues = array (
			'start'  => microtime(TRUE),
			'stop' => FALSE,
		);

    array_unshift(self::$_markers[$markerName], $markerValues);
  }

  static public function stop ($markerName) {
    if (isset(self::$_markers[$markerName]) == TRUE && self::$_markers[$markerName][0]['stop'] === FALSE) {
			self::$_markers[$markerName][0]['stop'] = microtime(TRUE);
		}
  }

	public static function usage ($markerName, $decimals = 4) {
    $usage = array();
    $time = 0;

		if (isset(self::$_markers[$markerName]) == FALSE) {
			return $usage;
    }

		if (self::$_markers[$markerName][0]['stop'] === FALSE) {
			// Stop the benchmark to prevent mis-matched results
			self::stop($markerName);
		}

		// Return a string version of the time between the start and stop points
		// Properly reading a float requires using number_format or sprintf
		for ($i = 0; $i < count(self::$_markers[$markerName]); $i++) {
			$time += self::$_markers[$markerName][$i]['stop'] - self::$_markers[$markerName][$i]['start'];
		}

		$usage = array (
			'time'   => number_format($time, $decimals),
			'count'  => count(self::$_markers[$markerName])
		);

    return $usage;
	}

  public static function usages ($decimals = 4) {
  	$usages = array();
    $markers = array();

  	$markers = array_keys(self::$_markers);

  	foreach ($markers as $marker) {
  		// Get each mark recursively
  		$usages[$marker] = self::usage($marker, $decimals);
  	}

  	// Return the array
  	return $usages;
  }

  /**
   Would be a good idea to write something that logs the benchmarks like so, but then this
   means we would be defying how the benchmarks should be logged
  public static function log () {
    $content = array();

    $file = new File_Stream(APPLICATION_PATH . 'logs/benchmark_' . date('Y-m-d') . '.csv');

    if ($file->exists() == FALSE) {
      $content = array(array('Marker', 'Time', 'Count'));
    }

    foreach (self::usages() as $marker => $usage) {
      $content[] = array($marker, $usage['time'], $usage['count']);
    }

    $file->write(Csv::arrayToCsv($content));
    $file->save(File_Mode::append);
  }
  **/
}

?>