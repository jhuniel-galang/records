<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table = "users";

    public $id;
    public $name;
    public $username;
    public $email;
    public $password;
    public $role;
    public $status;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE (username = :username OR email = :username) 
                  AND password = :password AND status = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                  SET name=:name, username=:username, email=:email, 
                      password=:password, role='uploader'";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = $this->password;

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function checkExisting($username, $email) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE username = :username OR email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Get all users
    public function getAllUsers() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single user by ID
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Create new user (admin function)
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET name=:name, username=:username, email=:email, 
                      password=:password, role=:role, status=:status";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = $this->password;
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':status', $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update user
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name=:name, username=:username, email=:email, 
                      role=:role, status=:status";
        
        // Only update password if provided
        if(!empty($this->password)) {
            $query .= ", password=:password";
        }
        
        $query .= " WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        
        if(!empty($this->password)) {
            $this->password = $this->password;
            $stmt->bindParam(':password', $this->password);
        }

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete user (soft delete by status)
    public function delete($id) {
        $query = "UPDATE " . $this->table . " SET status = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Activate user
    public function activate($id) {
        $query = "UPDATE " . $this->table . " SET status = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Count users by role
public function countByRole($role) {
    try {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE role = :role AND status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    } catch (PDOException $e) {
        error_log("User countByRole Error: " . $e->getMessage());
        return 0;
    }
}


    // Get all users with pagination and filtering
public function getAllUsersPaginated($limit, $offset, $filters = [], $sort_by = 'id', $sort_order = 'DESC') {
    try {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = [];
        
        // Apply filters
        if(!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= " AND (name LIKE :search OR username LIKE :search OR email LIKE :search)";
            $params[':search'] = $search;
        }
        
        if(!empty($filters['role'])) {
            $query .= " AND role = :role";
            $params[':role'] = $filters['role'];
        }
        
        if(isset($filters['status']) && $filters['status'] !== '') {
            $query .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if(!empty($filters['date_from'])) {
            $query .= " AND DATE(created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if(!empty($filters['date_to'])) {
            $query .= " AND DATE(created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        // Allowed sort columns
        $allowed_sort = ['id', 'name', 'username', 'role', 'status', 'created_at'];
        $sort_by = in_array($sort_by, $allowed_sort) ? $sort_by : 'id';
        $sort_order = $sort_order == 'ASC' ? 'ASC' : 'DESC';
        
        $query .= " ORDER BY " . $sort_by . " " . $sort_order . " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("User getAllUsersPaginated Error: " . $e->getMessage());
        return [];
    }
}

// Count total users with filters
public function countUsers($filters = []) {
    try {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        $params = [];
        
        if(!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query .= " AND (name LIKE :search OR username LIKE :search OR email LIKE :search)";
            $params[':search'] = $search;
        }
        
        if(!empty($filters['role'])) {
            $query .= " AND role = :role";
            $params[':role'] = $filters['role'];
        }
        
        if(isset($filters['status']) && $filters['status'] !== '') {
            $query .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if(!empty($filters['date_from'])) {
            $query .= " AND DATE(created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if(!empty($filters['date_to'])) {
            $query .= " AND DATE(created_at) <= :date_to";
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
        error_log("User countUsers Error: " . $e->getMessage());
        return 0;
    }
}


// Count users by status
public function countByStatus($status) {
    try {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    } catch (PDOException $e) {
        error_log("User countByStatus Error: " . $e->getMessage());
        return 0;
    }
}
}
?>