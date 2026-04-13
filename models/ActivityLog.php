<?php
require_once __DIR__ . '/../config/database.php';

class ActivityLog {
    private $conn;
    private $table = "activity_logs";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Log an activity (without IP and User Agent)
public function log($user_id, $username, $action, $description = '', $controller = '', $method = '', $old_data = null, $new_data = null, $status = 'success') {
    try {
        $query = "INSERT INTO " . $this->table . " 
                  SET user_id = :user_id, 
                      username = :username, 
                      action = :action, 
                      description = :description, 
                      controller = :controller, 
                      method = :method, 
                      old_data = :old_data, 
                      new_data = :new_data, 
                      status = :status,
                      created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Convert arrays to JSON
        $old_data_json = $old_data ? json_encode($old_data) : null;
        $new_data_json = $new_data ? json_encode($new_data) : null;
        
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':controller', $controller);
        $stmt->bindParam(':method', $method);
        $stmt->bindParam(':old_data', $old_data_json);
        $stmt->bindParam(':new_data', $new_data_json);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
        
    } catch (PDOException $e) {
        error_log("ActivityLog Error: " . $e->getMessage());
        return false;
    }
}

    // Get all activity logs with pagination - FIXED VERSION
    public function getAllLogs($limit = 50, $offset = 0, $filters = []) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
            $params = [];
            
            // Apply filters
            if(!empty($filters['user_id'])) {
                $query .= " AND user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            if(!empty($filters['action'])) {
                $query .= " AND action LIKE :action";
                $params[':action'] = '%' . $filters['action'] . '%';
            }
            
            if(!empty($filters['date_from'])) {
                $query .= " AND DATE(created_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if(!empty($filters['date_to'])) {
                $query .= " AND DATE(created_at) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            if(!empty($filters['status'])) {
                $query .= " AND status = :status";
                $params[':status'] = $filters['status'];
            }
            
            $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            // IMPORTANT: Cast to int for LIMIT and OFFSET
            $limit_int = (int)$limit;
            $offset_int = (int)$offset;
            $stmt->bindValue(':limit', $limit_int, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset_int, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("ActivityLog getAllLogs Error: " . $e->getMessage());
            return [];
        }
    }

    // Count total logs with filters - FIXED VERSION
    public function countLogs($filters = []) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
            $params = [];
            
            if(!empty($filters['user_id'])) {
                $query .= " AND user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            if(!empty($filters['action'])) {
                $query .= " AND action LIKE :action";
                $params[':action'] = '%' . $filters['action'] . '%';
            }
            
            if(!empty($filters['date_from'])) {
                $query .= " AND DATE(created_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if(!empty($filters['date_to'])) {
                $query .= " AND DATE(created_at) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            if(!empty($filters['status'])) {
                $query .= " AND status = :status";
                $params[':status'] = $filters['status'];
            }
            
            $stmt = $this->conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['total'] : 0;
            
        } catch (PDOException $e) {
            error_log("ActivityLog countLogs Error: " . $e->getMessage());
            return 0;
        }
    }

    // Get unique actions for filter dropdown - FIXED VERSION
    public function getUniqueActions() {
        try {
            $query = "SELECT DISTINCT action FROM " . $this->table . " WHERE action IS NOT NULL AND action != '' ORDER BY action";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $results ? $results : [];
        } catch (PDOException $e) {
            error_log("ActivityLog getUniqueActions Error: " . $e->getMessage());
            return [];
        }
    }

    // Get logs by user ID
    public function getLogsByUser($user_id, $limit = 100) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE user_id = :user_id 
                      ORDER BY created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':user_id', $user_id);
            $limit_int = (int)$limit;
            $stmt->bindValue(':limit', $limit_int, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ActivityLog getLogsByUser Error: " . $e->getMessage());
            return [];
        }
    }

    // Get single log by ID
    public function getLogById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ActivityLog getLogById Error: " . $e->getMessage());
            return false;
        }
    }

    // Helper function to get client IP
    private function getClientIP() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    // Add this method to get connection for debugging
    public function getConnection() {
        return $this->conn;
    }
}
?>