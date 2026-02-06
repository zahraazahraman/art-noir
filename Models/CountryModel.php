<?php
require_once __DIR__ . "/../DAL.class.php";

class Country {
  private $_db;

  public function __construct() {
    $this->_db = new DAL();
  }

  public function __destruct() {
    $this->_db = null;
  }

  // Get all countries
  public function getCountries() {
    $sql = "SELECT * FROM countries ORDER BY name ASC";

    try {
      return $this->_db->getData($sql);
    } catch (Exception $e) {
      throw $e;
    }
  }

  // Get country by ID
  public function getCountryById(int $id) {
    if ($id <= 0) {
      return null;
    }
    $sql = "SELECT * FROM countries WHERE id = $id";

    try {
      return $this->_db->getData($sql);
    } catch (Exception $e) {
      throw $e;
    }
  }

  // Get country by code
  public function getCountryByCode(string $code) {
    if (empty($code)) {
      return null;
    }
    $code = strtoupper(trim($code));
    $sql = "SELECT * FROM countries WHERE code = '$code'";

    try {
      return $this->_db->getData($sql);
    } catch (Exception $e) {
      throw $e;
    }
  }
}
?>
