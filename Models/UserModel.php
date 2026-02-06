<?php
require_once __DIR__ . "/../DAL.class.php";

class User {
  private $_db;

  public function __construct() {
    $this->_db = new DAL();
  }

  public function __destruct() {
    $this->_db = null;
  }

  // GET ALL USERS WITH OPTIONAL FILTERS
  public function getUsers(?string $search = null, ?string $role = null, ?string $state = null) {
    $sql = "SELECT * FROM users WHERE 1=1";

    if (!is_null($search) && $search !== "") {
      $search = $this->_db->escape($search);
      $search = "%$search%";
      $sql .= " AND (name LIKE '$search' OR email LIKE '$search')";
    }

    if (!is_null($role) && ($role !=="" && $role != "Role")) {
      $role = $this->_db->escape($role);
      $sql .= " AND role = '$role'";
    }

    if (!is_null($state) && ($state !=="" && $state != "State")) {
      $state = $this->_db->escape($state);
      $sql .= " AND state = '$state'";
    }

    try {
      return $this->_db->getData($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  // CHECK LOGIN CREDENTIALS
  public function checkLogin(string $email, string $password, string $role)
  {
    $email = $this->_db->escape($email);
    $role = $this->_db->escape($role);

    try {
      $sql = "SELECT * FROM users WHERE email = '$email' AND role = '$role'";
      $result = $this->_db->getData($sql);

      if (empty($result)) {
        return false;
      }

      // Check password using secure verification
      $user = $result[0];
      if (!password_verify($password, $user['password'])) {
        return false;
      }

      return $user;

    } catch (Exception $e) {
      return false;
    }
  }

  // ADD NEW USER
  public function addUser(string $name, string $email, string $password, string $role, string $state) {
    // Escape strings
    $name = $this->_db->escape($name);
    $email = $this->_db->escape($email);
    $role = $this->_db->escape($role);
    $state = $this->_db->escape($state);

    // Check if email already exists
    $checkSql = "SELECT id FROM users WHERE email = '$email'";
    $result = $this->_db->getData($checkSql);
    if ($result && count($result) > 0) {
      throw new Exception("User with this email already exists");
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
      $sql = "INSERT INTO users (name, email, password, role, state) VALUES ('$name', '$email', '$hashedPassword', '$role', '$state')";
      return $this->_db->executeQuery($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  // UPDATE EXISTING USER
  public function updateUser(int $id, array $updateData) {
    $allowedFields = ['name', 'email', 'password', 'role', 'state'];
    $setParts = [];

    foreach ($updateData as $field => $value) {
      if (in_array($field, $allowedFields)) {
        $value = $this->_db->escape($value);
        $setParts[] = "$field='$value'";
      }
    }

    if (empty($setParts)) {
      return false;
    }

    // Check if email is being updated and already exists for another user
    if (isset($updateData['email'])) {
      $email = $this->_db->escape($updateData['email']);
      $checkSql = "SELECT id FROM users WHERE email = '$email' AND id != $id";
      $result = $this->_db->getData($checkSql);
      if ($result && count($result) > 0) {
        throw new Exception("User with this email already exists");
      }
    }

    try {
      $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE id=$id";
      return $this->_db->executeQuery($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  // UPDATE USER PASSWORD
  public function updateUserPassword(int $id, string $newPassword) {
    // Hash the new password securely
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    try {
      $sql = "UPDATE users SET password='$hashedPassword' WHERE id=$id";
      return $this->_db->executeQuery($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }

  // DELETE USER
  public function deleteUser(int $id) {
    try {
      $sql = "DELETE FROM users WHERE id=$id";

      return $this->_db->executeQuery($sql);
    } catch(Exception $e) {
      throw $e;
    }
  }
}
?>
