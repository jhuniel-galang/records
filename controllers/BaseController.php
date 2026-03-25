<?php
class BaseController {
    protected function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function isLoggedIn() {
        $this->startSession();
        return isset($_SESSION['user_id']);
    }

    protected function redirect($url) {
        header("Location: $url");
        exit();
    }

    protected function render($viewName, $data = []) {
        // Extract data to make variables available in view
        if (!empty($data)) {
            extract($data);
        }
        
        // Fix the paths for including views
        $headerPath = __DIR__ . '/../views/layouts/header.php';
        $viewPath = __DIR__ . '/../views/' . $viewName;
        $footerPath = __DIR__ . '/../views/layouts/footer.php';
        
        if (file_exists($headerPath)) {
            require_once $headerPath;
        } else {
            die("Header file not found at: " . $headerPath);
        }
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View file not found at: " . $viewPath);
        }
        
        if (file_exists($footerPath)) {
            require_once $footerPath;
        } else {
            die("Footer file not found at: " . $footerPath);
        }
    }
}
?>