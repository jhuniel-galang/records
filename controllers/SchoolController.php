<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/School.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../models/OfficeType.php';

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

        // List all schools with pagination and filtering
        public function index() {
            // Pagination settings
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;
            
            // Sorting settings
            $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'school_name';
            $sort_order = isset($_GET['order']) && in_array($_GET['order'], ['ASC', 'DESC']) ? $_GET['order'] : 'ASC';
            
            // Build filters from GET parameters
            $filters = [];
            if(!empty($_GET['search'])) {
                $filters['search'] = $_GET['search'];
            }
            if(!empty($_GET['office_type_id'])) {
                $filters['office_type_id'] = $_GET['office_type_id'];
            }
            if(isset($_GET['status']) && $_GET['status'] !== '') {
                $filters['status'] = $_GET['status'];
            }
            if(!empty($_GET['created_by'])) {
                $filters['created_by'] = $_GET['created_by'];
            }
            
            $school = new School();
            $schools = $school->getAllSchoolsPaginated($limit, $offset, $filters, $sort_by, $sort_order);
            $totalSchools = $school->countSchools($filters);
            $totalPages = ceil($totalSchools / $limit);
            
            // Get statistics by office type
            $stats = $school->countByOfficeType();
            
            // Get all creators for filter dropdown
            $creators = $school->getAllCreators();
            
            // Get office types for filter dropdown
            $officeType = new OfficeType();
            $officeTypes = $officeType->getActiveTypes();
            
            $data = [
                'schools' => $schools,
                'stats' => $stats,
                'officeTypes' => $officeTypes,
                'total_schools' => $totalSchools,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'limit' => $limit,
                'filters' => $filters,
                'sort_by' => $sort_by,
                'sort_order' => $sort_order,
                'creators' => $creators,
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

        public function create() {
            // Get office types for dropdown
            $officeType = new OfficeType();
            $officeTypes = $officeType->getActiveTypes();
            
            $data = [
                'officeTypes' => $officeTypes,
                'title' => 'Add New School'
            ];
            $this->render('school/create.php', $data);
        }
        
        // Store new school
        public function store() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $school = new School();
                $school->school_name = $_POST['school_name'];
                $school->office_type_id = $_POST['office_type_id'];
                $school->address = $_POST['address'];
                $school->principal_name = $_POST['principal_name'];
                $school->created_by = $_SESSION['user_id'];
                $school->status = $_POST['status'] ?? 1;

                // Check if school name already exists
                if (!$school->schoolNameExists($school->school_name)) {
                    $newData = [
                        'school_name' => $school->school_name,
                        'office_type_id' => $school->office_type_id,
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

        public function edit() {
            $id = $_GET['id'] ?? 0;
            
            $school = new School();
            if($school->getSchoolById($id)) {
                // Get office types for dropdown
                $officeType = new OfficeType();
                $officeTypes = $officeType->getActiveTypes();
                
                $data = [
                    'school' => $school,
                    'officeTypes' => $officeTypes,
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
                    'office_type_id' => $school->office_type_id,
                    'address' => $school->address,
                    'principal_name' => $school->principal_name,
                    'status' => $school->status
                ];
                
                $school->id = $_POST['id'];
                $school->school_name = $_POST['school_name'];
                $school->office_type_id = $_POST['office_type_id'];
                $school->address = $_POST['address'];
                $school->principal_name = $_POST['principal_name'];
                $school->status = $_POST['status'] ?? 1;
                
                $newData = [
                    'school_name' => $school->school_name,
                    'office_type_id' => $school->office_type_id,
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

        // Delete school (permanent delete)
public function delete() {
    $id = $_GET['id'] ?? 0;
    
    $school = new School();
    $school->getSchoolById($id);
    $schoolData = [
        'id' => $school->id,
        'school_name' => $school->school_name,
        'office_type_id' => $school->office_type_id
    ];
    
    $result = $school->delete($id);
    
    if($result['success']) {
        // Log activity
        $this->activityLog->log(
            $_SESSION['user_id'],
            $_SESSION['user_username'],
            'DELETE_SCHOOL',
            'Permanently deleted school: ' . $school->school_name,
            'SchoolController',
            'delete',
            $schoolData,
            null,
            'success'
        );
        
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
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
                'office_type_id' => $school->office_type_id
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
    }
}
?>