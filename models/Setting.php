<?php
require_once __DIR__ . '/../config/database.php';

class Setting {
    private $conn;
    private $table = "system_settings";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get setting value
    public function get($key) {
        try {
            $query = "SELECT setting_value FROM " . $this->table . " WHERE setting_key = :key";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['setting_value'];
            }
            return null;
        } catch (PDOException $e) {
            error_log("Setting get Error: " . $e->getMessage());
            return null;
        }
    }

    // Update setting value
    public function update($key, $value, $updated_by) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET setting_value = :value, updated_by = :updated_by 
                      WHERE setting_key = :key";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':updated_by', $updated_by);
            $stmt->bindParam(':key', $key);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Setting update Error: " . $e->getMessage());
            return false;
        }
    }

    // Get all settings
    public function getAll() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY setting_key";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Setting getAll Error: " . $e->getMessage());
            return [];
        }
    }
}
?>