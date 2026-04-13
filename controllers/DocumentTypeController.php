<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/DocumentType.php';
require_once __DIR__ . '/../models/ActivityLog.php';

if (!class_exists('DocumentTypeController')) {
    class DocumentTypeController extends BaseController {
        
        private $activityLog;
        
        public function __construct() {
            $this->startSession();
            
            // Check if user is logged in and is admin
            if (!$this->isLoggedIn()) {
                $this->redirect('index.php?controller=auth&action=login');
            }
            
            if ($_SESSION['user_role'] !== 'admin') {
                $_SESSION['error'] = 'You do not have permission to manage document types';
                $this->redirect('index.php?controller=dashboard&action=index');
            }
            
            $this->activityLog = new ActivityLog();
        }

        // List all document types
        public function index() {
            $documentType = new DocumentType();
            $types = $documentType->getAllTypes(true); // Include inactive
            
            $data = [
                'types' => $types,
                'total_types' => count($types),
                'active_count' => $documentType->getTotalCount(),
                'title' => 'Document Types Management'
            ];
            
            $this->render('document_type/index.php', $data);
        }

        // Show create form
        public function create() {
            $data = [
                'title' => 'Add Document Type'
            ];
            $this->render('document_type/create.php', $data);
        }

        // Store new document type
        public function store() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $documentType = new DocumentType();
                $documentType->type_name = $_POST['type_name'];
                $documentType->description = $_POST['description'];
                $documentType->created_by = $_SESSION['user_id'];
                $documentType->status = $_POST['status'] ?? 1;

                // Check if type name already exists
                if (!$documentType->typeNameExists($documentType->type_name)) {
                    $newData = [
                        'type_name' => $documentType->type_name,
                        'description' => $documentType->description,
                        'status' => $documentType->status
                    ];

                    if ($documentType->create()) {
                        // Log activity
                        $this->activityLog->log(
                            $_SESSION['user_id'],
                            $_SESSION['user_username'],
                            'CREATE_DOCUMENT_TYPE',
                            'Created new document type: ' . $documentType->type_name,
                            'DocumentTypeController',
                            'store',
                            null,
                            $newData,
                            'success'
                        );
                        
                        $_SESSION['success'] = 'Document type created successfully';
                        $this->redirect('index.php?controller=documenttype&action=index');
                    } else {
                        $_SESSION['error'] = 'Failed to create document type';
                    }
                } else {
                    $_SESSION['error'] = 'Document type name already exists';
                }
                
                $this->redirect('index.php?controller=documenttype&action=create');
            }
        }

        // Show edit form
        public function edit() {
            $id = $_GET['id'] ?? 0;
            
            $documentType = new DocumentType();
            if($documentType->getTypeById($id)) {
                $data = [
                    'type' => $documentType,
                    'title' => 'Edit Document Type'
                ];
                $this->render('document_type/edit.php', $data);
            } else {
                $_SESSION['error'] = 'Document type not found';
                $this->redirect('index.php?controller=documenttype&action=index');
            }
        }

        // Update document type
        public function update() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $documentType = new DocumentType();
                
                // Get old data for logging
                $documentType->getTypeById($_POST['id']);
                $oldData = [
                    'type_name' => $documentType->type_name,
                    'description' => $documentType->description,
                    'status' => $documentType->status
                ];
                
                $documentType->id = $_POST['id'];
                $documentType->type_name = $_POST['type_name'];
                $documentType->description = $_POST['description'];
                $documentType->status = $_POST['status'] ?? 1;
                
                $newData = [
                    'type_name' => $documentType->type_name,
                    'description' => $documentType->description,
                    'status' => $documentType->status
                ];

                // Check if type name exists (excluding current)
                if (!$documentType->typeNameExists($documentType->type_name, $documentType->id)) {
                    if ($documentType->update()) {
                        // Log activity
                        $this->activityLog->log(
                            $_SESSION['user_id'],
                            $_SESSION['user_username'],
                            'UPDATE_DOCUMENT_TYPE',
                            'Updated document type: ' . $documentType->type_name,
                            'DocumentTypeController',
                            'update',
                            $oldData,
                            $newData,
                            'success'
                        );
                        
                        $_SESSION['success'] = 'Document type updated successfully';
                    } else {
                        $_SESSION['error'] = 'Failed to update document type';
                    }
                } else {
                    $_SESSION['error'] = 'Document type name already exists';
                }
                
                $this->redirect('index.php?controller=documenttype&action=index');
            }
        }

        // Delete document type (permanent delete)
public function delete() {
    $id = $_GET['id'] ?? 0;
    
    $documentType = new DocumentType();
    $documentType->getTypeById($id);
    $typeData = [
        'id' => $documentType->id,
        'type_name' => $documentType->type_name
    ];
    
    $result = $documentType->delete($id);
    
    if($result['success']) {
        // Log activity
        $this->activityLog->log(
            $_SESSION['user_id'],
            $_SESSION['user_username'],
            'DELETE_DOCUMENT_TYPE',
            'Permanently deleted document type: ' . $documentType->type_name,
            'DocumentTypeController',
            'delete',
            $typeData,
            null,
            'success'
        );
        
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
    
    $this->redirect('index.php?controller=documenttype&action=index');
}

        // Activate document type
        public function activate() {
            $id = $_GET['id'] ?? 0;
            
            $documentType = new DocumentType();
            $documentType->getTypeById($id);
            
            if($documentType->activate($id)) {
                // Log activity
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'ACTIVATE_DOCUMENT_TYPE',
                    'Activated document type: ' . $documentType->type_name,
                    'DocumentTypeController',
                    'activate',
                    null,
                    ['status' => 1],
                    'success'
                );
                
                $_SESSION['success'] = 'Document type activated successfully';
            } else {
                $_SESSION['error'] = 'Failed to activate document type';
            }
            
            $this->redirect('index.php?controller=documenttype&action=index');
        }
    }
}
?>