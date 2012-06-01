<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Controller extends Controller {
  public static function shared () {
    // Secure this page to admins only
    $authorisedUsers = new Cmf_Authorisation;
    $authorisedUsers->grantRoleAccess('Administrate Website');
    $authorisedUsers->grantGroupAccess('Administrators');
    Admin_Authentication_Controller::setAccess($authorisedUsers);
    Admin_Authentication_Controller::promptAuthentication();

    Cmf_Template_Engine::setMasterTemplate(Cmf_Module_Cache::getModulePath('admin_master_template') . 'views/admin_master' . PHP_EXT);

    $header = new Cmf_Template_Control;
    $header->templatePath = Cmf_Module_Cache::getModulePath('admin_master_template') . 'views/admin_header' . PHP_EXT;
    View_Data::setValue('header', $header);

    // xxx move into a block/module
    $footer = '<div id="footer"><hr /></div>';
    View_Data::setValue('footer', $footer);

    // xxx move into a block/module
    $sessionInformation = '<p>You are logged in as &quot;' . Session::getStore()->getValue('admin_username') . '&quot; and have no unread messages.</p>';
    View_Data::setValue('session_information', $sessionInformation);

    $content = new Cmf_Template_Control;
    $content->templatePath = Cmf_Module_Cache::getModulePath('admin_master_template') . 'views/admin_content' . PHP_EXT;
    View_Data::setValue('content', $content);

    $css = new Cmf_Css_Control;
    $css->addCss('http://fonts.googleapis.com/css?family=Cuprum&amp;subset=latin');
    $css->addCss('/themes/admin/css/admin.css');
    $css->addCss('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.3/themes/base/jquery-ui.css');
    View_Data::setValue('head', 'css', $css);

    $js = new Cmf_Js_Control;
    $js->addJs('/misc/scripts/jquery.min.js');
    $js->addJs('/misc/scripts/jquery.ui.min.js');
    $js->addJs('/misc/scripts/jquery.confirm.js');
    $js->addJs('/misc/scripts/jquery.hoverIntent.min.js');
    $js->addJs('/misc/scripts/jquery.fixedtableheader.min.js');
    $js->addJs('/misc/scripts/jquery.tablednd.js');
    $js->addJs('/misc/scripts/jquery.tablehover.js');
    $js->addJs('/misc/scripts/jquery.tree.js');
    $js->addJs('/misc/scripts/jquery.colresizable.js');
    $js->addJs('/misc/scripts/jquery.tablesorter.js');
    $js->addJs('/misc/scripts/jquery.form.js');
    $js->addJs('/misc/scripts/jquery.once.js');
    $js->addJs('/misc/scripts/jquery.ui.droppable.min.js');
    $js->addJs('/misc/scripts/jquery.tabledrag.js');
    $js->addJs('/profiles/admin/js/admin.js');

    View_Data::setValue('head', 'js', $js);

    /*
    // xxx view data should be populated by modules
    $form = new Cmf_Form_Control;
    $form->id = 'test';

    // xxx example of how to add a child element
    $block = new Cmf_Block_Control;
    $block->children['form'] = $form;

    // xxx adding regions to views
    View_Data::setValue('main_content', $region);

    // xxx setting the page title
    View_Data::setValue('page', 'title', 'Dashboard');
    */
  }

  public static function dashboard () {
    self::shared();

    $content = new Cmf_Template_Control;
    $content->templatePath = Cmf_Module_Cache::getModulePath('admin_dashboard') . 'views/admin_dashboard' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  public static function settings () {
    self::shared();

    $content = new Cmf_Template_Control;
    $content->templatePath = Cmf_Module_Cache::getModulePath('admin_settings') . 'views/admin_settings' . PHP_EXT;
    View_Data::setValue('content', $content);
  }
}

?>