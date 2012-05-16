<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

/**
 * Provides functions for image manipulation with automatic file type detection
 */
class Image {
  //Compresses an image by percentage, 100 being the worst and 0 being the best image quality
  public static function compress ($source, $destination, $compressionRate = 15, $overwrite = FALSE) {
    // xxx replace with assert class
    assert('is_string($source)');
    assert('is_string($destination)');
    assert('is_int($compressionRate)');
    assert('is_bool($overwrite)');

    if (is_file($source) == FALSE) {
      throw new RuntimeException('One or more parts of the path could not be found.');
    }

    if ($overwrite == FALSE && file_exists($destination) == TRUE) {
      throw new RuntimeException('Destination already exists.');
    }

    if ($compressionRate < 0 || $compressionRate > 100) {
      throw new OutOfRangeException('Percentage must be between 0 and 100.');
    }

    // switch extension to work out correct compression
    switch ($source) {
      default:
        // xxx default should throw a Not_Supported_Exception
        imagejpeg(imagecreatefromstring(file_get_contents($source)), $destination, 100 - $compressionRate);
    }
  }

  /**
   * Resizes an image to a maximum pixel width and/or height
   */
  public static function resize ($source, $destination, $newWidth, $newHeight, $compressionRate = 15, $overwrite = FALSE) {
    // xxx replace with assert class
    assert('is_string($source)');
    assert('is_string($destination)');
    assert('is_int($width)');
    assert('is_int($height)');
    assert('is_int($compressionRate)');
    assert('is_bool($overwrite)');

    if (is_file($source) == FALSE) {
      throw new RuntimeException('One or more parts of the path could not be found.');
    }

    if ($overwrite == FALSE && file_exists($destination) == TRUE) {
      throw new RuntimeException('Destination already exists.');
    }

    if ($compressionRate < 0 || $compressionRate > 100) {
      throw new OutOfRangeException('Percentage must be between 0 and 100.');
    }

  	// Get new dimensions
  	list($currentWidth, $currentHeight) = getimagesize($source);

  	// Resample
  	$imageResourceIdentifier  = imagecreatetruecolor($newWidth, $newHeight);
  	$image = imagecreatefromjpeg($source);
  	imagecopyresampled($imageResourceIdentifier, $image, 0, 0, 0, 0, $newWidth, $newHeight, $currentWidth, $currentHeight);

  	// Output
  	imagejpeg($imageResourceIdentifier, $destination, 100 - $compressionRate);
  	imagedestroy($imageResourceIdentifier);
  }
}

?>