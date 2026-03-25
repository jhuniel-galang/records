<?php
require_once 'controllers/BaseController.php';
require_once 'models/School.php';
require_once 'models/User.php';

class DashboardController extends BaseController {
    
    public function index() {
        $this->startSession();
        
        if (!$this->isLoggedIn()) {
            $this->redirect('index.php?controller=auth&action=login');
        }

        $school = new School();
        $schools = $school->getAllSchools();

        $data = [
            'user_name' => $_SESSION['user_name'],
            'user_role' => $_SESSION['user_role'],
            'schools' => $schools
        ];

        $this->render('dashboard/index.php', $data);
    }
}
?>