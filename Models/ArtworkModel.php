<?php
require_once __DIR__ . "/../DAL.class.php";

class Artwork {
  private $_db;

  public function __construct() {
    $this->_db = new DAL();
  }

  public function __destruct() {
    $this->_db = null;
  }

  // Get artworks with optional filters
  public function getArtworks($search = null, $category_id = null) {
    $sql = "
      SELECT
        artworks.*,
        artists.name AS artist_name,
        categories.name AS category_name
      FROM artworks
      LEFT JOIN artists ON artworks.artist_id = artists.id
      LEFT JOIN categories ON artworks.category_id = categories.id
      WHERE 1=1
    ";

    if (!is_null($search) && $search !== "") {
      $search = "%$search%";
      $sql .= " AND (artworks.title LIKE '$search'
                OR artworks.description LIKE '$search'
                OR artists.name LIKE '$search')";
    }

    if (!is_null($category_id) && $category_id !== "") {
      $sql .= " AND artworks.category_id = $category_id";
    }

    try {
      return $this->_db->getData($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  // Get approved artworks
  public function getApprovedArtworks() {
      $sql = "SELECT artworks.*, 
                    artists.name AS artist_name, 
                    categories.name AS category_name
              FROM artworks 
              LEFT JOIN artists ON artworks.artist_id = artists.id
              LEFT JOIN categories ON artworks.category_id = categories.id
              WHERE artworks.status = 'Approved'
              ORDER BY artworks.created_at DESC";

      try {
          return $this->_db->getData($sql);
      } catch(Exception $e) {
          throw $e;
      }
  }

  // Add new artwork
  public function addArtwork($title, $description, $image_path, $artist_id, $category_id, $year_created, $status = 'Pending') {
    // Check if artwork title already exists for this artist
    $checkSql = "SELECT id FROM artworks WHERE title = '" . $this->_db->escape($title) . "' AND artist_id = $artist_id";
    $result = $this->_db->getData($checkSql);
    if ($result && count($result) > 0) {
      throw new Exception("Artwork with this title already exists for this artist");
    }

    try {
      $sql = "INSERT INTO artworks (title, description, image_path, artist_id, category_id, year_created, status) VALUES (?, ?, ?, ?, ?, ?, ?)";

      $params = [$title, $description, $image_path, $artist_id, $category_id, $year_created, $status];

      if ($this->_db->executeQueryWithParams($sql, $params)) {
          // Return the inserted ID
          return $this->_db->getLastInsertId();
      }
      return false;
    } catch(Exception $e) {
      throw $e;
    }
  }

  // Update artwork
  public function updateArtwork($id, $title, $description, $image_path, $artist_id, $category_id, $year_created, $status = 'Pending') {
    // Check if artwork title already exists for this artist and another artwork
    $checkSql = "SELECT id FROM artworks WHERE title = '" . $this->_db->escape($title) . "' AND artist_id = $artist_id AND id != $id";
    $result = $this->_db->getData($checkSql);
    if ($result && count($result) > 0) {
      throw new Exception("Artwork with this title already exists for this artist");
    }

    try {
      $sql = "UPDATE artworks SET title=?, description=?, image_path=?, artist_id=?, category_id=?, year_created=?, status=? WHERE id=?";
      $params = [$title, $description, $image_path, $artist_id, $category_id, $year_created, $status, $id];
      return $this->_db->executeQueryWithParams($sql, $params);
    } catch(Exception $e) {
      throw $e;
    }
  }

  // Delete artwork
  public function deleteArtwork($id) {
    try {
      $sql = "DELETE FROM artworks WHERE id=$id";
      return $this->_db->executeQuery($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }
}
?>
