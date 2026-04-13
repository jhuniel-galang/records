<?php
require_once __DIR__ . '/../config/database.php';

class DocumentType {
    private $conn;
    private $table = "document_types";

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

    // Get all document types
    public function getAllTypes($includeInactive = false) {
        try {
            $query = "SELECT dt.*, u.username as created_by_name 
                      FROM " . $this->table . " dt
                      LEFT JOIN users u ON dt.created_by = u.id";
            
            if (!$includeInactive) {
                $query .= " WHERE dt.status = 1";
            }
            
            $query .= " ORDER BY dt.type_name ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("DocumentType getAllTypes Error: " . $e->getMessage());
            return [];
        }
    }

    // Get active document types (for dropdown)
    public function getActiveTypes() {
        try {
            $query = "SELECT id, type_name FROM " . $this->table . " 
                      WHERE status = 1 
                      ORDER BY type_name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("DocumentType getActiveTypes Error: " . $e->getMessage());
            return [];
        }
    }

    // Get single document type by ID
    public function getTypeById($id) {
        try {
            $query = "SELECT dt.*, u.username as created_by_name 
                      FROM " . $this->table . " dt
                      LEFT JOIN users u ON dt.created_by = u.id
                      WHERE dt.id = :id LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->id = $row['id'];
                $this->type_name = $row['type_name'];
                $this->description = $row['description'];
                $this->status = $row['status'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                $this->created_by = $row['created_by'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("DocumentType getTypeById Error: " . $e->getMessage());
            return false;
        }
    }

    // Create new document type
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
            error_log("DocumentType create Error: " . $e->getMessage());
            return false;
        }
    }

    // Update document type
    public function update() {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET type_name = :type_name,
                          description = :description,
                          status = :status,
                          updated_at = NOW()
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
            error_log("DocumentType update Error: " . $e->getMessage());
            return false;
        }
    }

    // Delete document type (permanent delete)
public function delete($id) {
    try {
        // First, check if this document type is being used by any documents
        $checkQuery = "SELECT COUNT(*) as total FROM documents WHERE document_type = :type_name AND status = 1";
        $checkStmt = $this->conn->prepare($checkQuery);
        
        // Get the type name first
        $getNameQuery = "SELECT type_name FROM " . $this->table . " WHERE id = :id";
        $getNameStmt = $this->conn->prepare($getNameQuery);
        $getNameStmt->bindParam(':id', $id);
        $getNameStmt->execute();
        $typeData = $getNameStmt->fetch(PDO::FETCH_ASSOC);
        
        if($typeData) {
            $checkStmt->bindParam(':type_name', $typeData['type_name']);
            $checkStmt->execute();
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if($result['total'] > 0) {
                // Document type is being used, cannot delete
                return ['success' => false, 'message' => 'Cannot delete document type because it is used by ' . $result['total'] . ' document(s).'];
            }
        }
        
        // Delete the document type permanently
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return ['success' => true, 'message' => 'Document type deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete document type'];
    } catch (PDOException $e) {
        error_log("DocumentType delete Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

    // Activate document type
    public function activate($id) {
        try {
            $query = "UPDATE " . $this->table . " SET status = 1 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("DocumentType activate Error: " . $e->getMessage());
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
            error_log("DocumentType typeNameExists Error: " . $e->getMessage());
            return false;
        }
    }

    // Get total count
    public function getTotalCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("DocumentType getTotalCount Error: " . $e->getMessage());
            return 0;
        }
    }
}
?>