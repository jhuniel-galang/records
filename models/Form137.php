<?php
require_once __DIR__ . '/../config/database.php';

class Form137 {
    private $conn;
    private $table = "documents";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all Form 137 documents
    public function getAll($filters = [], $limit = 50, $offset = 0) {
        try {
            $query = "SELECT d.*, u.username as uploader_name 
                      FROM " . $this->table . " d
                      LEFT JOIN users u ON d.user_id = u.id
                      WHERE d.document_type = 'Form 137' AND d.status = 1";
            
            $params = [];
            
            if(!empty($filters['search'])) {
                $search = '%' . $filters['search'] . '%';
                $query .= " AND (d.file_name LIKE :search OR d.extracted_data LIKE :search)";
                $params[':search'] = $search;
            }
            
            if(!empty($filters['school_name'])) {
                $query .= " AND d.school_name LIKE :school_name";
                $params[':school_name'] = '%' . $filters['school_name'] . '%';
            }
            
            if(!empty($filters['date_from'])) {
                $query .= " AND DATE(d.uploader_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if(!empty($filters['date_to'])) {
                $query .= " AND DATE(d.uploader_at) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            $query .= " ORDER BY d.uploader_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Decode extracted_data for each result
            foreach($results as &$result) {
                if(isset($result['extracted_data'])) {
                    $result['extracted_data'] = json_decode($result['extracted_data'], true);
                }
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log("Form137 getAll Error: " . $e->getMessage());
            return [];
        }
    }

    // Get single Form 137 document by ID
    public function getById($id) {
        try {
            $query = "SELECT d.*, u.username as uploader_name 
                      FROM " . $this->table . " d
                      LEFT JOIN users u ON d.user_id = u.id
                      WHERE d.id = :id AND d.document_type = 'Form 137' LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if(isset($result['extracted_data'])) {
                    $result['extracted_data'] = json_decode($result['extracted_data'], true);
                }
                return $result;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Form137 getById Error: " . $e->getMessage());
            return false;
        }
    }

    // Count total Form 137 documents
    public function countAll($filters = []) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                      WHERE document_type = 'Form 137' AND status = 1";
            $params = [];
            
            if(!empty($filters['search'])) {
                $search = '%' . $filters['search'] . '%';
                $query .= " AND (file_name LIKE :search OR extracted_data LIKE :search)";
                $params[':search'] = $search;
            }
            
            if(!empty($filters['school_name'])) {
                $query .= " AND school_name LIKE :school_name";
                $params[':school_name'] = '%' . $filters['school_name'] . '%';
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
            error_log("Form137 countAll Error: " . $e->getMessage());
            return 0;
        }
    }

    // Get all schools for filter
    public function getAllSchools() {
        try {
            $query = "SELECT DISTINCT school_name FROM " . $this->table . " 
                      WHERE document_type = 'Form 137' AND status = 1 
                      AND school_name IS NOT NULL AND school_name != ''
                      ORDER BY school_name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Form137 getAllSchools Error: " . $e->getMessage());
            return [];
        }
    }
}
?>