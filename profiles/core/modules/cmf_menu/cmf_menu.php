<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx at some point permissions will have to be checked to ensure pages that aren't accessible
// don't appear in the menu

class Cmf_Menu {
  protected $_id = NULL;
  protected $_name = NULL;
  protected $_renderCallback = NULL;

  const Prepared_Statement_Library = 'cmf_menu_prepared_statement_library';

  // xxx create new menu
  public function __construct ($menuProperties = array()) {
    Assert::isArray($menuProperties);

    if (isset($menuProperties['id']) == TRUE) {
      Assert::isInteger($menuProperties['id']);

      if (self::menuExists($menuProperties['id']) == TRUE) {
        throw new RuntimeException("Invalid menu id '{$menuProperties['id']}' specified");
      }

      $this->_id = $menuProperties['id'];
    }

    if (isset($menuProperties['name']) == TRUE) {
      Assert::isString($menuProperties['name']);
      $this->_name = $menuProperties['name'];
    }

    if (isset($menuLinkProperties['render_callback']) == TRUE) {
      Assert::isString($menuLinkProperties['render_callback']);
      $this->_renderCallback = $menuLinkProperties['render_callback'];
    }
  }

  public static function menuExists ($id) {
    $query = Cmf_Database::call('cmf_menu_exists', self::Prepared_Statement_Library);
    $query->bindValue(':mn_id', $id);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    return (bool) $query->fetchColumn();
  }

  public static function getMenu ($id) {
    Assert::isInteger($id);

    $query = Cmf_Database::call('cmf_menu_get', self::Prepared_Statement_Library);
    $query->bindValue(':mn_id', $id);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $row = $query->fetch();

    if ($row == FALSE) {
      throw new RuntimeException("Menu with the id '{$id}' could not be found");
    }

    $menu = new self;
    $menu->_id = $id;
    $menu->_name = $row['mn_name'];
    $menu->_renderCallback = $row['mn_render_callback'];

    return $menu;
  }

  public function getId () {
    return $this->_id;
  }

  public function setName ($name) {
    Assert::isString($name);
    $this->_name = $name;
    $this->_commitChanges();
    return $this;
  }

  public function getName () {
    return $this->_name;
  }

  public function setRenderCallback ($renderCallback) {
    Assert::isString($renderCallback);
    $this->_renderCallback = $renderCallback;
    $this->_commitChanges();
    return $this;
  }

  public function getCallback () {
    return $this->_renderCallback;
  }

  // returns a tree array of menu links
  public function getLinks ($activeOnly = TRUE, $refresh = FALSE) {
    Assert::isBoolean($activeOnly);
    Assert::isBoolean($refresh);

    static $menuLinks = NULL;
    $refs = array();

    if (isset($menuLinks[$activeOnly]) && $refresh == FALSE) {
      return $menuLinks[$activeOnly];
    }

    if ($activeOnly) {
      $query = Cmf_Database::call('cmf_menu_get_active_links', self::Prepared_Statement_Library);
    }
    else {
      $query = Cmf_Database::call('cmf_menu_get_all_links', self::Prepared_Statement_Library);
    }
    $query->bindValue(':mn_id', $this->_id);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    // Loop through the rows and build a hierarchical list of menu links
    while ($data = $query->fetch()) {
      $ref = &$refs[$data['mnl_id']];

      $ref['parent_id'] = $data['mnl_parent_id'];
      $ref['name'] = $data['mnl_name'];
      $ref['url'] = $data['mnl_url'];
      $ref['weight'] = $data['mnl_weight'];
      $ref['css_class'] = $data['mnl_css_class'];
      $ref['tooltip'] = $data['mnl_tooltip'];

      if (isset($data['mnl_active'])) {
        $ref['active'] = $data['mnl_active'];
      }

      if ($data['mnl_parent_id'] == 0) {
        $menuLinks[$activeOnly][$data['mnl_id']] = &$ref;
      }
      else {
        $refs[$data['mnl_parent_id']]['children'][$data['mnl_id']] = &$ref;
      }
    }

    // xxx call a hook so that links can be messed with

    return $menuLinks[$activeOnly];
  }

  public function addLink (Cmf_Menu_Link $menuLink) {
    $query = Cmf_Database::call('cmf_menu_add_link', self::Prepared_Statement_Library);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    return $this;
  }

  // to delete a link you need to delete the actual link itself

  protected function _commitChanges () {
    // xxx finish, will save changes to menu with the exception of adding new links
  }
}

?>