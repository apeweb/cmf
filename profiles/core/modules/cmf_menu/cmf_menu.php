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
  protected $_type = NULL;
  protected $_renderCallback = NULL;

  const Prepared_Statement_Library = 'cmf_menu_prepared_statement_library';

  public function __construct ($menuProperties = array()) {
    Assert::isArray($menuProperties);

    if (isset($menuProperties['id']) == TRUE) {
      $this->setId(intval($menuProperties['id']));
    }

    if (isset($menuProperties['name']) == TRUE) {
      $this->setName($menuProperties['name']);
    }

    if (isset($menuLinkProperties['render_callback']) == TRUE) {
      $this->setRenderCallback($menuLinkProperties['render_callback']);
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
    $menu->_id = $row['mn_id'];
    $menu->_name = $row['mn_name'];
    $menu->_renderCallback = $row['mn_render_callback'];

    return $menu;
  }

  public static function getMenuByName ($name) {
    Assert::isString($name);

    $query = Cmf_Database::call('cmf_menu_get_by_name', self::Prepared_Statement_Library);
    $query->bindValue(':mn_name', $name);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $row = $query->fetch();

    if ($row == FALSE) {
      throw new RuntimeException("Menu with the name '{$name}' could not be found");
    }

    $menu = new self;
    $menu->_id = $row['mn_id'];
    $menu->_name = $row['mn_name'];
    $menu->_renderCallback = $row['mn_render_callback'];

    return $menu;
  }

  public function setId ($id) {
    Assert::isInteger($id);
    $this->_id = $id;
    return $this;
  }

  public function getId () {
    return $this->_id;
  }

  public function setName ($name) {
    Assert::isString($name);
    $this->_name = $name;
    return $this;
  }

  public function getName () {
    return $this->_name;
  }

  // A friendly name for the type of navigation
  public function setType ($type) {
    Assert::isString($type);
    $this->_type = $type;
    return $type;
  }

  public function getType () {
    return $this->_type;
  }

  public function setRenderCallback ($renderCallback) {
    Assert::isString($renderCallback);
    $this->_renderCallback = $renderCallback;
    return $this;
  }

  public function getCallback () {
    return $this->_renderCallback;
  }

  // returns a tree array of menu links
  public function getLinks ($active = TRUE, $refresh = FALSE) {
    Assert::isBoolean($active, TRUE);
    Assert::isBoolean($refresh);

    static $menuLinks = NULL;
    $refs = array();

    if (isset($menuLinks[$this->_id][$active]) && $refresh == FALSE) {
      return $menuLinks[$this->_id][$active];
    }

    if ($active) {
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
        $menuLinks[$this->_id][$active][$data['mnl_id']] = &$ref;
      }
      else {
        $refs[$data['mnl_parent_id']]['children'][$data['mnl_id']] = &$ref;
      }
    }

    // xxx trigger an event so that links can be messed with

    return $menuLinks[$this->_id][$active];
  }

  public function addLink (Cmf_Menu_Link $cmfMenuLink) {
    // xxx finish
    $query = Cmf_Database::call('cmf_menu_add_link', self::Prepared_Statement_Library);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    return $this;
  }

  public function removeLink (Cmf_Menu_Link $cmfMenuLink) {
    // xxx finish
  }

  public function save () {
    if ($this->_id === NULL) {
      $query = Cmf_Database::call('xxx', self::Prepared_Statement_Library);
      // xxx finish
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
    }
    else {
      $query = Cmf_Database::call('xxx', self::Prepared_Statement_Library);
      // xxx finish
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
    }

    // xxx update the links

    return $this;
  }
}

?>