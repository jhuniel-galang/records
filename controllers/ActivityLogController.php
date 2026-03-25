<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../models/User.php';

class ActivityLogController extends BaseController {
    
    private $activityLog;
    
    public function __construct() {
        $this->startSession();
        
        // Check if user is logged in and is admin
        if (!$this->isLoggedIn()) {
            $this->redirect('index.php?controller=auth&action=login');
        }
        
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'You do not have permission to access activity logs';
            $this->redirect('index.php?controller=dashboard&action=index');
        }
        
        $this->activityLog = new ActivityLog();
    }

    // List all activity logs
public function index() {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = ($page - 1) * $limit;
    
    // TEMPORARILY IGNORE FILTERS FOR TESTING
    $filters = [];
    
    $logs = $this->activityLog->getAllLogs($limit, $offset, $filters);
    $totalLogs = $this->activityLog->countLogs($filters);
    $totalPages = ceil($totalLogs / $limit);
    
    // Get unique actions for filter dropdown
    $actions = $this->activityLog->getUniqueActions();
    
    // Get all users for filter dropdown
    $userModel = new User();
    $users = $userModel->getAllUsers();
    
    $data = [
        'logs' => $logs,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total_logs' => $totalLogs,
        'limit' => $limit,
        'filters' => $filters,
        'actions' => $actions,
        'users' => $users,
        'title' => 'Activity Logs'
    ];
    
    $this->render('activity_log/index.php', $data);
}

    // View single log entry
    public function view() {
        $id = $_GET['id'] ?? 0;
        
        $log = $this->activityLog->getLogById($id);
        
        if($log) {
            $data = [
                'log' => $log,
                'title' => 'View Activity Log'
            ];
            $this->render('activity_log/view.php', $data);
        } else {
            $_SESSION['error'] = 'Activity log not found';
            $this->redirect('index.php?controller=activitylog&action=index');
        }
    }

    // View user specific activities
    public function userActivity() {
        $user_id = $_GET['user_id'] ?? 0;
        
        if($user_id) {
            $userModel = new User();
            $user = $userModel->getUserById($user_id);
            
            if($user) {
                $logs = $this->activityLog->getLogsByUser($user_id, 100);
                
                $data = [
                    'user' => $user,
                    'logs' => $logs,
                    'title' => 'User Activity: ' . $user->username
                ];
                $this->render('activity_log/user_activity.php', $data);
            } else {
                $_SESSION['error'] = 'User not found';
                $this->redirect('index.php?controller=activitylog&action=index');
            }
        } else {
            $this->redirect('index.php?controller=activitylog&action=index');
        }
    }

    // Export logs to CSV
    public function export() {
        // Build filters from GET parameters
        $filters = [];
        if(!empty($_GET['user_id'])) $filters['user_id'] = $_GET['user_id'];
        if(!empty($_GET['action'])) $filters['action'] = $_GET['action'];
        if(!empty($_GET['date_from'])) $filters['date_from'] = $_GET['date_from'];
        if(!empty($_GET['date_to'])) $filters['date_to'] = $_GET['date_to'];
        if(!empty($_GET['status'])) $filters['status'] = $_GET['status'];
        
        // Get all logs without pagination for export
        $logs = $this->activityLog->getAllLogs(10000, 0, $filters);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="activity_logs_' . date('Y-m-d') . '.csv"');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, ['ID', 'Username', 'Action', 'Description', 'IP Address', 'Controller', 'Method', 'Status', 'Created At']);
        
        // Add data rows
        foreach($logs as $log) {
            fputcsv($output, [
                $log['id'],
                $log['username'],
                $log['action'],
                $log['description'],
                $log['ip_address'],
                $log['controller'],
                $log['method'],
                $log['status'],
                $log['created_at']
            ]);
        }
        
        fclose($output);
        exit();
    }

    // Get statistics
    public function statistics() {
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
        $stats = $this->activityLog->getStatistics($days);
        
        $data = [
            'stats' => $stats,
            'days' => $days,
            'title' => 'Activity Statistics'
        ];
        
        $this->render('activity_log/statistics.php', $data);
    }
}
?>