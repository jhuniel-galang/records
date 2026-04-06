<?php
require_once __DIR__ . '/../assets/fpdf/fpdf.php';

class PDFGenerator {
    private $setting;
    private $conn;
    
    public function __construct($setting, $conn) {
        $this->setting = $setting;
        $this->conn = $conn;
    }
    
    // Generate Form 137 PDF and save to documents
    public function generateAndSaveForm137($record, $user_id, $username) {
        // Create PDF
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        
        // Header with DepEd Logo (if exists)
        if(file_exists('assets/images/deped_logo.png')) {
            $pdf->Image('assets/images/deped_logo.png', 10, 10, 20);
        }
        
        // Title
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, 'Republic of the Philippines', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, 'Department of Education', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, $this->setting->get('school_name'), 0, 1, 'C');
        $pdf->Cell(0, 5, $this->setting->get('division_address'), 0, 1, 'C');
        
        $pdf->Ln(5);
        
        // Form 137 Title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'PERMANENT RECORD', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 6, '(Form 137)', 0, 1, 'C');
        
        $pdf->Ln(5);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(5);
        
        // Student Information
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, 'I. STUDENT INFORMATION', 0, 1, 'L');
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(3);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(45, 7, 'LRN:', 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, $record['lrn'], 0, 1);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(45, 7, 'Student Name:', 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 7, strtoupper($record['student_name']), 0, 1);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(45, 7, 'Date of Birth:', 0, 0);
        $pdf->Cell(55, 7, date('F d, Y', strtotime($record['birth_date'])), 0, 0);
        $pdf->Cell(30, 7, 'Sex:', 0, 0);
        $pdf->Cell(0, 7, $record['gender'], 0, 1);
        
        $pdf->Cell(45, 7, 'Place of Birth:', 0, 0);
        $pdf->Cell(0, 7, $record['birth_place'], 0, 1);
        
        $pdf->Cell(45, 7, 'Address:', 0, 0);
        $pdf->MultiCell(0, 6, $record['address']);
        
        $pdf->Cell(45, 7, 'Parent/Guardian:', 0, 0);
        $pdf->Cell(0, 7, $record['parent_name'], 0, 1);
        
        $pdf->Cell(45, 7, 'Contact No.:', 0, 0);
        $pdf->Cell(0, 7, $record['parent_contact'], 0, 1);
        
        $pdf->Ln(3);
        
        // School Information
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, 'II. SCHOOL INFORMATION', 0, 1, 'L');
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(3);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(45, 7, 'School:', 0, 0);
        $pdf->Cell(0, 7, $record['school_name'], 0, 1);
        
        $pdf->Cell(45, 7, 'Grade Level:', 0, 0);
        $pdf->Cell(0, 7, $record['grade_level'], 0, 1);
        
        $pdf->Cell(45, 7, 'School Year:', 0, 0);
        $pdf->Cell(0, 7, $record['school_year'], 0, 1);
        
        $pdf->Cell(45, 7, 'Adviser:', 0, 0);
        $pdf->Cell(0, 7, $record['adviser'], 0, 1);
        
        $pdf->Ln(3);
        
        // Grades Table
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, 'III. GRADE REPORT', 0, 1, 'L');
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(3);
        
        // Table headers
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(70, 10, 'SUBJECT', 1, 0, 'C');
        $pdf->Cell(25, 10, '1st Qtr', 1, 0, 'C');
        $pdf->Cell(25, 10, '2nd Qtr', 1, 0, 'C');
        $pdf->Cell(25, 10, '3rd Qtr', 1, 0, 'C');
        $pdf->Cell(25, 10, '4th Qtr', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Final', 1, 1, 'C');
        
        $pdf->SetFont('Arial', '', 9);
        $grades = json_decode($record['grades_data'], true);
        
        if(is_array($grades) && count($grades) > 0) {
            foreach($grades as $subject => $grades_row) {
                $pdf->Cell(70, 8, substr($subject, 0, 40), 1, 0, 'L');
                $pdf->Cell(25, 8, $grades_row['q1'] ?? '-', 1, 0, 'C');
                $pdf->Cell(25, 8, $grades_row['q2'] ?? '-', 1, 0, 'C');
                $pdf->Cell(25, 8, $grades_row['q3'] ?? '-', 1, 0, 'C');
                $pdf->Cell(25, 8, $grades_row['q4'] ?? '-', 1, 0, 'C');
                $pdf->Cell(25, 8, $grades_row['final'] ?? '-', 1, 1, 'C');
            }
        } else {
            $pdf->Cell(195, 8, 'No grades recorded', 1, 1, 'C');
        }
        
        // General Average
        if($record['general_average'] > 0) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(170, 10, 'GENERAL AVERAGE:', 1, 0, 'R');
            $pdf->Cell(25, 10, number_format($record['general_average'], 2), 1, 1, 'C');
        }
        
        $pdf->Ln(5);
        
        // Signature Section
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, 'IV. CERTIFICATION', 0, 1, 'L');
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(10);
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, 'This certifies that the information above is true and correct from the official records.', 0, 1, 'C');
        $pdf->Ln(15);
        
        // Signatures
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(80, 6, '_________________________', 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0);
        $pdf->Cell(80, 6, '_________________________', 0, 1, 'C');
        $pdf->Cell(80, 4, 'Class Adviser', 0, 0, 'C');
        $pdf->Cell(40, 4, '', 0, 0);
        $pdf->Cell(80, 4, 'Parent/Guardian', 0, 1, 'C');
        
        $pdf->Ln(15);
        
        $pdf->Cell(0, 6, '_________________________', 0, 1, 'C');
        $pdf->Cell(0, 4, $this->setting->get('principal_name'), 0, 1, 'C');
        $pdf->Cell(0, 4, 'Schools Division Superintendent', 0, 1, 'C');
        
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 10, 'Generated on: ' . date('F d, Y h:i A'), 0, 0, 'C');
        
        // Save PDF to file
        $uploads_dir = __DIR__ . '/../uploads/';
        if (!file_exists($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        
        $filename = 'Form137_' . $record['lrn'] . '_' . time() . '.pdf';
        $filepath = 'uploads/' . $filename;
        $fullpath = $uploads_dir . $filename;
        
        $pdf->Output('F', $fullpath);
        
        // Save to documents table
        $document = new Document();
        $document->user_id = $user_id;
        $document->school_name = $record['school_name'];
        $document->file_name = 'Form 137 - ' . $record['student_name'] . ' (' . $record['lrn'] . ')';
        $document->file_path = $filepath;
        $document->file_type = 'pdf';
        $document->file_size = filesize($fullpath);
        $document->remarks = 'Automatically generated Form 137 for ' . $record['student_name'] . ' - School Year ' . $record['school_year'];
        $document->document_type = 'Form 137';
        $document->status = 1;
        
        return $document->upload();
    }
}
?>