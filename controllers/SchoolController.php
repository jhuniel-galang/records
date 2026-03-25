<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/School.php';
require_once __DIR__ . '/../models/ActivityLog.php';

if (!class_exists('SchoolController')) {
    class SchoolController extends BaseController {
        
        private $activityLog;
        
        public function __construct() {
            $this->startSession();
            
            // Check if user is logged in
            if (!$this->isLoggedIn()) {
                $this->redirect('index.php?controller=auth&action=login');
            }
            
            $this->activityLog = new ActivityLog();
        }

        // List all schools
        public function index() {
            $school = new School();
            $schools = $school->getAllSchools();
            
            // Get statistics
            $stats = $school->countByLevel();
            $elementaryCount = 0;
            $hsCount = 0;
            
            foreach ($stats as $stat) {
                if ($stat['level'] == 'Elementary') {
                    $elementaryCount = $stat['total'];
                } else if ($stat['level'] == 'HS') {
                    $hsCount = $stat['total'];
                }
            }
            
            $data = [
                'schools' => $schools,
                'elementary_count' => $elementaryCount,
                'hs_count' => $hsCount,
                'total_schools' => count($schools),
                'title' => 'School Management'
            ];
            
            $this->render('school/index.php', $data);
        }

        // View single school
        public function view() {
            $id = $_GET['id'] ?? 0;
            
            $school = new School();
            if($school->getSchoolById($id)) {
                $data = [
                    'school' => $school,
                    'title' => 'View School'
                ];
                $this->render('school/view.php', $data);
            } else {
                $_SESSION['error'] = 'School not found';
                $this->redirect('index.php?controller=school&action=index');
            }
        }

        // Show create form
        public function create() {
            $data = [
                'title' => 'Add New School'
            ];
            $this->render('school/create.php', $data);
        }

        // Store new school
        public function store() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $school = new School();
                $school->school_name = $_POST['school_name'];
                $school->level = $_POST['level'];
                $school->address = $_POST['address'];
                $school->principal_name = $_POST['principal_name'];
                $school->created_by = $_SESSION['user_id'];
                $school->status = $_POST['status'] ?? 1;

                // Check if school name already exists
                if (!$school->schoolNameExists($school->school_name)) {
                    $newData = [
                        'school_name' => $school->school_name,
                        'level' => $school->level,
                        'address' => $school->address,
                        'principal_name' => $school->principal_name,
                        'status' => $school->status
                    ];

                    if ($school->create()) {
                        
                        // Log activity
                        $this->activityLog->log(
                            $_SESSION['user_id'],
                            $_SESSION['user_username'],
                            'CREATE_SCHOOL',
                            'Created new school: ' . $school->school_name,
                            'SchoolController',
                            'store',
                            null,
                            $newData,
                            'success'
                        );
                        
                        $_SESSION['success'] = 'School created successfully';
                        $this->redirect('index.php?controller=school&action=index');
                    } else {
                        $_SESSION['error'] = 'Failed to create school';
                    }
                } else {
                    $_SESSION['error'] = 'School name already exists';
                }
                
                $this->redirect('index.php?controller=school&action=create');
            }
        }

        // Show edit form
        public function edit() {
            $id = $_GET['id'] ?? 0;
            
            $school = new School();
            if($school->getSchoolById($id)) {
                $data = [
                    'school' => $school,
                    'title' => 'Edit School'
                ];
                $this->render('school/edit.php', $data);
            } else {
                $_SESSION['error'] = 'School not found';
                $this->redirect('index.php?controller=school&action=index');
            }
        }

        // Update school
        public function update() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $school = new School();
                
                // Get old data for logging
                $school->getSchoolById($_POST['id']);
                $oldData = [
                    'school_name' => $school->school_name,
                    'level' => $school->level,
                    'address' => $school->address,
                    'principal_name' => $school->principal_name,
                    'status' => $school->status
                ];
                
                $school->id = $_POST['id'];
                $school->school_name = $_POST['school_name'];
                $school->level = $_POST['level'];
                $school->address = $_POST['address'];
                $school->principal_name = $_POST['principal_name'];
                $school->status = $_POST['status'] ?? 1;
                
                $newData = [
                    'school_name' => $school->school_name,
                    'level' => $school->level,
                    'address' => $school->address,
                    'principal_name' => $school->principal_name,
                    'status' => $school->status
                ];

                // Check if school name exists (excluding current school)
                if (!$school->schoolNameExists($school->school_name, $school->id)) {
                    if ($school->update()) {
                        // Log activity
                        $this->activityLog->log(
                            $_SESSION['user_id'],
                            $_SESSION['user_username'],
                            'UPDATE_SCHOOL',
                            'Updated school: ' . $school->school_name,
                            'SchoolController',
                            'update',
                            $oldData,
                            $newData,
                            'success'
                        );
                        
                        $_SESSION['success'] = 'School updated successfully';
                    } else {
                        $_SESSION['error'] = 'Failed to update school';
                    }
                } else {
                    $_SESSION['error'] = 'School name already exists';
                }
                
                $this->redirect('index.php?controller=school&action=index');
            }
        }

        // Delete school (soft delete)
        public function delete() {
            $id = $_GET['id'] ?? 0;
            
            $school = new School();
            $school->getSchoolById($id);
            $schoolData = [
                'id' => $school->id,
                'school_name' => $school->school_name,
                'level' => $school->level
            ];
            
            if($school->delete($id)) {
                // Log activity
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'DEACTIVATE_SCHOOL',
                    'Deactivated school: ' . $school->school_name,
                    'SchoolController',
                    'delete',
                    $schoolData,
                    ['status' => 0],
                    'success'
                );
                
                $_SESSION['success'] = 'School deactivated successfully';
            } else {
                $_SESSION['error'] = 'Failed to deactivate school';
            }
            
            $this->redirect('index.php?controller=school&action=index');
        }

        // Activate school
        public function activate() {
            $id = $_GET['id'] ?? 0;
            
            $school = new School();
            $school->getSchoolById($id);
            $schoolData = [
                'id' => $school->id,
                'school_name' => $school->school_name,
                'level' => $school->level
            ];
            
            if($school->activate($id)) {
                // Log activity
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'ACTIVATE_SCHOOL',
                    'Activated school: ' . $school->school_name,
                    'SchoolController',
                    'activate',
                    $schoolData,
                    ['status' => 1],
                    'success'
                );
                
                $_SESSION['success'] = 'School activated successfully';
            } else {
                $_SESSION['error'] = 'Failed to activate school';
            }
            
            $this->redirect('index.php?controller=school&action=index');
        }

        // View schools by level
        public function byLevel() {
            $level = $_GET['level'] ?? '';
            
            if (!in_array($level, ['Elementary', 'HS'])) {
                $this->redirect('index.php?controller=school&action=index');
            }
            
            $school = new School();
            $schools = $school->getSchoolsByLevel($level);
            
            $data = [
                'schools' => $schools,
                'level' => $level,
                'title' => $level . ' Schools'
            ];
            
            $this->render('school/by_level.php', $data);
        }
    }
}
?>