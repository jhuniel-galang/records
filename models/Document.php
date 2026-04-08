<?php
require_once __DIR__ . '/../config/database.php';

class Document {
    private $conn;
    private $table = "documents";

    public $id;
    public $user_id;
    public $school_name;
    public $file_name;
    public $doc_title;
    public $doc_year;
    public $file_path;
    public $file_type;
    public $file_size;
    public $remarks;
    public $document_type;
    public $uploader_at;
    public $delete_at;
    public $status;
    public $office_type_id;

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
                      doc_title = :doc_title,
                      doc_year = :doc_year,
                      file_path = :file_path,
                      file_hash = :file_hash,
                      file_type = :file_type,
                      file_size = :file_size,
                      remarks = :remarks,
                      document_type = :document_type,
                      status = 1";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':school_name', $this->school_name);
        $stmt->bindParam(':file_name', $this->file_name);
        $stmt->bindParam(':doc_title', $this->doc_title);
        $stmt->bindParam(':doc_year', $this->doc_year);
        $stmt->bindParam(':file_path', $this->file_path);
        $stmt->bindParam(':file_hash', $this->file_hash);
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
            $this->office_type_id = $row['office_type_id'];  // Add this line
            $this->file_name = $row['file_name'];
            $this->doc_title = $row['doc_title'];
            $this->doc_year = $row['doc_year'];
            $this->file_path = $row['file_path'];
            $this->file_hash = $row['file_hash'];
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
                      office_type_id = :office_type_id,
                      doc_title = :doc_title,
                      doc_year = :doc_year,
                      remarks = :remarks,
                      document_type = :document_type
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':school_name', $this->school_name);
        $stmt->bindParam(':office_type_id', $this->office_type_id);
        $stmt->bindParam(':doc_title', $this->doc_title);
        $stmt->bindParam(':doc_year', $this->doc_year);
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

    // Update extracted data
    public function updateExtractedData($id, $extracted_data) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET extracted_data = :extracted_data 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $extracted_json = json_encode($extracted_data);
            $stmt->bindParam(':extracted_data', $extracted_json);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Document updateExtractedData Error: " . $e->getMessage());
            return false;
        }
    }

    // Get document with extracted data
    public function getDocumentWithData($id) {
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
                if(isset($row['extracted_data'])) {
                    $row['extracted_data'] = json_decode($row['extracted_data'], true);
                }
                return $row;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Document getDocumentWithData Error: " . $e->getMessage());
            return false;
        }
    }

    // Get last insert ID
    public function getLastInsertId() {
        try {
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Document getLastInsertId Error: " . $e->getMessage());
            return 0;
        }
    }

    // Get all documents including inactive (for archive)
    public function getAllDocumentsWithInactive($filters = [], $limit = 50, $offset = 0) {
        try {
            $query = "SELECT d.*, u.username as uploader_name 
                      FROM " . $this->table . " d
                      LEFT JOIN users u ON d.user_id = u.id
                      WHERE 1=1";
            $params = [];
            
            if(!empty($filters['search'])) {
                $search = '%' . $filters['search'] . '%';
                $query .= " AND (d.file_name LIKE :search OR d.school_name LIKE :search OR d.doc_title LIKE :search)";
                $params[':search'] = $search;
            }
            
            if(!empty($filters['document_type'])) {
                $query .= " AND d.document_type = :document_type";
                $params[':document_type'] = $filters['document_type'];
            }
            
            if(!empty($filters['doc_year'])) {
                $query .= " AND d.doc_year = :doc_year";
                $params[':doc_year'] = $filters['doc_year'];
            }
            
            // Show only inactive documents (status = 0)
            $query .= " AND d.status = 0";
            
            $query .= " ORDER BY d.delete_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Document getAllDocumentsWithInactive Error: " . $e->getMessage());
            return [];
        }
    }

    // Count archived documents
    public function countArchivedDocuments($filters = []) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 0";
            $params = [];
            
            if(!empty($filters['search'])) {
                $search = '%' . $filters['search'] . '%';
                $query .= " AND (file_name LIKE :search OR school_name LIKE :search OR doc_title LIKE :search)";
                $params[':search'] = $search;
            }
            
            if(!empty($filters['document_type'])) {
                $query .= " AND document_type = :document_type";
                $params[':document_type'] = $filters['document_type'];
            }
            
            if(!empty($filters['doc_year'])) {
                $query .= " AND doc_year = :doc_year";
                $params[':doc_year'] = $filters['doc_year'];
            }
            
            $stmt = $this->conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Document countArchivedDocuments Error: " . $e->getMessage());
            return 0;
        }
    }

    // Permanently delete document (hard delete)
    public function permanentDelete($id) {
        try {
            // Get file path first
            $query = "SELECT file_path FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row) {
                $file_path = __DIR__ . '/../' . $row['file_path'];
                // Delete the physical file
                if(file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // Delete from database
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Document permanentDelete Error: " . $e->getMessage());
            return false;
        }
    }

    // Restore document from archive
    public function restoreFromArchive($id) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET status = 1, delete_at = NULL 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Document restoreFromArchive Error: " . $e->getMessage());
            return false;
        }
    }

    // Get all documents with pagination, filtering, and sorting
    public function getAllDocumentsPaginated($limit, $offset, $filters = [], $sort_by = 'id', $sort_order = 'DESC') {
        try {
            $query = "SELECT d.*, u.username as uploader_name 
                      FROM " . $this->table . " d
                      LEFT JOIN users u ON d.user_id = u.id
                      WHERE d.status = 1";
            $params = [];
            
            // Apply filters
            if(!empty($filters['search'])) {
                $search = '%' . $filters['search'] . '%';
                $query .= " AND (d.file_name LIKE :search OR d.school_name LIKE :search OR d.doc_title LIKE :search)";
                $params[':search'] = $search;
            }
            
            if(!empty($filters['document_type'])) {
                $query .= " AND d.document_type = :document_type";
                $params[':document_type'] = $filters['document_type'];
            }
            
            if(!empty($filters['doc_year'])) {
                $query .= " AND d.doc_year = :doc_year";
                $params[':doc_year'] = $filters['doc_year'];
            }
            
            if(!empty($filters['date_from'])) {
                $query .= " AND DATE(d.uploader_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if(!empty($filters['date_to'])) {
                $query .= " AND DATE(d.uploader_at) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            // Allowed sort columns
            $allowed_sort = ['id', 'file_name', 'doc_title', 'doc_year', 'uploader_at'];
            $sort_by = in_array($sort_by, $allowed_sort) ? $sort_by : 'id';
            $sort_order = $sort_order == 'ASC' ? 'ASC' : 'DESC';
            
            $query .= " ORDER BY " . $sort_by . " " . $sort_order . " LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Document getAllDocumentsPaginated Error: " . $e->getMessage());
            return [];
        }
    }

    // Count documents with filters
    public function countDocuments($filters = []) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 1";
            $params = [];
            
            if(!empty($filters['search'])) {
                $search = '%' . $filters['search'] . '%';
                $query .= " AND (file_name LIKE :search OR school_name LIKE :search OR doc_title LIKE :search)";
                $params[':search'] = $search;
            }
            
            if(!empty($filters['document_type'])) {
                $query .= " AND document_type = :document_type";
                $params[':document_type'] = $filters['document_type'];
            }
            
            if(!empty($filters['doc_year'])) {
                $query .= " AND doc_year = :doc_year";
                $params[':doc_year'] = $filters['doc_year'];
            }
            
            if(!empty($filters['date_from'])) {
                $query .= " AND DATE(uploader_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if(!empty($filters['date_to'])) {
                $query .= " AND DATE(uploader_at) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            $stmt = $this->conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            error_log("Document countDocuments Error: " . $e->getMessage());
            return 0;
        }
    }

    // Get unique years for filter
    public function getDocumentYears() {
        try {
            $query = "SELECT DISTINCT doc_year FROM " . $this->table . " 
                      WHERE doc_year IS NOT NULL AND doc_year != '' AND status = 1
                      ORDER BY doc_year DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Document getDocumentYears Error: " . $e->getMessage());
            return [];
        }
    }

    // Check if document title already exists
public function checkDuplicateTitle($doc_title, $school_name, $doc_year = null, $exclude_id = null) {
    try {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE doc_title = :doc_title 
                  AND school_name = :school_name 
                  AND status = 1";
        $params = [
            ':doc_title' => $doc_title,
            ':school_name' => $school_name
        ];
        
        if ($doc_year) {
            $query .= " AND doc_year = :doc_year";
            $params[':doc_year'] = $doc_year;
        }
        
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
            $params[':exclude_id'] = $exclude_id;
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    } catch (PDOException $e) {
        error_log("Document checkDuplicateTitle Error: " . $e->getMessage());
        return false;
    }
}


// Check if file already exists (by file name)
public function checkFileDuplicate($file_name, $school_name, $exclude_id = null) {
    try {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE file_name = :file_name 
                  AND school_name = :school_name 
                  AND status = 1";
        $params = [
            ':file_name' => $file_name,
            ':school_name' => $school_name
        ];
        
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
            $params[':exclude_id'] = $exclude_id;
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    } catch (PDOException $e) {
        error_log("Document checkFileDuplicate Error: " . $e->getMessage());
        return false;
    }
}

// Check if file content is duplicate (by file hash)
public function checkFileHashDuplicate($file_hash, $exclude_id = null) {
    try {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE file_hash = :file_hash 
                  AND status = 1";
        $params = [
            ':file_hash' => $file_hash
        ];
        
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
            $params[':exclude_id'] = $exclude_id;
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    } catch (PDOException $e) {
        error_log("Document checkFileHashDuplicate Error: " . $e->getMessage());
        return false;
    }
}



// Get document type statistics with filters
public function getDocumentTypeStats($filters = []) {
    try {
        $query = "SELECT document_type, COUNT(*) as total, SUM(file_size) as total_size 
                  FROM " . $this->table . " 
                  WHERE status = 1";
        $params = [];
        
        if(!empty($filters['doc_year'])) {
            $query .= " AND doc_year = :doc_year";
            $params[':doc_year'] = $filters['doc_year'];
        }
        
        if(!empty($filters['office_type_id'])) {
            $query .= " AND office_type_id = :office_type_id";
            $params[':office_type_id'] = $filters['office_type_id'];
        }
        
        if(!empty($filters['school_id'])) {
            $query .= " AND school_id = :school_id";
            $params[':school_id'] = $filters['school_id'];
        }
        
        $query .= " GROUP BY document_type ORDER BY total DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Document getDocumentTypeStats Error: " . $e->getMessage());
        return [];
    }
}

// Get recent documents with filters
public function getRecentDocuments($limit = 10, $filters = []) {
    try {
        $query = "SELECT d.*, u.username as uploader_name 
                  FROM " . $this->table . " d
                  LEFT JOIN users u ON d.user_id = u.id
                  WHERE d.status = 1";
        $params = [];
        
        if(!empty($filters['doc_year'])) {
            $query .= " AND d.doc_year = :doc_year";
            $params[':doc_year'] = $filters['doc_year'];
        }
        
        if(!empty($filters['office_type_id'])) {
            $query .= " AND d.office_type_id = :office_type_id";
            $params[':office_type_id'] = $filters['office_type_id'];
        }
        
        if(!empty($filters['school_id'])) {
            $query .= " AND d.school_id = :school_id";
            $params[':school_id'] = $filters['school_id'];
        }
        
        $query .= " ORDER BY d.uploader_at DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Document getRecentDocuments Error: " . $e->getMessage());
        return [];
    }
}

// Get top schools by document count
public function getTopSchoolsByDocumentCount($limit = 5, $filters = []) {
    try {
        $query = "SELECT s.school_name, ot.type_name as office_type_name, COUNT(d.id) as doc_count
                  FROM schools s
                  LEFT JOIN documents d ON d.school_name = s.school_name AND d.status = 1
                  LEFT JOIN office_types ot ON s.office_type_id = ot.id
                  WHERE s.status = 1";
        $params = [];
        
        if(!empty($filters['doc_year'])) {
            $query .= " AND d.doc_year = :doc_year";
            $params[':doc_year'] = $filters['doc_year'];
        }
        
        if(!empty($filters['office_type_id'])) {
            $query .= " AND s.office_type_id = :office_type_id";
            $params[':office_type_id'] = $filters['office_type_id'];
        }
        
        $query .= " GROUP BY s.id ORDER BY doc_count DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Document getTopSchoolsByDocumentCount Error: " . $e->getMessage());
        return [];
    }
}

// Get documents grouped by year
public function getDocumentsByYear($filters = []) {
    try {
        $query = "SELECT COALESCE(doc_year, 'No Year') as doc_year, COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE status = 1";
        $params = [];
        
        if(!empty($filters['office_type_id'])) {
            $query .= " AND office_type_id = :office_type_id";
            $params[':office_type_id'] = $filters['office_type_id'];
        }
        
        if(!empty($filters['school_id'])) {
            $query .= " AND school_id = :school_id";
            $params[':school_id'] = $filters['school_id'];
        }
        
        $query .= " GROUP BY doc_year ORDER BY doc_year DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Document getDocumentsByYear Error: " . $e->getMessage());
        return [];
    }
}
}
?>