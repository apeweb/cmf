<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Cmf_Menu_Link {
  protected $_id = NULL;
  protected $_parentId = 0;
  protected $_menuId = NULL;
  protected $_name = NULL;
  protected $_url = NULL;
  protected $_weight = 0;
  protected $_cssClass = NULL;
  protected $_tooltip = NULL;
  protected $_active = TRUE;

  const Prepared_Statement_Library = 'cmf_menu_link_prepared_statement_library';

  public function __construct ($menuLinkProperties = array()) {
    Assert::isArray($menuLinkProperties);

    if (isset($menuLinkProperties['id']) == TRUE) {
      Assert::isInteger($menuLinkProperties['id']);

      if (self::menuLinkExists($menuLinkProperties['id']) == TRUE) {
        throw new RuntimeException("Invalid menu link id '{$menuLinkProperties['id']}' specified");
      }

      $this->_id = $menuLinkProperties['id'];
    }

    if (isset($menuLinkProperties['parent_id']) == TRUE) {
      Assert::isInteger($menuLinkProperties['parent_id']);
      $this->_parentId = $menuLinkProperties['parent_id'];
    }

    if (isset($menuLinkProperties['name']) == TRUE) {
      Assert::isString($menuLinkProperties['name']);
      $this->_name = $menuLinkProperties['name'];
    }

    if (isset($menuLinkProperties['url']) == TRUE) {
      Assert::isString($menuLinkProperties['url']);
      $this->_url = $menuLinkProperties['url'];
    }

    if (isset($menuLinkProperties['weight']) == TRUE) {
      Assert::isInteger($menuLinkProperties['weight']);
      $this->_weight = $menuLinkProperties['weight'];
    }

    if (isset($menuLinkProperties['css_class']) == TRUE) {
      Assert::isString($menuLinkProperties['css_class']);
      $this->_cssClass = $menuLinkProperties['css_class'];
    }

    if (isset($menuLinkProperties['tooltip']) == TRUE) {
      Assert::isBoolean($menuLinkProperties['tooltip']);
      $this->_tooltip = $menuLinkProperties['tooltip'];
    }

    if (isset($menuLinkProperties['active']) == TRUE) {
      Assert::isBoolean($menuLinkProperties['active']);
      $this->_active = $menuLinkProperties['active'];
    }
  }

  public static function menuLinkExists ($id) {
    $query = Cmf_Database::call('cmf_menu_link_exists', self::Prepared_Statement_Library);
    $query->bindValue(':mnl_id', $id);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    return (bool) $query->fetchColumn();
  }

  public static function getMenuLink ($id) {
    Assert::isInteger($id);

    $query = Cmf_Database::call('cmf_menu_link_get', self::Prepared_Statement_Library);
    $query->bindValue(':mnl_id', $id);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    $row = $query->fetch();

    if ($row == FALSE) {
      throw new RuntimeException("Menu link with the id '{$id}' could not be found");
    }

    $menuLink = new self;
    $menuLink->_id = $id;
    $menuLink->_parentId = $row['mnl_parent_id'];
    $menuLink->_menuId = $row['mn_id'];
    $menuLink->_name = $row['mnl_name'];
    $menuLink->_url = $row['mnl_url'];
    $menuLink->_weight = $row['mnl_weight'];
    $menuLink->_cssClass = $row['mnl_css_class'];
    $menuLink->_tooltip = $row['mnl_tooltip'];
    $menuLink->_active = $row['mnl_active'];

    return $menuLink;
  }

  // You cannot set an ID once a link has been created

  public function getId () {
    return $this->_id;
  }

  public function setParentId ($menuLinkId) {
    Assert::isInteger($menuLinkId);
    $this->_parentId = $menuLinkId;
    $this->_commitChanges();
    return $this;
  }

  public function getParentId () {
    return $this->_parentId;
  }

  // To set the menu id you need to use the Cmf_Menu class

  public function getMenuId () {
    return $this->_menuId;
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

  public function setUrl ($url) {
    Assert::isString($url);
    $this->_url = $url;
    $this->_commitChanges();
    return $this;
  }

  public function getUrl () {
    return $this->_url;
  }

  public function setWeight ($weight) {
    Assert::isInteger($weight);
    $this->_weight = $weight;
    $this->_commitChanges();
    return $this;
  }

  public function getWeight () {
    return $this->_weight;
  }

  public function setCssClass ($cssClass) {
    Assert::isString($cssClass);
    $this->_cssClass = $cssClass;
    $this->_commitChanges();
  }

  public function getCssClass () {
    return $this->_cssClass;
  }

  public function setTooltip ($tooltip) {
    Assert::isString($tooltip);
    $this->_tooltip = $tooltip;
    $this->_commitChanges();
  }

  public function getTooltip () {
    return $this->_tooltip;
  }

  public function activate () {
    $this->_active = TRUE;
    $this->_commitChanges();
  }

  public function deactivate () {
    $this->_active = FALSE;
    $this->_commitChanges();
  }

  public function isActive () {
    return $this->_active;
  }

  public function delete () {
    $query = Cmf_Database::call('cmf_menu_link_delete', self::Prepared_Statement_Library);
    $query->bindValue(':mnl_id', $this->_id);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }

  protected function _commitChanges () {
    $query = Cmf_Database::call('cmf_menu_link_update', self::Prepared_Statement_Library);
    $query->bindValue(':mnl_parent_id', $this->_parentId);
    $query->bindValue(':mnl_name', $this->_name);
    $query->bindValue(':mnl_url', $this->_url);
    $query->bindValue(':mnl_weight', $this->_weight);
    $query->bindValue(':mnl_css_class', $this->_cssClass);
    $query->bindValue(':mnl_tooltip', $this->_tooltip);
    $query->bindValue(':mnl_active', $this->_active);
    $query->bindValue(':mnl_id', $this->_id);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
  }
}

?>