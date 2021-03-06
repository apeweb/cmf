<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx replace with http://code.google.com/p/parsecsv-for-php/
class Csv {
	static protected function _arrayToCsvLine ($data, $delimiter = ',', $enclosure = '"', $escape = '"') {
		$fields = array();
	  $record = '';

	  $delimiter_esc = preg_quote($delimiter, '/');
	  $enclosure_esc = preg_quote($enclosure, '/');

		foreach ($data as $field) {
		  if (is_array($field) == TRUE) {
		    $field = serialize($field);
		  }

		  if (preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field)) {
		    $fields[] = $enclosure . str_replace($enclosure, $escape . $enclosure, $field) . $enclosure;
		  }
		  else {
		    $fields[] = $field;
		  }
		}

		$record = implode($delimiter, $fields);

	  return $record;
	}

	static public function loadArray ($data, $delimiter = ',', $enclosure = '"', $escape = '"', $terminator = "\r\n") {
		$records = array();

		foreach ($data as $record) {
      if (is_array($record) == FALSE) {
        $record = array($record);
      }
			$records[] = self::_arrayToCsvLine($record, $delimiter, $enclosure, $escape);
		}

		return implode($terminator, $records) . $terminator;
	}

  static public function vprint ($data) {
    echo self::loadArray($data);
  }

  // Write CSV to file, takes data as array
  public function createFile ($path, $data, $delimiter = ',', $enclosure = '"', $escape = '"', $terminator = "\r\n") {
    $data = self::loadArray($data, $delimiter, $enclosure, $escape, $terminator);

//    $file = new File_Stream($filename, File_Mode::write, TRUE);
//    $file->lock();
//    $file->write($data);
//    $file->save();
//    $file->unlock();
//    $file->close();

    // xxx update
    file_put_contents($path, $data);
  }

  // return CSV as XML
  static public function asXml () {

  }
}

?>