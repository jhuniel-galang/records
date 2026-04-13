<?php
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../models/School.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Document.php';
require_once __DIR__ . '/../models/OfficeType.php';

class DashboardController extends BaseController {
    
    public function index() {
        $this->startSession();
        
        if (!$this->isLoggedIn()) {
            $this->redirect('index.php?controller=auth&action=login');
        }

        // Get filters for Document Type Analytics
        $filters = [];
        if(!empty($_GET['doc_year'])) $filters['doc_year'] = $_GET['doc_year'];
        if(!empty($_GET['office_type_id'])) $filters['office_type_id'] = $_GET['office_type_id'];
        if(!empty($_GET['school_id'])) $filters['school_id'] = $_GET['school_id'];
        
        // Get filters for Schools by Document Count
        $school_filters = [];
        if(!empty($_GET['school_year'])) $school_filters['doc_year'] = $_GET['school_year'];
        if(!empty($_GET['school_doc_type'])) $school_filters['document_type'] = $_GET['school_doc_type'];
        
        // Get all schools
        $school = new School();
        $all_schools = $school->getAllSchools();
        $total_schools = count($all_schools);
        
        // Get document statistics
        $document = new Document();
        
        // Get document type statistics with filters
        $document_type_stats = $document->getDocumentTypeStats($filters);
        $total_documents = 0;
        $total_documents_size = 0;
        foreach($document_type_stats as $stat) {
            $total_documents += $stat['total'];
            $total_documents_size += $stat['total_size'];
        }
        
        // Get recent documents with filters
        $recent_documents = $document->getRecentDocuments(10, $filters);
        
        // Get top schools by document count
        $top_schools = $document->getTopSchoolsByDocumentCount(5, $filters);
        
        // Get schools by document count (with school filters)
        $schools_by_document_count = $document->getSchoolsByDocumentCountWithFilters($school_filters);
        
        // Get documents by year
        $documents_by_year = $document->getDocumentsByYear($filters);
        
        // Get available years for filter
        $available_years = $document->getDocumentYears();
        
        // Get document types for filter
        $document_types = $document->getDocumentTypes();
        
        // Get office types for filter
        $officeType = new OfficeType();
        $office_types = $officeType->getActiveTypes();
        $total_office_types = count($office_types);
        
        // Get user count
        $user = new User();
        $total_users_count = count($user->getAllUsers());
        
        $data = [
            'user_name' => $_SESSION['user_name'],
            'user_role' => $_SESSION['user_role'],
            'schools' => $all_schools,
            'total_schools' => $total_schools,
            'total_documents' => $total_documents,
            'total_documents_size' => $total_documents_size,
            'document_type_stats' => $document_type_stats,
            'recent_documents' => $recent_documents,
            'top_schools' => $top_schools,
            'schools_by_document_count' => $schools_by_document_count,
            'documents_by_year' => $documents_by_year,
            'available_years' => $available_years,
            'document_types' => $document_types,
            'office_types' => $office_types,
            'all_schools' => $all_schools,
            'total_office_types' => $total_office_types,
            'total_users_count' => $total_users_count,
            'filters' => $filters,
            'school_filters' => $school_filters,
            'title' => 'Dashboard'
        ];
        
        $this->render('dashboard/index.php', $data);
    }
}
?>