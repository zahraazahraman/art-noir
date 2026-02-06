<?php
class DAL {

    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "ArtNoir";

    private $conn;

    public function __construct() {
        $this->conn = @new mysqli(
            $this->servername,
            $this->username,
            $this->password,
            $this->dbname
        );

        if ($this->conn->connect_error) {
            throw new Exception("Connection failed");
        }
    }

    // Get data without parameters
    public function getData($sql) {
        $result = mysqli_query($this->conn, $sql);

        if ($result === false) {
            throw new Exception("Query failed: " . mysqli_error($this->conn));
        }

        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }

    // Execute query without parameters
    public function executeQuery($sql) {
        $result = mysqli_query($this->conn, $sql);

        if ($result === false) {
            throw new Exception("Query failed: " . mysqli_error($this->conn));
        }

        if (stripos(trim($sql), "INSERT") === 0) {
            return $this->conn->insert_id;
        }

        return true;
    }

    // Execute query with parameters
    public function executeQueryWithParams($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }
            
            // Bind parameters if any
            if (!empty($params)) {
                $types = '';
                $bindParams = [];
                
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_double($param)) {
                        $types .= 'd';
                    } elseif (is_null($param)) {
                        $types .= 's';
                    } else {
                        $types .= 's';
                    }
                    $bindParams[] = &$param;
                }
                
                $stmt->bind_param($types, ...$bindParams);
            }
            
            $result = $stmt->execute();
            
            // For INSERT queries, return the insert ID
            if (stripos(trim($sql), "INSERT") === 0) {
                $insertId = $stmt->insert_id;
                $stmt->close();
                return $insertId;
            }
            
            $stmt->close();
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }


    // Get data with parameters
    public function getDataWithParams($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt === false) {
                throw new Exception("Failed to prepare statement: " . $this->conn->error);
            }
            
            // Bind parameters if any
            if (!empty($params)) {
                $types = '';
                $bindParams = [];
                
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_double($param)) {
                        $types .= 'd';
                    } elseif (is_null($param)) {
                        $types .= 's';
                    } else {
                        $types .= 's';
                    }
                    $bindParams[] = &$param;
                }
                
                $stmt->bind_param($types, ...$bindParams);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            $stmt->close();
            
            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getLastInsertId() {
        return $this->conn->insert_id;
    }

    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>