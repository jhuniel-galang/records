<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class UserController extends BaseController {
    
    private $activityLog;
    
    public function __construct() {
        $this->startSession();
        
        // Check if user is logged in and is admin
        if (!$this->isLoggedIn()) {
            $this->redirect('index.php?controller=auth&action=login');
        }
        
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to access user management';
            $this->redirect('index.php?controller=dashboard&action=index');
        }
        
        $this->activityLog = new ActivityLog();
    }

    // List all users - NO LOGGING for viewing
    public function index() {
        $user = new User();
        $users = $user->getAllUsers();
        
        $data = [
            'users' => $users,
            'title' => 'User Management'
        ];
        
        $this->render('user/index.php', $data);
    }

    // View single user - NO LOGGING for viewing
    public function view() {
        $id = $_GET['id'] ?? 0;
        
        $user = new User();
        if($user->getUserById($id)) {
            $data = [
                'user' => $user,
                'title' => 'View User'
            ];
            $this->render('user/view.php', $data);
        } else {
            $_SESSION['error'] = 'User not found';
            $this->redirect('index.php?controller=user&action=index');
        }
    }

    // Show create form - NO LOGGING for viewing
    public function create() {
        $data = [
            'title' => 'Create New User'
        ];
        $this->render('user/create.php', $data);
    }

    // Show edit form - NO LOGGING for viewing
    public function edit() {
        $id = $_GET['id'] ?? 0;
        
        $user = new User();
        if($user->getUserById($id)) {
            $data = [
                'user' => $user,
                'title' => 'Edit User'
            ];
            $this->render('user/edit.php', $data);
        } else {
            $_SESSION['error'] = 'User not found';
            $this->redirect('index.php?controller=user&action=index');
        }
    }

    // Store new user - LOG THIS (CREATE action)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User();
            $user->name = $_POST['name'];
            $user->username = $_POST['username'];
            $user->email = $_POST['email'];
            $user->password = $_POST['password'];
            $user->role = $_POST['role'];
            $user->status = $_POST['status'] ?? 1;

            $newData = [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status
            ];

            // Check if username or email already exists
            if (!$user->checkExisting($user->username, $user->email)) {
                if ($user->create()) {
                    // Log success
                    $this->activityLog->log(
                        $_SESSION['user_id'],
                        $_SESSION['user_username'],
                        'CREATE_USER',
                        'Created user: ' . $user->username,
                        'UserController',
                        'store',
                        null,
                        $newData,
                        'success'
                    );
                    
                    $_SESSION['success'] = 'User created successfully';
                    $this->redirect('index.php?controller=user&action=index');
                } else {
                    // Log failure
                    $this->activityLog->log(
                        $_SESSION['user_id'],
                        $_SESSION['user_username'],
                        'CREATE_USER_FAILED',
                        'Failed to create user: ' . $user->username,
                        'UserController',
                        'store',
                        null,
                        $newData,
                        'failed'
                    );
                    
                    $_SESSION['error'] = 'Failed to create user';
                }
            } else {
                // Log duplicate attempt
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'CREATE_USER_DUPLICATE',
                    'Attempted to create user with existing username/email: ' . $user->username,
                    'UserController',
                    'store',
                    null,
                    $newData,
                    'failed'
                );
                
                $_SESSION['error'] = 'Username or email already exists';
            }
            
            $this->redirect('index.php?controller=user&action=create');
        }
    }

    // Update user - LOG THIS (UPDATE action)
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User();
            
            // Get old data for logging
            $user->getUserById($_POST['id']);
            $oldData = [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status
            ];
            
            $user->id = $_POST['id'];
            $user->name = $_POST['name'];
            $user->username = $_POST['username'];
            $user->email = $_POST['email'];
            $user->password = $_POST['password'] ?? '';
            $user->role = $_POST['role'];
            $user->status = $_POST['status'] ?? 1;
            
            $newData = [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status
            ];

            if ($user->update()) {
                // Log success
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'UPDATE_USER',
                    'Updated user: ' . $user->username,
                    'UserController',
                    'update',
                    $oldData,
                    $newData,
                    'success'
                );
                
                $_SESSION['success'] = 'User updated successfully';
            } else {
                // Log failure
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'UPDATE_USER_FAILED',
                    'Failed to update user: ' . $user->username,
                    'UserController',
                    'update',
                    $oldData,
                    $newData,
                    'failed'
                );
                
                $_SESSION['error'] = 'Failed to update user';
            }
            
            $this->redirect('index.php?controller=user&action=index');
        }
    }

    // Delete user (soft delete) - LOG THIS (DELETE action)
    public function delete() {
        $id = $_GET['id'] ?? 0;
        
        $user = new User();
        $user->getUserById($id);
        $userData = [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ];
        
        if($user->delete($id)) {
            // Log success
            $this->activityLog->log(
                $_SESSION['user_id'],
                $_SESSION['user_username'],
                'DEACTIVATE_USER',
                'Deactivated user: ' . $user->username,
                'UserController',
                'delete',
                $userData,
                ['status' => 0],
                'success'
            );
            
            $_SESSION['success'] = 'User deactivated successfully';
        } else {
            // Log failure
            $this->activityLog->log(
                $_SESSION['user_id'],
                $_SESSION['user_username'],
                'DEACTIVATE_USER_FAILED',
                'Failed to deactivate user: ' . $user->username,
                'UserController',
                'delete',
                $userData,
                null,
                'failed'
            );
            
            $_SESSION['error'] = 'Failed to deactivate user';
        }
        
        $this->redirect('index.php?controller=user&action=index');
    }

    // Activate user - LOG THIS (UPDATE action)
    public function activate() {
        $id = $_GET['id'] ?? 0;
        
        $user = new User();
        $user->getUserById($id);
        $userData = [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ];
        
        if($user->activate($id)) {
            // Log success
            $this->activityLog->log(
                $_SESSION['user_id'],
                $_SESSION['user_username'],
                'ACTIVATE_USER',
                'Activated user: ' . $user->username,
                'UserController',
                'activate',
                $userData,
                ['status' => 1],
                'success'
            );
            
            $_SESSION['success'] = 'User activated successfully';
        } else {
            // Log failure
            $this->activityLog->log(
                $_SESSION['user_id'],
                $_SESSION['user_username'],
                'ACTIVATE_USER_FAILED',
                'Failed to activate user: ' . $user->username,
                'UserController',
                'activate',
                $userData,
                null,
                'failed'
            );
            
            $_SESSION['error'] = 'Failed to activate user';
        }
        
        $this->redirect('index.php?controller=user&action=index');
    }
}
?>