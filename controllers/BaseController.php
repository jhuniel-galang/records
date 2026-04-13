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

    protected function render($view, $data = []) {
        // Extract data to make variables available in view
        if (!empty($data)) {
            extract($data);
        }
        
        // Fix the paths for including views
        $headerPath = __DIR__ . '/../views/layouts/header.php';
        $viewPath = __DIR__ . '/../views/' . $view;
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

    // New method for print-friendly pages (without sidebar)
    protected function renderPrint($view, $data = []) {
        // Extract data to make variables available in view
        if (!empty($data)) {
            extract($data);
        }
        
        // Use print-specific header and footer (without sidebar)
        $headerPath = __DIR__ . '/../views/layouts/print_header.php';
        $viewPath = __DIR__ . '/../views/' . $view;
        $footerPath = __DIR__ . '/../views/layouts/print_footer.php';
        
        if (file_exists($headerPath)) {
            require_once $headerPath;
        } else {
            die("Print header file not found at: " . $headerPath);
        }
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View file not found at: " . $viewPath);
        }
        
        if (file_exists($footerPath)) {
            require_once $footerPath;
        } else {
            die("Print footer file not found at: " . $footerPath);
        }
    }
}
?>