<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// Test module
class Module_X {
  static public function redirectingContent () {
    echo 'Hello World!';

    Session::keepAlive(TRUE);

    // When redirecting, you still need to exit to prevent any further PHP from being executed
    Response::redirect('test');
    // However, exiting will still cause any shutdown events to be executed and any callbacks registered with the
    // output buffer
    exit;

    // This code wouldn't be executed
    echo "more content!";
  }

  static public function logout () {
    // When logging out, make sure you run...
    Session::destroy();
    // Please note that if sessions are set to autostart, upon the next request a new session will still be created
  }

  static public function addUserOld () {
    $user = new Cmf_User;
    $user->setUserName($_POST['username']);
    $user->setPassword($_POST['password']);
    Cmf_Directory::addUser($user);

    // Get the normalised username
    $username = $user->getUserName();

    // This method allows any custom data to be set against a user
    // in this case the user profile allows profile fields to be populated
    // based on the profile fields available

    $name = new Text_Box_Control;
    $phone = new Text_Box_Control;
    Cmf_User_Profile::addField($name);
    Cmf_User_Profile::addField($phone);
    
    $userProfile = Cmf_User_Profile::create($userName); // this would get the user id from the Cmf_Directory
    $userProfile->name = 'Matthew Bonner';
    $userProfile->email = 'matthew.bonner@gmail.com';
    $userProfile->save();

    // So you can add phone numbers also
    $phoneNumber = Cmf_User_Phone_Number::add($userName);
    $phoneNumber->setLocation(Cmf_User_Phone_Number::Home);
    $phoneNumber->setNumber('07535674738');
    $phoneNumber->setDefault();
    $phoneNumber->save();

    $userProfile = Cmf_User_Profile::load($userName);
    echo $userProfile->name; // outputs Matthew Bonner
    echo $userProfile->email; // outputs matthew.bonner@gmail.com
  }

  static public function install () {
    //Cmf_User::install();
  }

  static public function test2 () {
    // We require the directory to be loaded before continuing
    Cmf_Module_Manager::loadModule('Cmf_Directory');
    echo 'Username valid: ' . Cmf_User_Library::validateUserName('ShelfSideSpur - Tuck Me In!');
  }

  // Quick example of how to add a user
  static public function addUser ($username, $password) {
    Cmf_Module_Manager::loadModule('Cmf_User');

    $user = new Cmf_User;
    $user->setUserName($username);
    $user->setPassword($password);
    $user->save();
    return $user->getUserId();
  }

  // Quick example of how to get a user
  static public function getUser ($username, $password = NULL) {
    $user = Cmf_User::getByUserName($username);
    if ($password !== NULL) {
      $user->authenticate($password);
    }
    return $user;
  }

  static public function authenticateUser () {
    //Cmf_User::install();

    //self::addUser('Administrator', 'administrator');

    //$user = self::getUser('Administrator');
    //$user->setPassword('administrator');
    //$user->save();

    $user = self::getUser('Administrator', 'administrator');
    echo (int) $user->hasAuthenticated();
  }

  static public function test () {
    // $group = Cmf_Group::getByGroupName(); // get a group object
    // $groups = Cmf_Group::getUserMembershipByUserName($user->getUserName()); // get an array of group names the user is a member of

    //Event_Dispatcher::attachObserver(Cmf_Template_Engine_Event::modifyContent, __CLASS__ . '::dumpMainContentRegion');
  }

  static public function dumpMainContentRegion () {
    //View_Data::setValue('main_content', 'test');
  }
}

?>