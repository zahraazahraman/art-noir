<?php
require_once __DIR__ . "/../DAL.class.php";

class Artist {
  private $_db;

  public function __construct() {
    $this->_db = new DAL();
  }

  public function __destruct() {
    $this->_db = null;
  }

  // Get artists with optional filters
  public function getArtists(?string $search = null, ?string $artist_type = null) {
    $sql = "SELECT artists.*, countries.name AS country_name 
            FROM artists 
            LEFT JOIN countries ON artists.country_id = countries.id 
            WHERE 1=1";

    if (!is_null($search) && $search !== "") {
      $search = $this->_db->escape($search);
      $search = "%" . $search . "%";
      $sql .= " AND (artists.name LIKE '$search' OR countries.name LIKE '$search')";
    }

    if (!is_null($artist_type) && $artist_type !== "" && $artist_type !== "Artist Type") {
      // Normalize common UI placeholders that mean "no filter":
      // older pages used the label 'Type' while some code checks 'Artist Type'
      if ($artist_type !== "Type") {
        $artist_type = $this->_db->escape($artist_type);
        $sql .= " AND artists.artist_type = '$artist_type'";
      }
    }

    try {
      return $this->_db->getData($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  // Add new artist
  public function addArtist(string $name, string $biography, int $country_id, ?string $birth_year, ?string $death_year, string $artist_type, ?int $user_id = null) {
    // Escape strings
    $name = $this->_db->escape($name);
    $biography = $this->_db->escape($biography);
    $birth_year = $birth_year ? $this->_db->escape($birth_year) : null;
    $death_year = $death_year ? $this->_db->escape($death_year) : null;
    $artist_type = $this->_db->escape($artist_type);

    // Check if artist name already exists
    $checkSql = "SELECT id FROM artists WHERE name = '$name'";
    $result = $this->_db->getData($checkSql);
    if ($result && count($result) > 0) {
      throw new Exception("Artist with this name already exists");
    }

    try {
      $sql  = "INSERT INTO artists (name, biography, country_id, birth_year, death_year, artist_type, user_id) ";
      $sql .= "VALUES ('$name', '$biography', $country_id, " . (is_null($birth_year) ? 'NULL' : "'$birth_year'") . ", " . (is_null($death_year) ? 'NULL' : "'$death_year'") . ", '$artist_type', " . (is_null($user_id) ? 'NULL' : intval($user_id)) . ")";
      
      if ($this->_db->executeQuery($sql)) {
          // Return the inserted ID
          return $this->_db->getLastInsertId();
      }
      return false;
    } catch(Exception $e) {
      throw $e;
    }
  }

  // Update existing artist
  public function updateArtist(int $id, string $name, string $biography, int $country_id, ?string $birth_year, ?string $death_year, string $artist_type) {
    // Escape strings
    $name = $this->_db->escape($name);
    $biography = $this->_db->escape($biography);
    $birth_year = $birth_year ? $this->_db->escape($birth_year) : null;
    $death_year = $death_year ? $this->_db->escape($death_year) : null;
    $artist_type = $this->_db->escape($artist_type);

    // Check if artist name already exists for another artist
    $checkSql = "SELECT id FROM artists WHERE name = '$name' AND id != $id";
    $result = $this->_db->getData($checkSql);
    if ($result && count($result) > 0) {
      throw new Exception("Artist with this name already exists");
    }

    try {
      $sql = "UPDATE artists SET name='$name', biography='$biography', country_id=$country_id, birth_year='$birth_year', death_year=" . (is_null($death_year) ? 'NULL' : "'$death_year'") . ", artist_type='$artist_type' WHERE id=$id";
      return $this->_db->executeQuery($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  // Delete artist
  public function deleteArtist(int $id) {
    // Check if artist has associated artworks
    $checkSql = "SELECT COUNT(*) as count FROM artworks WHERE artist_id = $id";
    $result = $this->_db->getData($checkSql);
    if ($result && $result[0]['count'] > 0) {
      throw new Exception("Cannot delete artist because there are artworks assigned to this artist.");
    }

    try {
      $sql = "DELETE FROM artists WHERE id=$id";

      return $this->_db->executeQuery($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  // Get artist by user_id
  public function getArtistByUserId(int $user_id) {
    $sql = "SELECT * FROM artists WHERE user_id = $user_id";
    try {
      $result = $this->_db->getData($sql);
      return $result ? $result[0] : null;
    } catch(Exception $e) {
      throw $e;
    }
  }
}
?>