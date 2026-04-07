<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/OfficeType.php';
require_once __DIR__ . '/../models/ActivityLog.php';

if (!class_exists('OfficeTypeController')) {
    class OfficeTypeController extends BaseController {
        
        private $activityLog;
        
        public function __construct() {
            $this->startSession();
            
            // Check if user is logged in and is admin
            if (!$this->isLoggedIn()) {
                $this->redirect('index.php?controller=auth&action=login');
            }
            
            if ($_SESSION['user_role'] !== 'admin') {
                $_SESSION['error'] = 'You do not have permission to manage office types';
                $this->redirect('index.php?controller=dashboard&action=index');
            }
            
            $this->activityLog = new ActivityLog();
        }

        // List all office types
        public function index() {
            $officeType = new OfficeType();
            $types = $officeType->getAllTypes(true); // Include inactive
            
            $data = [
                'types' => $types,
                'total_types' => count($types),
                'active_count' => $officeType->getTotalCount(),
                'title' => 'Office Types Management'
            ];
            
            $this->render('office_type/index.php', $data);
        }

        // Show create form
        public function create() {
            $data = [
                'title' => 'Add Office Type'
            ];
            $this->render('office_type/create.php', $data);
        }

        // Store new office type
        public function store() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $officeType = new OfficeType();
                $officeType->type_name = $_POST['type_name'];
                $officeType->description = $_POST['description'];
                $officeType->created_by = $_SESSION['user_id'];
                $officeType->status = $_POST['status'] ?? 1;

                // Check if type name already exists
                if (!$officeType->typeNameExists($officeType->type_name)) {
                    $newData = [
                        'type_name' => $officeType->type_name,
                        'description' => $officeType->description,
                        'status' => $officeType->status
                    ];

                    if ($officeType->create()) {
                        // Log activity
                        $this->activityLog->log(
                            $_SESSION['user_id'],
                            $_SESSION['user_username'],
                            'CREATE_OFFICE_TYPE',
                            'Created new office type: ' . $officeType->type_name,
                            'OfficeTypeController',
                            'store',
                            null,
                            $newData,
                            'success'
                        );
                        
                        $_SESSION['success'] = 'Office type created successfully';
                        $this->redirect('index.php?controller=officetype&action=index');
                    } else {
                        $_SESSION['error'] = 'Failed to create office type';
                    }
                } else {
                    $_SESSION['error'] = 'Office type name already exists';
                }
                
                $this->redirect('index.php?controller=officetype&action=create');
            }
        }

        // Show edit form
        public function edit() {
            $id = $_GET['id'] ?? 0;
            
            $officeType = new OfficeType();
            $type = $officeType->getTypeById($id);
            
            if($type) {
                $data = [
                    'type' => $type,
                    'title' => 'Edit Office Type'
                ];
                $this->render('office_type/edit.php', $data);
            } else {
                $_SESSION['error'] = 'Office type not found';
                $this->redirect('index.php?controller=officetype&action=index');
            }
        }

        // Update office type
        public function update() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $officeType = new OfficeType();
                
                // Get old data for logging
                $oldType = $officeType->getTypeById($_POST['id']);
                $oldData = [
                    'type_name' => $oldType['type_name'],
                    'description' => $oldType['description'],
                    'status' => $oldType['status']
                ];
                
                $officeType->id = $_POST['id'];
                $officeType->type_name = $_POST['type_name'];
                $officeType->description = $_POST['description'];
                $officeType->status = $_POST['status'] ?? 1;
                
                $newData = [
                    'type_name' => $officeType->type_name,
                    'description' => $officeType->description,
                    'status' => $officeType->status
                ];

                // Check if type name exists (excluding current)
                if (!$officeType->typeNameExists($officeType->type_name, $officeType->id)) {
                    if ($officeType->update()) {
                        // Log activity
                        $this->activityLog->log(
                            $_SESSION['user_id'],
                            $_SESSION['user_username'],
                            'UPDATE_OFFICE_TYPE',
                            'Updated office type: ' . $officeType->type_name,
                            'OfficeTypeController',
                            'update',
                            $oldData,
                            $newData,
                            'success'
                        );
                        
                        $_SESSION['success'] = 'Office type updated successfully';
                    } else {
                        $_SESSION['error'] = 'Failed to update office type';
                    }
                } else {
                    $_SESSION['error'] = 'Office type name already exists';
                }
                
                $this->redirect('index.php?controller=officetype&action=index');
            }
        }

        // Delete office type (soft delete)
        public function delete() {
            $id = $_GET['id'] ?? 0;
            
            $officeType = new OfficeType();
            $type = $officeType->getTypeById($id);
            
            if($officeType->delete($id)) {
                // Log activity
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'DEACTIVATE_OFFICE_TYPE',
                    'Deactivated office type: ' . $type['type_name'],
                    'OfficeTypeController',
                    'delete',
                    ['id' => $id, 'type_name' => $type['type_name']],
                    ['status' => 0],
                    'success'
                );
                
                $_SESSION['success'] = 'Office type deactivated successfully';
            } else {
                $_SESSION['error'] = 'Failed to deactivate office type';
            }
            
            $this->redirect('index.php?controller=officetype&action=index');
        }

        // Activate office type
        public function activate() {
            $id = $_GET['id'] ?? 0;
            
            $officeType = new OfficeType();
            $type = $officeType->getTypeById($id);
            
            if($officeType->activate($id)) {
                // Log activity
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'ACTIVATE_OFFICE_TYPE',
                    'Activated office type: ' . $type['type_name'],
                    'OfficeTypeController',
                    'activate',
                    null,
                    ['status' => 1],
                    'success'
                );
                
                $_SESSION['success'] = 'Office type activated successfully';
            } else {
                $_SESSION['error'] = 'Failed to activate office type';
            }
            
            $this->redirect('index.php?controller=officetype&action=index');
        }
    }
}
?>