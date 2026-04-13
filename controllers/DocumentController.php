<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/School.php';
require_once __DIR__ . '/../models/ActivityLog.php';
require_once __DIR__ . '/../models/OfficeType.php';

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

        // List all documents with pagination, filtering, and sorting
public function index() {
    // Pagination settings
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    // Sorting settings
    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'id';
    $sort_order = isset($_GET['order']) && in_array($_GET['order'], ['ASC', 'DESC']) ? $_GET['order'] : 'DESC';
    
    // Build filters from GET parameters
    $filters = [];
    if(!empty($_GET['search'])) $filters['search'] = $_GET['search'];
    if(!empty($_GET['document_type'])) $filters['document_type'] = $_GET['document_type'];
    if(!empty($_GET['doc_year'])) $filters['doc_year'] = $_GET['doc_year'];
    if(!empty($_GET['date_from'])) $filters['date_from'] = $_GET['date_from'];
    if(!empty($_GET['date_to'])) $filters['date_to'] = $_GET['date_to'];
    
    $document = new Document();
    $documents = $document->getAllDocumentsPaginated($limit, $offset, $filters, $sort_by, $sort_order);
    $totalDocuments = $document->countDocuments($filters);
    $totalPages = ceil($totalDocuments / $limit);
    
    // Get statistics by document type
    $stats = $document->countByType();
    
    // Get all document types for filter
    $documentTypes = $document->getDocumentTypes();
    
    // Get available years for filter
    $available_years = $document->getDocumentYears();
    
    $data = [
        'documents' => $documents,
        'stats' => $stats,
        'total_documents' => $totalDocuments,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'limit' => $limit,
        'filters' => $filters,
        'sort_by' => $sort_by,
        'sort_order' => $sort_order,
        'documentTypes' => $documentTypes,
        'available_years' => $available_years,
        'title' => 'Document Management'
    ];
    
    $this->render('document/index.php', $data);
}

        // Show upload form
public function upload() {
    // Get schools grouped with office type info
    $school = new School();
    $schools = $school->getAllSchoolsWithOfficeType();
    
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
        $doc_title = trim($_POST['doc_title'] ?? '');
        $doc_year = $_POST['doc_year'] ?? '';
        $remarks = $_POST['remarks'] ?? '';
        $document_type = $_POST['document_type'] ?? 'Other';
        
        // Validate required fields
        if (empty($doc_title)) {
            $_SESSION['error'] = 'Document title is required';
            $this->redirect('index.php?controller=document&action=upload');
            return;
        }
        
        $document = new Document();
        
        // Check for duplicate document title
        if ($document->checkDuplicateTitle($doc_title, $school_name, $doc_year)) {
            $_SESSION['error'] = 'A document with the same title already exists for this school! Please use a different title.';
            $this->redirect('index.php?controller=document&action=upload');
            return;
        }
        
        // Check for duplicate file name
        $original_filename = $file['name'];
        if ($document->checkFileDuplicate($original_filename, $school_name)) {
            $_SESSION['error'] = 'A file with the same name already exists for this school! Please rename the file or upload a different one.';
            $this->redirect('index.php?controller=document&action=upload');
            return;
        }
        
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Error uploading file';
            $this->redirect('index.php?controller=document&action=upload');
            return;
        }
        
        // Check file size (max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            $_SESSION['error'] = 'File size too large. Maximum size is 10MB';
            $this->redirect('index.php?controller=document&action=upload');
            return;
        }
        
        // Get file extension
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Allowed file types
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx'];
        
        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['error'] = 'File type not allowed. Allowed types: ' . implode(', ', $allowed_types);
            $this->redirect('index.php?controller=document&action=upload');
            return;
        }
        
        // Calculate file hash for content-based duplicate checking
        $file_hash = md5_file($file['tmp_name']);
        
        // Check if the exact same file content already exists
        if ($document->checkFileHashDuplicate($file_hash)) {
            $_SESSION['error'] = 'This exact file has already been uploaded! Please upload a different file.';
            $this->redirect('index.php?controller=document&action=upload');
            return;
        }
        
        // Generate unique filename
        $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
        $upload_path = $this->uploadDir . $new_filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            
            $document->user_id = $_SESSION['user_id'];
            $document->school_name = $school_name;
            $document->file_name = $original_filename;
            $document->doc_title = $doc_title;
            $document->doc_year = $doc_year;
            $document->file_path = 'uploads/' . $new_filename;
            $document->file_hash = $file_hash;
            $document->file_type = $file_ext;
            $document->file_size = $file['size'];
            $document->remarks = $remarks;
            $document->document_type = $document_type;
            
            $newData = [
                'file_name' => $original_filename,
                'doc_title' => $doc_title,
                'doc_year' => $doc_year,
                'school_name' => $school_name,
                'document_type' => $document_type,
                'file_size' => $file['size'],
                'file_type' => $file_ext,
                'file_hash' => $file_hash
            ];
            
            if ($document->upload()) {
                $doc_id = $document->getLastInsertId();
                
                // If it's a Form 137 PDF, extract data
                if ($document_type == 'Form 137' && $file_ext == 'pdf') {
                    if (file_exists(__DIR__ . '/../helpers/PDFParser.php')) {
                        require_once __DIR__ . '/../helpers/PDFParser.php';
                        $parser = new PDFParser();
                        $extractedData = $parser->extractStudentInfo('uploads/' . $new_filename);
                        
                        if ($extractedData && !empty($extractedData)) {
                            $document->updateExtractedData($doc_id, $extractedData);
                            $_SESSION['success'] = 'Document uploaded successfully! Student data extracted.';
                        } else {
                            $_SESSION['success'] = 'Document uploaded successfully!';
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
                    'Uploaded document: ' . $original_filename,
                    'DocumentController',
                    'store',
                    null,
                    $newData,
                    'success'
                );
                
                $this->redirect('index.php?controller=document&action=index');
            } else {
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
        
        // Get file extension
        $file_ext = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
        
        // For PDF files
        if ($file_ext == 'pdf') {
            // Set headers for PDF viewing
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($document->file_name) . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            // Clear output buffer
            ob_clean();
            flush();
            
            // Read the file
            readfile($file_path);
            exit();
        }
        
        // For images
        if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file_path);
            finfo_close($finfo);
            
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: inline; filename="' . $document->file_name . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            readfile($file_path);
            exit();
        }
        
        die('Preview not available for this file type');
        
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
        
        // Check for duplicate document title (excluding current document)
        $doc_title = trim($_POST['doc_title'] ?? '');
        $school_name = $_POST['school_name'] ?? '';
        $doc_year = $_POST['doc_year'] ?? '';
        
        if (empty($doc_title)) {
            $_SESSION['error'] = 'Document title is required';
            $this->redirect('index.php?controller=document&action=edit&id=' . $_POST['id']);
            return;
        }
        
        if ($document->checkDuplicateTitle($doc_title, $school_name, $doc_year, $_POST['id'])) {
            $_SESSION['error'] = 'A document with the same title already exists for this school! Please use a different title.';
            $this->redirect('index.php?controller=document&action=edit&id=' . $_POST['id']);
            return;
        }
        
        // First, get the document to get old data and file name
        $document->getDocumentById($_POST['id']);
        
        $oldData = [
            'school_name' => $document->school_name,
            'doc_title' => $document->doc_title,
            'doc_year' => $document->doc_year,
            'remarks' => $document->remarks,
            'document_type' => $document->document_type
        ];
        
        // Set new values
        $document->id = $_POST['id'];
        $document->school_name = $_POST['school_name'];
        $document->doc_title = $doc_title;
        $document->doc_year = $doc_year;
        $document->remarks = $_POST['remarks'];
        $document->document_type = $_POST['document_type'];
        
        $newData = [
            'school_name' => $document->school_name,
            'doc_title' => $document->doc_title,
            'doc_year' => $document->doc_year,
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


        // Secure document preview (no download) - Allow all logged-in users to view
public function securePreview() {
    $id = $_GET['id'] ?? 0;
    
    $document = new Document();
    if($document->getDocumentById($id)) {
        
        // Allow any logged-in user to view documents
        // Remove the user_id check - just check if user is logged in
        if (!$this->isLoggedIn()) {
            die("You must be logged in to view this document.");
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



// AJAX endpoint to check for duplicate title
public function checkDuplicate() {
    $doc_title = $_GET['doc_title'] ?? '';
    $school_name = $_GET['school_name'] ?? '';
    
    $document = new Document();
    $isDuplicate = $document->checkDuplicateTitle($doc_title, $school_name);
    
    header('Content-Type: application/json');
    echo json_encode(['duplicate' => $isDuplicate]);
    exit();
}


    }    
}
?>