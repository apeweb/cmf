<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

// xxx needs system testing

class Cmf_Registry_Database_Driver extends Config_Array_Driver {
  protected $_array = array();

  /**
   * Gets the ID for the root key, only root keys have an ID
   */
  public function getRootKeyId ($key) {
    $query = Cmf_Database::call('cmf_registry_key_get_id');
    $query->bindValue(':r_name', $key);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if (isset($row['r_id']) == TRUE) {
      return $row['r_id'];
    }

    return NULL;
  }

  /**
   * Sets a key's value, if the root key does not already exist it is automatically created
   * Called normally by the Config class like so:
   * Config::setValue(CMF_REGISTRY, 'cache', 'page', 'enabled', FALSE);
   */
  public function setValue ($key, $value) {
    $args = func_get_args();
    $this->_array = Array_Helper::array_merge_recursive_simple($this->_array, Array_Helper::flatToTree($args));
    return $this->_writeValue($key);
  }

  /**
   * Deletes a key's value
   * Called normally by the Config class like so:
   * Config::deleteValue(CMF_REGISTRY, 'cache', 'page', 'enabled');
   */
  public function deleteValue ($key) {
    $args = func_get_args();

    if (count($args) > 1) {
      $target = &$this->_array;
      while ($current = array_shift($args)) {
        if (count($args) == 0){
          unset($target[$current]);
          break;
        }
        if (isset($target[$current]) == FALSE) {
          $target[$current] = array();
        }
        $target = &$target[$current];
      }

      return $this->_writeValue($key);
    }
    else {
      return self::deleteRootKey($key);
    }
  }

  /**
   * Writes any changes to the key
   */
  private function _writeValue ($key) {
    // We must check first that the value hasn't just been added or deleted by something else
    $keyId = self::getRootKeyId($key);

    if ($keyId == NULL) {
      $query = Cmf_Database::call('cmf_registry_key_add');
      $query->bindValue(':r_name', $key);
      $query->bindValue(':r_value', serialize($this->_array[$key]));
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
      return $query->rowCount();
    }
    else {
      $query = Cmf_Database::call('cmf_registry_key_update');
      $query->bindValue(':r_id', $keyId);
      $query->bindValue(':r_value', serialize($this->_array[$key]));
      $query->bindValue(':s_id', Config::getValue('site', 'id'));
      $query->execute();
      return $query->rowCount();
    }
  }

  /**
   * Enable the root key so it can be used
   */
  public function enableRootKey ($key) {
    $keyId = self::getRootKeyId($key);

    $query = Cmf_Database::call('cmf_registry_key_enable');
    $query->bindValue(':r_id', $keyId);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    $numAffectedRows = $query->rowCount();
    
    if ($numAffectedRows > 0) {
      $this->_array[$key] = self::_getRootKeyValue($keyId);
    }
    
    return $numAffectedRows;
  }

  /**
   * Disable the root key so it cannot be used
   */
  public function disableRootKey ($key) {
    $keyId = self::getRootKeyId($key);

    unset($this->_array[$key]);

    $query = Cmf_Database::call('cmf_registry_key_disable');
    $query->bindValue(':r_id', $keyId);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    return $query->rowCount();
  }

  /**
   * Delete the root key so it doesn't show in the registry
   */
  public function deleteRootKey ($key) {
    $keyId = self::getRootKeyId($key);

    unset($this->_array[$key]);

    $query = Cmf_Database::call('cmf_registry_key_delete');
    $query->bindValue(':r_id', $keyId);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    return $query->rowCount();
  }

  /**
   * Get the value of a root key
   */
  private function _getRootKeyValue ($keyId) {
    $query = Cmf_Database::call('cmf_registry_key_get_value');
    $query->bindValue(':r_id', $keyId);
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);
    
    if (isset($row['r_value'])) {
      return unserialize($row['r_value']);
    }
  }

  /**
   * Loads all settings from the detault data source registry
   */
  public function load ($options = array()) {
    $query = Cmf_Database::call('cmf_registry_site_id_check');
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();

    if ($query->fetchColumn() == FALSE) {
      throw new RuntimeException("Invalid site id '" . Config::getValue('site', 'id') . "' specified in site configuration file");
    }

    // if a time comes where we need to load each setting by the root key this design makes it
    // possible to load an entire root key without loading all root keys meaning that only 1 row
    // has to be returned for the module, etc. using the key
    $query = Cmf_Database::call('cmf_registry_get_all');
    $query->bindValue(':r_active', '1');
    $query->bindValue(':r_deleted', '0');
    $query->bindValue(':s_id', Config::getValue('site', 'id'));
    $query->execute();
    /**
     * The performance of looping each record, opposed to just grabbing all records, means
     * we can get away with using less memory. This is because PDO struggles to fetch all
     * rows at the same time making it use lots of memory
     */
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $this->_array[$row['r_name']] = unserialize($row['r_value']);
    }
  }
}

?>