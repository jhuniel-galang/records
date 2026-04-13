<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Simple router
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Security: Sanitize controller and action names
$controller = preg_replace('/[^a-zA-Z0-9]/', '', $controller);
$action = preg_replace('/[^a-zA-Z0-9]/', '', $action);

// Map controller names to file names
$controllerMap = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'user' => 'UserController',
    'activitylog' => 'ActivityLogController',
    'school' => 'SchoolController',
    'document' => 'DocumentController',
    'documenttype' => 'DocumentTypeController',
    'form137' => 'Form137Controller',
    'officetype' => 'OfficeTypeController',
    'report' => 'ReportController'
];

if (array_key_exists($controller, $controllerMap)) {
    $controllerClass = $controllerMap[$controller];
    $controllerFile = __DIR__ . "/controllers/{$controllerClass}.php";
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        
        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            
            if (method_exists($controllerInstance, $action)) {
                $controllerInstance->$action();
            } else {
                die("Action '{$action}' not found in controller '{$controllerClass}'");
            }
        } else {
            die("Class '{$controllerClass}' not found in file");
        }
    } else {
        die("Controller file not found at: " . $controllerFile);
    }
} else {
    die("Controller '{$controller}' not found in mapping");
}
?>