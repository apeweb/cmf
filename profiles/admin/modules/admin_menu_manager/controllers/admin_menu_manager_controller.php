<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Admin_Menu_Manager_Controller extends Controller {
  const Prepared_Statement_Library = 'admin_menu_manager_prepared_statement_library';

  // manage menus
  public static function manage () {
    Admin_Controller::shared();
    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_menu_manager' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  public static function add () {
    Admin_Controller::shared();
    self::edit();
  }

  public static function edit ($arguments = array('id' => 0)) {
    Admin_Controller::shared();

    if (isset($arguments['id']) == FALSE) {
      // xxx show the http_error controller bad_request view
    }

    // xxx update
    // $submitButton = new Cmf_Button;
    // if ($submitButton->isClicked() == TRUE) {
    //   self::_handleEditPostback();
    // }
    // also will eventually be managed by form like so
    // $form->setCallback('Admin_Menu_Manager_Controller::handleEditPostback');
    if (Request::method() == 'POST') {
      self::_handleEditPostback();
    }

    if ($arguments['id'] > 0) {
      // edit
      $menu = Cmf_Menu::getMenu(intval($arguments['id']));
      View_Data::setValue('menu_id', intval($menu->getId()));
      View_Data::setValue('menu_name', $menu->getName());
    
      $linksTable = new Cmf_Menu_Control;
      $linksTable->setMenu($menu);
      $linksTable->renderCallback = __CLASS__ . '::renderLinksTable';
      View_Data::setValue('menu_links', $linksTable);

      View_Data::setValue('page', 'title', 'Edit &quot;' . $menu->getName() . '&quot; Menu');
    }
    else {
      // add

      View_Data::setValue('page', 'title', 'Add a Menu');
    }

    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_menu_edit' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  protected static function _handleEditPostback () {
    
  }

  public static function renderLinksTable (Cmf_Menu_Control $menu) {
    $menu->setContent(self::_renderLinksTableHelper($menu->getMenu()->getLinks(FALSE, TRUE)));
  }

  // xxx needs tidying, ie use controls, and fix JS issues
  protected static function _renderLinksTableHelper ($links, $level = 0) {
    $html = '';

    $indentation = str_repeat('<span class="indentation">&nbsp;</span>', $level);

    $weightOptions = '';
    for ($i = -50; $i < 51; ++$i) {
      $weightOptions .= '<option>' . $i . '</option>';
    }

    foreach ($links as $linkId => $link) {
      $activeCheckbox = $link['active'] ? ' checked="checked"' : '';
      $linkId = intval($linkId);

      // xxx fix JS issues with dragging
      //$html .= '<tr class="draggable">
      $html .= '<tr class="">
        <td>
          ' . $indentation . '<a href="#" title="">' . $link['name'] . '</a>
        </td>
        <td>
          <a href="' . $link['url'] . '" rel="external">' . $link['url'] . '</a>

          <!--
        </td>
        <td>
          <select class="mnl_weight" name="mnl_id[' . $linkId . '][mnl_weight]">
            ' . str_replace('<option>' . $link['weight'] . '</option>', '<option selected="selected">' . $link['weight'] . '</option>', $weightOptions) . '
          </select>
          -->

          <input class="mnl_parent_id" type="hidden" name="mnl_id[' . $linkId . '][mnl_parent_id]" value="' . $link['parent_id'] . '" />
          <input class="mnl_id" type="hidden" value="' . $linkId . '" />
        </td>
        <td>
          <input type="checkbox" name="mnl_id[' . $linkId . '][mnl_active]" value="1"' . $activeCheckbox . ' />
        </td>
        <td class="actions">
          <a href="/admin/menus/delete-link/' . $linkId . '" title="Delete" class="delete"><img src="/themes/admin/images/mini-icons/delete.png" alt="Delete" /></a>
          &nbsp; <a href="/admin/menus/edit-link/' . $linkId . '" title="Edit"><img src="/themes/admin/images/mini-icons/edit.png" alt="Edit" /></a>
        </td>
      </tr>';

      if (array_key_exists('children', $link)) {
        $html .= self::_renderLinksTableHelper($link['children'], ($level + 1));
      }
    }

    return $html;
  }

  // xxx move into own menu link controller
  public static function addLink ($arguments = array()) {
    Admin_Controller::shared();

    if (isset($arguments['id']) == FALSE) {
      // xxx show the http_error controller bad_request view
    }

    self::editLink(array('id' => 0, 'parent_id' => $arguments['id']));
  }

  // xxx move into own menu link controller
  public static function editLink ($arguments = array('id' => 0)) {
    Admin_Controller::shared();

    if (isset($arguments['id']) == FALSE) {
      // xxx show the http_error controller bad_request view
    }

    // xxx update
    if (Request::method() == 'POST') {
      self::_handleEditLinkPostback();
    }

    if ($arguments['id'] > 0) {
      // edit
      $menu = Cmf_Menu_Link::getMenuLink(intval($arguments['id']));
      View_Data::setValue('menu_link_id', $menu->getId());
      View_Data::setValue('menu_link_name', $menu->getName());
      View_Data::setValue('menu_link_url', $menu->getUrl());
      View_Data::setValue('menu_link_description', $menu->getTooltip());
      View_Data::setValue('menu_link_css_class', $menu->getCssClass());
      View_Data::setValue('menu_link_active', $menu->isActive());

      View_Data::setValue('page', 'title', 'Edit &quot;' . $menu->getName() . '&quot; Menu Link');
    }
    else {
      // add
      View_Data::setValue('menu_id', $arguments['parent_id']);
      View_Data::setValue('page', 'title', 'Add a New Menu Link');
    }

    $content = new Cmf_Template_Control;
    $content->templatePath = dirname(__DIR__) . '/views/admin_menu_edit_link' . PHP_EXT;
    View_Data::setValue('content', $content);
  }

  // xxx move into own menu link controller
  protected static function _handleEditLinkPostback () {
    $query = Cmf_Database::call('admin_menu_update_link', self::Prepared_Statement_Library);

    $query->bindValue(':s_id', Config::getValue('site', 'id'), PDO::PARAM_INT);
    $query->bindParam(':mnl_weight', $mnl_weight, PDO::PARAM_STR);
    $query->bindParam(':mnl_parent_id', $mnl_parent_id, PDO::PARAM_STR);
    $query->bindParam(':mnl_active', $mnl_active, PDO::PARAM_STR);
    $query->bindParam(':mnl_id', $mnl_id, PDO::PARAM_INT);

    foreach ($_POST['mnl_id'] as $mnl_id => $menuLink) {
      $mnl_weight = $menuLink['mnl_weight'];
      $mnl_parent_id = $menuLink['mnl_parent_id'];
      $mnl_active = $menuLink['mnl_active'];
      $query->execute();
    }
  }
}

?>