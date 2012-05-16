<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Mvc_Exception_Handler {
  public function catchUncaught ($exception, $message = NULL, $file = NULL, $line = NULL) {
    try {
      $code = $exception->getCode();
      $type = get_class($exception);
      $message = $exception->getMessage();
      $file = $exception->getFile();
      $line = $exception->getLine();

  		$file = preg_replace('#^' . preg_quote(ROOT, '#') . '#', '', $file);

      // Log the error
      Log::writeExtendedEntry($type, $error, $message, Backtrace::summary(), Log_Level::error);

      // Send the headers
      if (method_exists($exception, 'sendHeaders') == TRUE && headers_sent() == FALSE) {
  			$exception->sendHeaders();
  		}
      else {
        header('HTTP/1.1 500 Internal Server Error');
      }

      // Make sure the output is free from HTML as it could break the page
      $error = htmlspecialchars($error);
      $message = htmlspecialchars($message);
      $line = htmlspecialchars($line);
      $file = htmlspecialchars($file);
      $backtrace = Backtrace::steps();

      // xxx tmp
      require(FRAMEWORK_PATH . 'views' . DIRECTORY_SEPARATOR . 'basic_error' . EXT);
      exit;

  		// Load the error view
      if (Config::hasValue('500', 'errors', 'system') == TRUE) {
  		  require(Path_Library::getFiles(Config::getValue('500', 'errors', 'system'), File_System_Folder::views));
      }
      elseif (defined('FRAMEWORK_PATH') == TRUE && defined('EXT') == TRUE && defined('IN_PRODUCTION') == TRUE && IN_PRODUCTION == FALSE && file_exists(FRAMEWORK_PATH . 'views' . DIRECTORY_SEPARATOR . 'basic_error' . EXT) == TRUE) {
        require(FRAMEWORK_PATH . 'views' . DIRECTORY_SEPARATOR . 'basic_error' . EXT);
      }
      else {
        if (defined('IN_PRODUCTION') == TRUE && IN_PRODUCTION == FALSE) {
          echo '[' . $code . '] ' . $type . '<br>';
          echo $message . '<br>';
          echo 'File: ' . $line . '<br>';
          echo 'Line: ' . $file . '<br>';
          echo '<pre>' . print_r(debug_backtrace(), TRUE) . '</pre>';
        }
        else {
          echo '
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
            <html><head><title>Website Error</title></head><body><h1>' . Literal::getPhrase('Website Error', 'system') . '</h1>
            <p>' . Literal::getPhrase('Website error occured', 'system') . '</p>
            <!--[' . $line . '] ' . $type . ': ' . $message . '--></body></html>
          ';
        }
      }

      // xxx should be application?
      // xxx commented out because needs changing...
      //if (Event::has_run('system.shutdown') == FALSE) {
      //  // Run the shutdown even to ensure a clean exit
      //  Event::run('system.shutdown');
      //}

  		// Turn off error reporting
    	error_reporting(0);
    	exit;
    }
    // Catch errors/exception within fatal exceptions
    catch (Exception $ex) {
      if (headers_sent() == FALSE) {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-type: text/html', TRUE);
      }

			if (IN_PRODUCTION == TRUE) {
			  /**
			   * Do not trigger error with trigger_error function as website is in
			   * production so we only want to display minimal information
			   */
        echo '
          <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
          <html><head><title>Website Error</title></head><body><h1>' . Literal::getPhrase('Website Error', 'system') . '</h1>
          <p>' . Literal::getPhrase('Website error occured', 'system') . '</p>
          <!--[' . $line . '] ' . $type . ': ' . $message . '--></body></html>
        ';
        exit;
			}
			else {
				echo $ex->getMessage() . '<br>';
        echo 'File: ' . $ex->getFile() . '<br>';
        echo 'Line: ' . $ex->getLine() . '<br>';
			  echo '<pre>' . print_r(debug_backtrace(), TRUE) . '</pre>';
        exit;
			}
    }
  }

  /**
   * This method sends E_USER_ERRORs and E_RECOVERABLE_ERRORs to the exception
   * handler for recovery while other errors are just logged to prevent the
   * script from exiting due to an uncaught exception
   */
  static public function throwErrorException ($errno, $errstr, $errfile, $errline) {
    if (in_array($errno, array(E_USER_ERROR, E_RECOVERABLE_ERROR)) == TRUE) {
      throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    else {
      // xxx Log the error
    }
  }
}

?>