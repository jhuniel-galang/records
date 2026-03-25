<?php
require_once __DIR__ . '/../config/database.php';

class Document {
    private $conn;
    private $table = "documents";

    public $id;
    public $user_id;
    public $school_name;
    public $file_name;
    public $file_path;
    public $file_type;
    public $file_size;
    public $remarks;
    public $document_type;
    public $uploader_at;
    public $delete_at;
    public $status;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Upload a new document
    public function upload() {
        try {
            $query = "INSERT INTO " . $this->table . " 
                      SET user_id = :user_id,
                          school_name = :school_name,
                          file_name = :file_name,
                          file_path = :file_path,
                          file_type = :file_type,
                          file_size = :file_size,
                          remarks = :remarks,
                          document_type = :document_type,
                          status = 1";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':school_name', $this->school_name);
            $stmt->bindParam(':file_name', $this->file_name);
            $stmt->bindParam(':file_path', $this->file_path);
            $stmt->bindParam(':file_type', $this->file_type);
            $stmt->bindParam(':file_size', $this->file_size);
            $stmt->bindParam(':remarks', $this->remarks);
            $stmt->bindParam(':document_type', $this->document_type);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Document upload Error: " . $e->getMessage());
            return false;
        }
    }

    // Get all documents
    public function getAllDocuments($includeInactive = false) {
        try {
            $query = "SELECT d.*, u.username as uploader_name 
                      FROM " . $this->table . " d
                      LEFT JOIN users u ON d.user_id = u.id";
            
            if (!$includeInactive) {
                $query .= " WHERE d.status = 1";
            }
            
            $query .= " ORDER BY d.uploader_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Document getAllDocuments Error: " . $e->getMessage());
            return [];
        }
    }

    // Get documents by user
    public function getDocumentsByUser($user_id) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE user_id = :user_id AND status = 1 
                      ORDER BY uploader_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Document getDocumentsByUser Error: " . $e->getMessage());
            return [];
        }
    }

    // Get documents by school
    public function getDocumentsBySchool($school_name) {
        try {
            $query = "SELECT d.*, u.username as uploader_name 
                      FROM " . $this->table . " d
                      LEFT JOIN users u ON d.user_id = u.id
                      WHERE d.school_name = :school_name AND d.status = 1 
                      ORDER BY d.uploader_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':school_name', $school_name);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Document getDocumentsBySchool Error: " . $e->getMessage());
            return [];
        }
    }

    // Get single document by ID
    public function getDocumentById($id) {
        try {
            $query = "SELECT d.*, u.username as uploader_name 
                      FROM " . $this->table . " d
                      LEFT JOIN users u ON d.user_id = u.id
                      WHERE d.id = :id LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->id = $row['id'];
                $this->user_id = $row['user_id'];
                $this->school_name = $row['school_name'];
                $this->file_name = $row['file_name'];
                $this->file_path = $row['file_path'];
                $this->file_type = $row['file_type'];
                $this->file_size = $row['file_size'];
                $this->remarks = $row['remarks'];
                $this->document_type = $row['document_type'];
                $this->uploader_at = $row['uploader_at'];
                $this->status = $row['status'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Document getDocumentById Error: " . $e->getMessage());
            return false;
        }
    }

    // Update document
    public function update() {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET school_name = :school_name,
                          remarks = :remarks,
                          document_type = :document_type
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':school_name', $this->school_name);
            $stmt->bindParam(':remarks', $this->remarks);
            $stmt->bindParam(':document_type', $this->document_type);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Document update Error: " . $e->getMessage());
            return false;
        }
    }

    // Soft delete document
    public function delete($id) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET status = 0, delete_at = NOW() 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Document delete Error: " . $e->getMessage());
            return false;
        }
    }

    // Restore document
    public function restore($id) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET status = 1, delete_at = NULL 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Document restore Error: " . $e->getMessage());
            return false;
        }
    }

    // Get documents by type
    public function getDocumentsByType($document_type) {
        try {
            $query = "SELECT d.*, u.username as uploader_name 
                      FROM " . $this->table . " d
                      LEFT JOIN users u ON d.user_id = u.id
                      WHERE d.document_type = :document_type AND d.status = 1 
                      ORDER BY d.uploader_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':document_type', $document_type);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Document getDocumentsByType Error: " . $e->getMessage());
            return [];
        }
    }

    // Count documents by type
    public function countByType() {
        try {
            $query = "SELECT document_type, COUNT(*) as total 
                      FROM " . $this->table . " 
                      WHERE status = 1 
                      GROUP BY document_type";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Document countByType Error: " . $e->getMessage());
            return [];
        }
    }

    // Get file size in human readable format
    public function getFileSize($bytes) {
        $bytes = floatval($bytes);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Get document types for dropdown
public function getDocumentTypes() {
    try {
        $query = "SELECT id, type_name FROM document_types WHERE status = 1 ORDER BY type_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Document getDocumentTypes Error: " . $e->getMessage());
        return [];
    }
}
}
?>