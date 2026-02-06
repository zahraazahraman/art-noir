<?php
require_once __DIR__ . "/../DAL.class.php";

class Category {
  private $_db;

  public function __construct() {
    $this->_db = new DAL();
  }

  public function __destruct() {
    $this->_db = null;
  }

  public function getCategories() {
  // return all categories
  $sql = "SELECT 
            c.id,
            c.name,
            COUNT(a.id) AS item_count
          FROM categories c
          LEFT JOIN artworks a ON a.category_id = c.id
          GROUP BY c.id, c.name";

    try {
      return $this->_db->getData($sql);

    } catch(Exception $e) {
      throw $e;
    }
  }

  public function addCategory($name) {
    // Check if category name already exists
    $checkSql = "SELECT id FROM categories WHERE name = '$name'";
    $result = $this->_db->getData($checkSql);
    if ($result && count($result) > 0) {
      throw new Exception("Category with this name already exists");
    }

    try {
      $sql = "INSERT INTO categories (name) VALUES ('$name')";
      return $this->_db->executeQuery($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  public function updateCategory(int $id, string $name) {
    // Check if category name already exists for another category
    $checkSql = "SELECT id FROM categories WHERE name = '$name' AND id != $id";
    $result = $this->_db->getData($checkSql);
    if ($result && count($result) > 0) {
      throw new Exception("Category with this name already exists");
    }

    try {
      $sql = "UPDATE categories SET name='$name' WHERE id=$id";
      return $this->_db->executeQuery($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  public function deleteCategory(int $id) {
    try {
      $sql = "DELETE FROM categories WHERE id=$id";
      return $this->_db->executeQuery($sql);

    } catch(Exception $e) {
      throw $e;
    }
  }

  public function hasArtworks(int $id) {
    try {
      $sql = "SELECT COUNT(*) as count FROM artworks WHERE category_id = $id";
      $result = $this->_db->getData($sql);
      return $result[0]['count'] > 0;
    } catch(Exception $e) {
      throw $e;
    }
  }
}
?>
