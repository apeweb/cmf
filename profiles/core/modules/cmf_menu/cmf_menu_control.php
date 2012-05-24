<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}
 
class Cmf_Menu_Control extends Cmf_Control {
  protected $_menu = NULL;

  public static function render (Cmf_Menu_Control $control) {
    $control->_content .= $control->_renderHelper($control->_menu->getLinks());
  }

  public function setMenu (Cmf_Menu $menu) {
    $this->_menu = $menu;
    $this->renderCallback = $menu->getCallback();
  }

  public function getMenu () {
    return $this->_menu;
  }

  protected function _renderHelper ($links) {
    $html = '<ul>';

    foreach ($links as $link){
      $html .= '<li>' . $link['name'];

      if (array_key_exists('children', $link)) {
        $html .= $this->_renderHelper($link['children']);
      }

      $html .= '</li>';
    }

    $html .= '</ul>';

    return $html;
  }
}

?>