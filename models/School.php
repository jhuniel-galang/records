<?php
require_once __DIR__ . '/../config/database.php';

class School {
    private $conn;
    private $table = "schools";

    public $id;
    public $school_name;
    public $level;
    public $address;
    public $principal_name;
    public $created_at;
    public $updated_at;
    public $created_by;
    public $status;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all schools
    public function getAllSchools($includeInactive = false) {
        try {
            $query = "SELECT s.*, u.username as creator_name 
                      FROM " . $this->table . " s
                      LEFT JOIN users u ON s.created_by = u.id";
            
            if (!$includeInactive) {
                $query .= " WHERE s.status = 1";
            }
            
            $query .= " ORDER BY s.school_name ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("School getAllSchools Error: " . $e->getMessage());
            return [];
        }
    }

    public function getSchoolById($id) {
    try {
        $query = "SELECT s.*, u.username as creator_name, ot.type_name as office_type_name 
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.created_by = u.id
                  LEFT JOIN office_types ot ON s.office_type_id = ot.id
                  WHERE s.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->school_name = $row['school_name'];
            $this->office_type_id = $row['office_type_id'];
            $this->office_type_name = $row['office_type_name'];
            $this->address = $row['address'];
            $this->principal_name = $row['principal_name'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->created_by = $row['created_by'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log("School getSchoolById Error: " . $e->getMessage());
        return false;
    }
}

    // Create new school
public function create() {
    try {
        $query = "INSERT INTO " . $this->table . " 
                  SET school_name = :school_name,
                      office_type_id = :office_type_id,
                      address = :address,
                      principal_name = :principal_name,
                      created_by = :created_by,
                      status = :status";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->school_name = htmlspecialchars(strip_tags($this->school_name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->principal_name = htmlspecialchars(strip_tags($this->principal_name));
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        $stmt->bindParam(':school_name', $this->school_name);
        $stmt->bindParam(':office_type_id', $this->office_type_id);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':principal_name', $this->principal_name);
        $stmt->bindParam(':created_by', $this->created_by);
        $stmt->bindParam(':status', $this->status);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("School create Error: " . $e->getMessage());
        return false;
    }
}

// Update school
public function update() {
    try {
        $query = "UPDATE " . $this->table . " 
                  SET school_name = :school_name,
                      office_type_id = :office_type_id,
                      address = :address,
                      principal_name = :principal_name,
                      status = :status,
                      updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->school_name = htmlspecialchars(strip_tags($this->school_name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->principal_name = htmlspecialchars(strip_tags($this->principal_name));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(':school_name', $this->school_name);
        $stmt->bindParam(':office_type_id', $this->office_type_id);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':principal_name', $this->principal_name);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("School update Error: " . $e->getMessage());
        return false;
    }
}

    // Delete school (permanent delete)
public function delete($id) {
    try {
        // First, check if there are any documents linked to this school
        $checkQuery = "SELECT COUNT(*) as total FROM documents WHERE school_name = :school_name";
        $checkStmt = $this->conn->prepare($checkQuery);
        
        // Get school name first
        $getNameQuery = "SELECT school_name FROM " . $this->table . " WHERE id = :id";
        $getNameStmt = $this->conn->prepare($getNameQuery);
        $getNameStmt->bindParam(':id', $id);
        $getNameStmt->execute();
        $schoolData = $getNameStmt->fetch(PDO::FETCH_ASSOC);
        
        if($schoolData) {
            $checkStmt->bindParam(':school_name', $schoolData['school_name']);
            $checkStmt->execute();
            $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if($result['total'] > 0) {
                // School has documents, cannot delete
                return ['success' => false, 'message' => 'Cannot delete school because it has ' . $result['total'] . ' document(s) linked to it.'];
            }
        }
        
        // Delete the school permanently
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return ['success' => true, 'message' => 'School deleted successfully'];
        }
        return ['success' => false, 'message' => 'Failed to delete school'];
    } catch (PDOException $e) {
        error_log("School delete Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

    // Activate school
    public function activate($id) {
        try {
            $query = "UPDATE " . $this->table . " SET status = 1, updated_at = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("School activate Error: " . $e->getMessage());
            return false;
        }
    }

    // Count schools by level
    public function countByLevel() {
        try {
            $query = "SELECT level, COUNT(*) as total FROM " . $this->table . " WHERE status = 1 GROUP BY level";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("School countByLevel Error: " . $e->getMessage());
            return [];
        }
    }

    // Get schools by level
    public function getSchoolsByLevel($level) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE level = :level AND status = 1 ORDER BY school_name";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':level', $level);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("School getSchoolsByLevel Error: " . $e->getMessage());
            return [];
        }
    }

    // Check if school name already exists
    public function schoolNameExists($school_name, $exclude_id = null) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE school_name = :school_name";
            if ($exclude_id) {
                $query .= " AND id != :exclude_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':school_name', $school_name);
            if ($exclude_id) {
                $stmt->bindParam(':exclude_id', $exclude_id);
            }
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] > 0;
        } catch (PDOException $e) {
            error_log("School schoolNameExists Error: " . $e->getMessage());
            return false;
        }
    }


    // Get all schools with pagination, filtering, and sorting
public function getAllSchoolsPaginated($limit, $offset, $filters = [], $sort_by = 'school_name', $sort_order = 'ASC') {
    try {
        $query = "SELECT s.*, u.username as creator_name, ot.type_name as office_type_name 
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.created_by = u.id
                  LEFT JOIN office_types ot ON s.office_type_id = ot.id
                  WHERE 1=1";
        $params = [];
        
        // Apply filters
        if(!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= " AND (s.school_name LIKE :search OR s.address LIKE :search)";
            $params[':search'] = $search;
        }
        
        if(!empty($filters['office_type_id'])) {
            $query .= " AND s.office_type_id = :office_type_id";
            $params[':office_type_id'] = $filters['office_type_id'];
        }
        
        if(isset($filters['status']) && $filters['status'] !== '') {
            $query .= " AND s.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if(!empty($filters['created_by'])) {
            $query .= " AND s.created_by = :created_by";
            $params[':created_by'] = $filters['created_by'];
        }
        
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
        error_log("School getAllSchoolsPaginated Error: " . $e->getMessage());
        return [];
    }
}

// Count total schools with filters
public function countSchools($filters = []) {
    try {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " s WHERE 1=1";
        $params = [];
        
        if(!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= " AND (s.school_name LIKE :search OR s.address LIKE :search OR s.principal_name LIKE :search)";
            $params[':search'] = $search;
        }
        
        if(!empty($filters['level'])) {
            $query .= " AND s.level = :level";
            $params[':level'] = $filters['level'];
        }
        
        if(isset($filters['status']) && $filters['status'] !== '') {
            $query .= " AND s.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if(!empty($filters['created_by'])) {
            $query .= " AND s.created_by = :created_by";
            $params[':created_by'] = $filters['created_by'];
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
        
    } catch (PDOException $e) {
        error_log("School countSchools Error: " . $e->getMessage());
        return 0;
    }
}

// Get all users for filter (creators)
public function getAllCreators() {
    try {
        $query = "SELECT id, username FROM users ORDER BY username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("School getAllCreators Error: " . $e->getMessage());
        return [];
    }
}

// Get schools by office type
public function getSchoolsByOfficeType($office_type_id) {
    try {
        $query = "SELECT s.*, u.username as creator_name, ot.type_name as office_type_name
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.created_by = u.id
                  LEFT JOIN office_types ot ON s.office_type_id = ot.id
                  WHERE s.office_type_id = :office_type_id AND s.status = 1
                  ORDER BY s.school_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':office_type_id', $office_type_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("School getSchoolsByOfficeType Error: " . $e->getMessage());
        return [];
    }
}

// Get schools with office type info
public function getAllSchoolsWithOfficeType() {
    try {
        $query = "SELECT s.*, u.username as creator_name, ot.type_name as office_type_name
                  FROM " . $this->table . " s
                  LEFT JOIN users u ON s.created_by = u.id
                  LEFT JOIN office_types ot ON s.office_type_id = ot.id
                  WHERE s.status = 1
                  ORDER BY ot.type_name, s.school_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("School getAllSchoolsWithOfficeType Error: " . $e->getMessage());
        return [];
    }
}

public function countByOfficeType() {
    try {
        $query = "SELECT ot.type_name as office_type, COUNT(s.id) as total 
                  FROM " . $this->table . " s
                  LEFT JOIN office_types ot ON s.office_type_id = ot.id
                  WHERE s.status = 1
                  GROUP BY ot.type_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("School countByOfficeType Error: " . $e->getMessage());
        return [];
    }
}
}
?>