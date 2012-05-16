<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Authentication_Controller extends Controller {
  private static $_access = NULL;

  // Protect admin pages by setting access and then calling the promptAuthentication method
  public static function setAccess (Cmf_Authorisation $access) {
    Assert::isObject($access);
    self::$_access = $access;
  }

  public static function promptAuthentication () {
    // If the user is logging out
    if (isset($_GET['action']) == TRUE && $_GET['action'] == 'logout') {
      if (Session::valueExists('admin_username') == TRUE) {
        Session::deleteValue('admin_username');
      }
      $redirectPath = Cmf_Route_Table::routes()->getRoute('admin_authentication')->getUrl(array('action' => 'login'));
      Response::redirect($redirectPath, 303);
      exit;
    }

    // xxx need to fix, have to check for action to prevent other page postbacks from being handled here, but this should be done
    // xxx using a form control to ensure that the postback event is for this specific form (using form tokens maybe?)
    if (Request::method() == 'POST' && isset($_POST['action']) == TRUE && $_POST['action'] == 'Log in') {
      self::_buildPageControls();
      self::_handlePostBack();
    }

    $controllerName = Cmf_Route_Table::getActiveRoute()->getArgumentValue('controller');
    $controller = Controller_Builder::getControllerFactory()->normaliseControllerName($controllerName);

    // If the user is already logged in
    if (Session::valueExists('admin_username') == TRUE && Session::getValue('admin_username') != '') {
      if (self::$_access == NULL) {
        self::$_access = new Cmf_Authorisation;
        self::$_access->grantRoleAccess('Administrate Website');
        self::$_access->grantGroupAccess('Administrators');
      }

      if (self::$_access->isUserAuthorised(Session::getValue('admin_username')) == FALSE) {
        // Does the user have permission to access this page?
        if (count(Cmf_Flash_Message::getMessage('error', FALSE)) > 0) {
          if ($controller != __CLASS__) {
            Cmf_Flash_Message::setMessage('Access is denied. You do not have the sufficient privileges to access this page. To gain access to this page try logging in as a different user.', 'error');
          }
          else {
            Cmf_Flash_Message::setMessage('Access is denied. You are already logged in but do not have the sufficient privileges to access this area of the website. To gain access try logging in as a different user.', 'error');
          }
        }

        self::_renderLoginView();
      }

      // If the user does have admin access redirect them to the admin home if they are hitting the login page
      if ($controller == __CLASS__) {
        $redirectPath = Cmf_Route_Table::routes()->getRoute('admin_dashboard')->getUrl();
        Response::redirect($redirectPath, 303);
        exit;
      }
    }
    else {
      // Get the location of the user so that we can redirect them back there once they have logged in
      if ($controller != __CLASS__) {
        Session::setValue('admin_redirect_path', Request::path());
      }

      self::_renderLoginView();
    }
  }

  private static function _handlePostBack () {
    if (trim(View_Data::getValue('username_control')->text) == '') {
      Cmf_Flash_Message::setMessage('Please enter the user name for your account. If you are unsure what your user name is please contact Ape Web.', 'error');
      return;
    }

    if (View_Data::getValue('password_control')->text == '') {
      Cmf_Flash_Message::setMessage('Please enter the password for your account. If you are unsure what your password is please contact Ape Web.', 'error');
      return;
    }

    try {
      $user = Cmf_User::getByUserName(htmlspecialchars(View_Data::getValue('username_control')->text));
      $user->authenticate(htmlspecialchars(View_Data::getValue('password_control')->text));

      if ($user->hasAuthenticated() == FALSE) {
        Cmf_Flash_Message::setMessage('The user name or password is incorrect. Please check the information you have provided and try again.', 'error');
        return;
      }
    }
    catch (Argument_Exception $ex) {
      Cmf_Flash_Message::setMessage('The user name or password is incorrect. Please check the information you have provided and try again.', 'error');
      return;
    }

    Session::setValue('admin_username', $user->getUserName());
  }
  
  private static function _renderLoginView () {
    self::_buildPageControls();
    Cmf_Template_Engine::renderTemplate(dirname(__DIR__) . '/views/admin_login' . PHP_EXT);
    exit;
  }

  private static function _buildPageControls ($reset = FALSE) {
    static $built = FALSE;

    if ($built == TRUE && $reset == FALSE) {
      return;
    }

    // xxx add the form controls for the radio buttons

    $userNameControl = new Cmf_Text_Box_Control;
    $userNameControl->name = 'username';
    $userNameControl->id = 'username';
    View_Data::setValue('username_control', $userNameControl);

    $passwordControl = new Cmf_Text_Box_Control;
    $passwordControl->name = 'password';
    $passwordControl->id = 'password';
    $passwordControl->textMode = 'password';
    View_Data::setValue('password_control', $passwordControl);

    $built = TRUE;
  }
}

?>