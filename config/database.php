<?php
class Database {
    private $host = "localhost";
    private $db_name = "records";
    private $username = "root";
    private $password = "";
    public $conn;
    private static $instance = null;

    // Singleton pattern to prevent multiple connections
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_PERSISTENT, false); // Disable persistent connections
        } catch(PDOException $e) {
            error_log("Connection error: " . $e->getMessage());
        }
        return $this->conn;
    }
    
    // Close connection
    public function closeConnection() {
        $this->conn = null;
    }
}
?>