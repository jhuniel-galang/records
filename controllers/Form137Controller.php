<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/Form137.php';
require_once __DIR__ . '/../models/School.php';
require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../models/ActivityLog.php';

if (!class_exists('Form137Controller')) {
    class Form137Controller extends BaseController {
        
        private $activityLog;
        private $setting;
        
        public function __construct() {
            $this->startSession();
            
            if (!$this->isLoggedIn()) {
                $this->redirect('index.php?controller=auth&action=login');
            }
            
            $this->activityLog = new ActivityLog();
            $this->setting = new Setting();
        }

        // List all Form 137 documents
        public function index() {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $offset = ($page - 1) * $limit;
            
            $filters = [];
            if(!empty($_GET['search'])) $filters['search'] = $_GET['search'];
            if(!empty($_GET['school_name'])) $filters['school_name'] = $_GET['school_name'];
            if(!empty($_GET['date_from'])) $filters['date_from'] = $_GET['date_from'];
            if(!empty($_GET['date_to'])) $filters['date_to'] = $_GET['date_to'];
            
            $form137 = new Form137();
            $records = $form137->getAll($filters, $limit, $offset);
            $totalRecords = $form137->countAll($filters);
            $totalPages = ceil($totalRecords / $limit);
            
            // Get schools for filter
            $schools = $form137->getAllSchools();
            
            $data = [
                'records' => $records,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'limit' => $limit,
                'filters' => $filters,
                'schools' => $schools,
                'title' => 'Form 137 Management'
            ];
            
            $this->render('form137/index.php', $data);
        }

        // View Form 137 document
        public function view() {
            $id = $_GET['id'] ?? 0;
            
            $form137 = new Form137();
            $record = $form137->getById($id);
            
            if($record) {
                $data = [
                    'record' => $record,
                    'title' => 'View Form 137'
                ];
                $this->render('form137/view.php', $data);
            } else {
                $_SESSION['error'] = 'Form 137 document not found';
                $this->redirect('index.php?controller=form137&action=index');
            }
        }

        // Print Form 137 (direct print)
        public function printForm() {
            $id = $_GET['id'] ?? 0;
            
            $form137 = new Form137();
            $record = $form137->getById($id);
            
            if($record) {
                $data = [
                    'record' => $record,
                    'title' => 'Print Form 137'
                ];
                $this->render('form137/print.php', $data);
            } else {
                $_SESSION['error'] = 'Form 137 document not found';
                $this->redirect('index.php?controller=form137&action=index');
            }
        }

        // Generate Certificate
        public function certificate() {
            $id = $_GET['id'] ?? 0;
            $type = $_GET['type'] ?? 'good_moral';
            
            $form137 = new Form137();
            $record = $form137->getById($id);
            
            if($record) {
                $data = [
                    'record' => $record,
                    'type' => $type,
                    'setting' => $this->setting,
                    'title' => ucfirst(str_replace('_', ' ', $type)) . ' Certificate'
                ];
                $this->render('form137/certificate.php', $data);
            } else {
                $_SESSION['error'] = 'Form 137 document not found';
                $this->redirect('index.php?controller=form137&action=index');
            }
        }

        // Print Certificate
        public function printCertificate() {
            $id = $_GET['id'] ?? 0;
            $type = $_GET['type'] ?? 'good_moral';
            
            $form137 = new Form137();
            $record = $form137->getById($id);
            
            if($record) {
                $data = [
                    'record' => $record,
                    'type' => $type,
                    'setting' => $this->setting,
                    'title' => ucfirst(str_replace('_', ' ', $type)) . ' Certificate'
                ];
                $this->render('form137/print_certificate.php', $data);
            } else {
                $_SESSION['error'] = 'Form 137 document not found';
                $this->redirect('index.php?controller=form137&action=index');
            }
        }
    }
}
?>