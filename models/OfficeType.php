<?php
require_once __DIR__ . '/../config/database.php';

class OfficeType {
    private $conn;
    private $table = "office_types";

    public $id;
    public $type_name;
    public $description;
    public $status;
    public $created_at;
    public $updated_at;
    public $created_by;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all office types
    public function getAllTypes($includeInactive = false) {
        try {
            $query = "SELECT ot.*, u.username as created_by_name 
                      FROM " . $this->table . " ot
                      LEFT JOIN users u ON ot.created_by = u.id";
            
            if (!$includeInactive) {
                $query .= " WHERE ot.status = 1";
            }
            
            $query .= " ORDER BY ot.type_name ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("OfficeType getAllTypes Error: " . $e->getMessage());
            return [];
        }
    }

    // Get active office types for dropdown
    public function getActiveTypes() {
        try {
            $query = "SELECT id, type_name FROM " . $this->table . " 
                      WHERE status = 1 
                      ORDER BY type_name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("OfficeType getActiveTypes Error: " . $e->getMessage());
            return [];
        }
    }

    // Get single office type by ID
public function getTypeById($id) {
    try {
        $query = "SELECT ot.*, u.username as created_by_name 
                  FROM " . $this->table . " ot
                  LEFT JOIN users u ON ot.created_by = u.id
                  WHERE ot.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    } catch (PDOException $e) {
        error_log("OfficeType getTypeById Error: " . $e->getMessage());
        return false;
    }
}

    // Create new office type
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table . " 
                      SET type_name = :type_name,
                          description = :description,
                          created_by = :created_by,
                          status = :status";
            
            $stmt = $this->conn->prepare($query);
            
            $this->type_name = htmlspecialchars(strip_tags($this->type_name));
            $this->description = htmlspecialchars(strip_tags($this->description));
            
            $stmt->bindParam(':type_name', $this->type_name);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':created_by', $this->created_by);
            $stmt->bindParam(':status', $this->status);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("OfficeType create Error: " . $e->getMessage());
            return false;
        }
    }

    // Update office type
    public function update() {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET type_name = :type_name,
                          description = :description,
                          status = :status
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $this->type_name = htmlspecialchars(strip_tags($this->type_name));
            $this->description = htmlspecialchars(strip_tags($this->description));
            
            $stmt->bindParam(':type_name', $this->type_name);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("OfficeType update Error: " . $e->getMessage());
            return false;
        }
    }

    // Delete office type (soft delete)
    public function delete($id) {
        try {
            $query = "UPDATE " . $this->table . " SET status = 0 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("OfficeType delete Error: " . $e->getMessage());
            return false;
        }
    }

    // Activate office type
    public function activate($id) {
        try {
            $query = "UPDATE " . $this->table . " SET status = 1 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("OfficeType activate Error: " . $e->getMessage());
            return false;
        }
    }

    // Check if type name already exists
    public function typeNameExists($type_name, $exclude_id = null) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE type_name = :type_name";
            if ($exclude_id) {
                $query .= " AND id != :exclude_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':type_name', $type_name);
            if ($exclude_id) {
                $stmt->bindParam(':exclude_id', $exclude_id);
            }
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            error_log("OfficeType typeNameExists Error: " . $e->getMessage());
            return false;
        }
    }


    // Get total count of active office types
public function getTotalCount() {
    try {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    } catch (PDOException $e) {
        error_log("OfficeType getTotalCount Error: " . $e->getMessage());
        return 0;
    }
}
}
?>