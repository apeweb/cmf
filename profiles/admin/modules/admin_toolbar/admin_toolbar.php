<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Toolbar {
  public static function install () {
    // Config settings
    //Config::setValue(CMF_REGISTRY, 'admin', 'toolbar', 'setting_name', 'setting_value');
  }

  public static function initialise () {
    Event_Dispatcher::attachObserver(Cmf_Template_Engine_Event::buildContent, __CLASS__ . '::buildSystemLinks');
    Event_Dispatcher::attachObserver(Cmf_Template_Engine_Event::buildContent, __CLASS__ . '::buildNavigationBar');
  }

  // xxx need to be site & user specific
  public static function buildSystemLinks () {
    // $systemLinks = new Cmf_Menu_Control;
    // $systemLinks->setMenu('administration_system_links');
    // $systemLinks->renderCallback = 'Admin_Toolbar::renderSystemLinks';
    View_Data::setValue('system_links', '<a href="/admin/">Dashboard</a> | <a href="/">Homepage</a> | <a href="/admin/settings/">Settings</a> | <a href="/admin/tools/">Tools</a> | <a href="?action=logout">Log Out</a>');
  }

  public static function buildNavigationBar () {
    $navigationBar = new Cmf_Menu_Control;

    // xxx specifying id 2 here breaks things, need blocks or something to fix this issue so that we specify which menu to use in the block and the value is stored in the DB
    switch (Config::getValue('site', 'id')) {
      case 1:
        $menuId = 1;
      break;

      case 2:
        $menuId = 2;
      break;

      default:
        $menuId = 0;
    }

    $navigationBar->setMenu(Cmf_Menu::getMenu($menuId));
    View_Data::setValue('navigation_bar', $navigationBar);
  }

  public static function renderNavigationBar (Cmf_Menu_Control $menu) {
    $menu->setContent(self::_renderNavigationBarHelper($menu->getMenu()->getLinks()));
  }

  protected static function _renderNavigationBarHelper ($links, $level = 0) {
    $html = '<ul class="drop_down">';

    foreach ($links as $link) {
      $anchor = '<a';
      if ($link['url'] != '') {
        $anchor .= ' href="' . $link['url'] . '"';
      }
      if ($link['css_class'] != '') {
        $anchor .= ' class="' . $link['css_class'] . '"';
      }
      if ($link['tooltip'] != '') {
        $anchor .= ' title="' . $link['tooltip'] . '"';
      }
      $anchor .= '><span';
      if ($link['css_class'] != '') {
        $anchor .= ' class="' . $link['css_class'] . '"';
      }
      $anchor .= '>' . $link['name'] . '</span></a>';

      if (array_key_exists('children', $link)) {
        $html .= '<li class="level_' . $level . ' hover">';
        $html .= $anchor;
        $html .= self::_renderNavigationBarHelper($link['children'], $level + 1);
      }
      else {
        $html .= '<li class="level_' . $level . '">';
        $html .= $anchor;
      }

      $html .= '</li>';
    }

    $html .= '</ul>';

    return $html;
  }
}

?>