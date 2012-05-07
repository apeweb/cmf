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

class Hash {
  public static function password ($password, $salt = '', $blockIterations = 100000, $keyLength = 32, $algorithm = 'sha512') {
		$hashLength = strlen(hash($algorithm, NULL, TRUE));
		$keyBlocks = ceil($keyLength / $hashLength);
		$derivedKey = '';

		for ($block = 1; $block <= $keyBlocks; $block++) {
			$iteratedBlock = $iteratedBlockCopy = hash_hmac($algorithm, $salt . pack('N', $block), $password, TRUE);
			for ($i = 1; $i < $blockIterations; $i++) {
				$iteratedBlock ^= ($iteratedBlockCopy = hash_hmac($algorithm, $iteratedBlockCopy, $password, TRUE));
      }
			$derivedKey .= $iteratedBlock;
		}

		return substr($derivedKey, 0, $keyLength);
	}

  /**
   * Used for hashing session ID's
   * @param string $data Data to be hashed
   * @return string Hashed data
   */
  public static function id ($data) {
    $hash = base64_encode(hash('sha256', $data, TRUE));
    // Modify the hash so it's safe to use in URLs.
    return strtr($hash, array('+' => '-', '/' => '_', '=' => ''));
  }
}

?>