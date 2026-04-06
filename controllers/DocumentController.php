<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/School.php';
require_once __DIR__ . '/../models/ActivityLog.php';

if (!class_exists('DocumentController')) {
    class DocumentController extends BaseController {
        
        private $activityLog;
        private $uploadDir;
        
        public function __construct() {
            $this->startSession();
            
            // Check if user is logged in
            if (!$this->isLoggedIn()) {
                $this->redirect('index.php?controller=auth&action=login');
            }
            
            $this->activityLog = new ActivityLog();
            
            // Set upload directory
            $this->uploadDir = __DIR__ . '/../uploads/';
            
            // Create upload directory if it doesn't exist
            if (!file_exists($this->uploadDir)) {
                mkdir($this->uploadDir, 0777, true);
            }
        }

        // List all documents
        public function index() {
            $document = new Document();
            $documents = $document->getAllDocuments();
            
            // Get statistics by document type
            $stats = $document->countByType();
            
            $data = [
                'documents' => $documents,
                'stats' => $stats,
                'total_documents' => count($documents),
                'title' => 'Document Management'
            ];
            
            $this->render('document/index.php', $data);
        }

        // Show upload form
        // Show upload form
public function upload() {
    // Get schools for dropdown
    $school = new School();
    $schools = $school->getAllSchools();
    
    // Get document types from database
    $document = new Document();
    $documentTypes = $document->getDocumentTypes();
    
    $data = [
        'schools' => $schools,
        'documentTypes' => $documentTypes,
        'title' => 'Upload Document'
    ];
    
    $this->render('document/upload.php', $data);
}

        // Process document upload
public function store() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
        
        $file = $_FILES['document'];
        $school_name = $_POST['school_name'] ?? '';
        $remarks = $_POST['remarks'] ?? '';
        $document_type = $_POST['document_type'] ?? 'Other';
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Error uploading file';
            $this->redirect('index.php?controller=document&action=upload');
        }
        
        // Check file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            $_SESSION['error'] = 'File size too large. Maximum size is 10MB';
            $this->redirect('index.php?controller=document&action=upload');
        }
        
        // Get file extension
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Allowed file types
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx'];
        
        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['error'] = 'File type not allowed. Allowed types: ' . implode(', ', $allowed_types);
            $this->redirect('index.php?controller=document&action=upload');
        }
        
        // Generate unique filename
        $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
        $upload_path = $this->uploadDir . $new_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            
            $document = new Document();
            $document->user_id = $_SESSION['user_id'];
            $document->school_name = $school_name;
            $document->file_name = $file['name'];
            $document->file_path = 'uploads/' . $new_filename;
            $document->file_type = $file_ext;
            $document->file_size = $file['size'];
            $document->remarks = $remarks;
            $document->document_type = $document_type;
            
            $newData = [
                'file_name' => $file['name'],
                'school_name' => $school_name,
                'document_type' => $document_type,
                'file_size' => $file['size'],
                'file_type' => $file_ext
            ];
            
            // Get the document ID after upload by getting the last inserted ID from the model
            // We need to add a method to get the last insert ID from the document model
            if ($document->upload()) {
                // Get the last inserted ID from the document model
                $doc_id = $document->getLastInsertId();
                
                // If it's a Form 137 PDF, extract data
                if ($document_type == 'Form 137' && $file_ext == 'pdf') {
                    // Check if PDFParser exists
                    if (file_exists(__DIR__ . '/../helpers/PDFParser.php')) {
                        require_once __DIR__ . '/../helpers/PDFParser.php';
                        $parser = new PDFParser();
                        $extractedData = $parser->extractStudentInfo('uploads/' . $new_filename);
                        
                        if ($extractedData && !empty($extractedData)) {
                            $document->updateExtractedData($doc_id, $extractedData);
                            $_SESSION['success'] = 'Document uploaded successfully! Student data extracted.';
                        } else {
                            $_SESSION['success'] = 'Document uploaded successfully! (No data could be extracted)';
                        }
                    } else {
                        $_SESSION['success'] = 'Document uploaded successfully!';
                    }
                } else {
                    $_SESSION['success'] = 'Document uploaded successfully';
                }
                
                // Log activity
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'UPLOAD_DOCUMENT',
                    'Uploaded document: ' . $file['name'],
                    'DocumentController',
                    'store',
                    null,
                    $newData,
                    'success'
                );
                
                $this->redirect('index.php?controller=document&action=index');
            } else {
                // Delete uploaded file if database insert fails
                unlink($upload_path);
                $_SESSION['error'] = 'Failed to save document information';
                $this->redirect('index.php?controller=document&action=upload');
            }
        } else {
            $_SESSION['error'] = 'Failed to upload file';
            $this->redirect('index.php?controller=document&action=upload');
        }
    }
}

        // View document details
        public function view() {
            $id = $_GET['id'] ?? 0;
            
            $document = new Document();
            if($document->getDocumentById($id)) {
                
                // Get file size in human readable format
                $file_size_formatted = $document->getFileSize($document->file_size);
                
                $data = [
                    'document' => $document,
                    'file_size_formatted' => $file_size_formatted,
                    'title' => 'View Document'
                ];
                $this->render('document/view.php', $data);
            } else {
                $_SESSION['error'] = 'Document not found';
                $this->redirect('index.php?controller=document&action=index');
            }
        }

        // Preview document (strictly view-only, no download)
public function preview() {
    $id = $_GET['id'] ?? 0;
    
    $document = new Document();
    if($document->getDocumentById($id)) {
        
        $file_path = __DIR__ . '/../' . $document->file_path;
        
        if (!file_exists($file_path)) {
            die('File not found');
        }
        
        // Get file mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        
        // Disable all caching and download attempts
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: inline; filename="' . $document->file_name . '"');
        header('Content-Length: ' . filesize($file_path));
        
        // Security headers to prevent download
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        
        // Disable caching
        header('Cache-Control: no-cache, no-store, must-revalidate, private');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // For PDF files, add additional headers to disable download buttons
        if ($document->file_type == 'pdf') {
            header('Content-Security-Policy: default-src \'none\'; script-src \'none\'; style-src \'self\'; frame-ancestors \'self\';');
        }
        
        // Output file
        readfile($file_path);
        exit();
        
    } else {
        die('Document not found');
    }
}

        // Show edit form
        // Show edit form
public function edit() {
    $id = $_GET['id'] ?? 0;
    
    $document = new Document();
    if($document->getDocumentById($id)) {
        
        // Get schools for dropdown
        $school = new School();
        $schools = $school->getAllSchools();
        
        // Get document types from database
        $documentTypes = $document->getDocumentTypes();
        
        $data = [
            'document' => $document,
            'schools' => $schools,
            'documentTypes' => $documentTypes,
            'title' => 'Edit Document'
        ];
        $this->render('document/edit.php', $data);
    } else {
        $_SESSION['error'] = 'Document not found';
        $this->redirect('index.php?controller=document&action=index');
    }
}

        // Update document
        public function update() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                
                $document = new Document();
                
                // Get old data for logging
                $document->getDocumentById($_POST['id']);
                $oldData = [
                    'school_name' => $document->school_name,
                    'remarks' => $document->remarks,
                    'document_type' => $document->document_type
                ];
                
                $document->id = $_POST['id'];
                $document->school_name = $_POST['school_name'];
                $document->remarks = $_POST['remarks'];
                $document->document_type = $_POST['document_type'];
                
                $newData = [
                    'school_name' => $document->school_name,
                    'remarks' => $document->remarks,
                    'document_type' => $document->document_type
                ];
                
                if ($document->update()) {
                    // Log activity
                    $this->activityLog->log(
                        $_SESSION['user_id'],
                        $_SESSION['user_username'],
                        'UPDATE_DOCUMENT',
                        'Updated document: ' . $document->file_name,
                        'DocumentController',
                        'update',
                        $oldData,
                        $newData,
                        'success'
                    );
                    
                    $_SESSION['success'] = 'Document updated successfully';
                } else {
                    $_SESSION['error'] = 'Failed to update document';
                }
                
                $this->redirect('index.php?controller=document&action=index');
            }
        }

        public function delete() {
    $id = $_GET['id'] ?? 0;
    
    $document = new Document();
    $document->getDocumentById($id);
    $documentData = [
        'id' => $document->id,
        'file_name' => $document->file_name
    ];
    
    if($document->delete($id)) {
        // Log activity
        $this->activityLog->log(
            $_SESSION['user_id'],
            $_SESSION['user_username'],
            'DELETE_DOCUMENT',
            'Moved document to archive: ' . $document->file_name,
            'DocumentController',
            'delete',
            $documentData,
            ['status' => 0],
            'success'
        );
        
        $_SESSION['success'] = 'Document moved to archive';
    } else {
        $_SESSION['error'] = 'Failed to move document to archive';
    }
    
    $this->redirect('index.php?controller=document&action=index');
}

        // Restore document
        public function restore() {
            $id = $_GET['id'] ?? 0;
            
            $document = new Document();
            $document->getDocumentById($id);
            
            if($document->restore($id)) {
                // Log activity
                $this->activityLog->log(
                    $_SESSION['user_id'],
                    $_SESSION['user_username'],
                    'RESTORE_DOCUMENT',
                    'Restored document: ' . $document->file_name,
                    'DocumentController',
                    'restore',
                    null,
                    ['status' => 1],
                    'success'
                );
                
                $_SESSION['success'] = 'Document restored successfully';
            } else {
                $_SESSION['error'] = 'Failed to restore document';
            }
            
            $this->redirect('index.php?controller=document&action=index');
        }

        // Filter by document type
        public function byType() {
            $type = $_GET['type'] ?? '';
            
            $document = new Document();
            $documents = $document->getDocumentsByType($type);
            
            $data = [
                'documents' => $documents,
                'type' => $type,
                'title' => $type . ' Documents'
            ];
            
            $this->render('document/by_type.php', $data);
        }

        // Filter by school
        public function bySchool() {
            $school_name = $_GET['school'] ?? '';
            
            $document = new Document();
            $documents = $document->getDocumentsBySchool($school_name);
            
            $data = [
                'documents' => $documents,
                'school_name' => $school_name,
                'title' => 'Documents - ' . $school_name
            ];
            
            $this->render('document/by_school.php', $data);
        }


        // Secure document preview (no download)
public function securePreview() {
    $id = $_GET['id'] ?? 0;
    
    $document = new Document();
    if($document->getDocumentById($id)) {
        
        // Check permission (admin or owner)
        if ($_SESSION['user_role'] !== 'admin' && $document->user_id != $_SESSION['user_id']) {
            die("You don't have permission to view this document.");
        }
        
        // Log the preview action
        $this->activityLog->log(
            $_SESSION['user_id'],
            $_SESSION['user_username'],
            'PREVIEW_DOCUMENT',
            'Previewed document: ' . $document->file_name,
            'DocumentController',
            'securePreview',
            null,
            ['document_id' => $id, 'file_name' => $document->file_name],
            'success'
        );
        
        // Get file extension
        $file_ext = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
        $is_pdf = ($file_ext === 'pdf');
        $is_image = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp']);
        
        $data = [
            'document' => $document,
            'is_pdf' => $is_pdf,
            'is_image' => $is_image,
            'file_ext' => $file_ext,
            'title' => 'Preview: ' . $document->file_name
        ];
        
        $this->render('document/secure_preview.php', $data);
        
    } else {
        die("Document not found or access denied.");
    }
}



// Archive - Show deleted documents
public function archive() {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = ($page - 1) * $limit;
    
    $filters = [];
    if(!empty($_GET['search'])) $filters['search'] = $_GET['search'];
    if(!empty($_GET['document_type'])) $filters['document_type'] = $_GET['document_type'];
    
    $document = new Document();
    $documents = $document->getAllDocumentsWithInactive($filters, $limit, $offset);
    $totalDocuments = $document->countArchivedDocuments($filters);
    $totalPages = ceil($totalDocuments / $limit);
    
    // Get statistics by document type for archived documents
    $stats = $document->countByType();
    
    // Get all document types for filter
    $documentTypes = $document->getDocumentTypes();
    
    $data = [
        'documents' => $documents,
        'stats' => $stats,
        'total_documents' => $totalDocuments,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'limit' => $limit,
        'filters' => $filters,
        'documentTypes' => $documentTypes,
        'title' => 'Document Archive'
    ];
    
    $this->render('document/archive.php', $data);
}

// Permanent delete from archive
public function permanentDelete() {
    $id = $_GET['id'] ?? 0;
    
    $document = new Document();
    $document->getDocumentById($id);
    $documentData = [
        'id' => $document->id,
        'file_name' => $document->file_name
    ];
    
    if($document->permanentDelete($id)) {
        // Log activity
        $this->activityLog->log(
            $_SESSION['user_id'],
            $_SESSION['user_username'],
            'PERMANENT_DELETE_DOCUMENT',
            'Permanently deleted document: ' . $document->file_name,
            'DocumentController',
            'permanentDelete',
            $documentData,
            null,
            'success'
        );
        
        $_SESSION['success'] = 'Document permanently deleted from archive';
    } else {
        $_SESSION['error'] = 'Failed to delete document';
    }
    
    $this->redirect('index.php?controller=document&action=archive');
}

// Restore document from archive
public function restoreFromArchive() {
    $id = $_GET['id'] ?? 0;
    
    $document = new Document();
    $document->getDocumentById($id);
    
    if($document->restoreFromArchive($id)) {
        // Log activity
        $this->activityLog->log(
            $_SESSION['user_id'],
            $_SESSION['user_username'],
            'RESTORE_FROM_ARCHIVE',
            'Restored document from archive: ' . $document->file_name,
            'DocumentController',
            'restoreFromArchive',
            null,
            ['status' => 1],
            'success'
        );
        
        $_SESSION['success'] = 'Document restored from archive successfully';
    } else {
        $_SESSION['error'] = 'Failed to restore document';
    }
    
    $this->redirect('index.php?controller=document&action=archive');
}


    }    
}
?>