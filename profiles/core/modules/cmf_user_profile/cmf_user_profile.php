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
 * // Create the user
 * $userName = 'x';
 * $user = new Cmf_User;
 * $user->setUsername($userName);
 * $userId = Cmf_Directory::addUser($user);
 *
 * // Create the user profile
 * $userProfile = new Cmf_User_Profile;
 * $userProfile->linkTo($userName); // this would get the user id from the Cmf_Directory
 * $userProfile->name = 'Matthew Bonner';
 * $userProfile->email = 'matthew.bonner@gmail.com';
 */

class Cmf_User_Profile {

}

?>