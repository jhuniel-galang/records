<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ActivityLog.php';

class AuthController extends BaseController {
    
    private $activityLog; // Add this property
    
    public function __construct() {
        $this->activityLog = new ActivityLog(); // Initialize in constructor
    }
    
    public function login() {
        $this->startSession();
        
        if ($this->isLoggedIn()) {
            $this->redirect('index.php?controller=dashboard&action=index');
        }

        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = new User();
            if ($user->login($username, $password)) {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->name;
                $_SESSION['user_role'] = $user->role;
                $_SESSION['user_username'] = $user->username;
                
                // Log successful login
                $this->activityLog->log(
                    $user->id,
                    $user->username,
                    'LOGIN',
                    'User logged in successfully',
                    'AuthController',
                    'login',
                    null,
                    null,
                    'success'
                );
                
                $this->redirect('index.php?controller=dashboard&action=index');
            } else {
                // Log failed login attempt
                $this->activityLog->log(
                    null,
                    $username,
                    'LOGIN_FAILED',
                    'Failed login attempt for username: ' . $username,
                    'AuthController',
                    'login',
                    null,
                    null,
                    'failed'
                );
                
                $error = 'Invalid username/email or password';
            }
        }

        $this->render('auth/login.php', ['error' => $error]);
    }

    public function register() {
        $this->startSession();
        
        if ($this->isLoggedIn()) {
            $this->redirect('index.php?controller=dashboard&action=index');
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User();
            $user->name = $_POST['name'];
            $user->username = $_POST['username'];
            $user->email = $_POST['email'];
            $user->password = $_POST['password'];

            $userData = [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email
            ];

            if (!$user->checkExisting($user->username, $user->email)) {
                if ($user->register()) {
                    // Log successful registration
                    $this->activityLog->log(
                        null,
                        $user->username,
                        'REGISTER',
                        'New user registered: ' . $user->username,
                        'AuthController',
                        'register',
                        null,
                        $userData,
                        'success'
                    );
                    
                    $success = 'Registration successful! You can now login.';
                } else {
                    // Log failed registration
                    $this->activityLog->log(
                        null,
                        $user->username,
                        'REGISTER_FAILED',
                        'Failed to register user: ' . $user->username,
                        'AuthController',
                        'register',
                        null,
                        $userData,
                        'failed'
                    );
                    
                    $error = 'Registration failed. Please try again.';
                }
            } else {
                // Log duplicate registration attempt
                $this->activityLog->log(
                    null,
                    $user->username,
                    'REGISTER_DUPLICATE',
                    'Attempted to register with existing username/email: ' . $user->username,
                    'AuthController',
                    'register',
                    null,
                    $userData,
                    'failed'
                );
                
                $error = 'Username or email already exists';
            }
        }

        $this->render('auth/register.php', ['error' => $error, 'success' => $success]);
    }

    public function logout() {
        $this->startSession();
        
        // Log logout
        if(isset($_SESSION['user_id'])) {
            $this->activityLog->log(
                $_SESSION['user_id'],
                $_SESSION['user_username'],
                'LOGOUT',
                'User logged out',
                'AuthController',
                'logout',
                null,
                null,
                'success'
            );
        }
        
        session_destroy();
        $this->redirect('index.php?controller=auth&action=login');
    }
}
?>