<?php
class PDFParser {
    
    // Simple PDF text extraction using regular expressions
    // For more accurate extraction, consider using a library like PDFParser
    public function extractText($filepath) {
        $fullpath = __DIR__ . '/../' . $filepath;
        
        if (!file_exists($fullpath)) {
            return false;
        }
        
        // Try to extract text using PDF to text conversion
        $content = file_get_contents($fullpath);
        
        // Extract text from PDF content
        $text = $this->parsePDFContent($content);
        
        return $text;
    }
    
    private function parsePDFContent($content) {
        // Simple text extraction from PDF
        $text = '';
        
        // Remove objects and other non-text data
        $content = preg_replace('/\d+ \d+ obj/', '', $content);
        $content = preg_replace('/stream(.*?)endstream/s', '', $content);
        
        // Extract text between BT and ET
        preg_match_all('/BT(.*?)ET/s', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $block) {
                // Extract text from TJ, Tj, etc.
                preg_match_all('/\(([^)]+)\)/s', $block, $textMatches);
                if (!empty($textMatches[1])) {
                    $text .= implode(' ', $textMatches[1]) . ' ';
                }
            }
        }
        
        // Clean up the text
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        return $text;
    }
    
    // Extract student information from PDF content
    public function extractStudentInfo($filepath) {
        $text = $this->extractText($filepath);
        
        if (!$text) {
            return false;
        }
        
        $studentInfo = [];
        
        // Extract LRN (Learner Reference Number) - looks for 12-digit number
        if (preg_match('/\b(\d{12})\b/', $text, $matches)) {
            $studentInfo['lrn'] = $matches[1];
        }
        
        // Extract Student Name (looks for patterns like "Name: ..." or capital letters)
        if (preg_match('/(?:Name|STUDENT)[\s:]+([A-Z][A-Z\s.,]+)/i', $text, $matches)) {
            $studentInfo['student_name'] = trim($matches[1]);
        } elseif (preg_match('/([A-Z][A-Z\s.,]+)\s+(?:Birth|Address|Parent)/i', $text, $matches)) {
            $studentInfo['student_name'] = trim($matches[1]);
        }
        
        // Extract Birth Date
        if (preg_match('/(?:Birth|Born)[\s:]+([A-Z][a-z]+\s+\d{1,2},\s+\d{4}|\d{1,2}\/\d{1,2}\/\d{4})/i', $text, $matches)) {
            $studentInfo['birth_date'] = $matches[1];
        } elseif (preg_match('/(\d{1,2}\/\d{1,2}\/\d{4})/', $text, $matches)) {
            $studentInfo['birth_date'] = $matches[1];
        }
        
        // Extract Gender
        if (preg_match('/(?:Sex|Gender)[\s:]+(Male|Female)/i', $text, $matches)) {
            $studentInfo['gender'] = $matches[1];
        }
        
        // Extract School
        if (preg_match('/(?:School|SCHOOL)[\s:]+([A-Z][a-z\s.]+)/i', $text, $matches)) {
            $studentInfo['school'] = trim($matches[1]);
        }
        
        // Extract Grade Level
        if (preg_match('/(?:Grade)[\s:]+(\d{1,2})/i', $text, $matches)) {
            $studentInfo['grade_level'] = $matches[1];
        }
        
        // Extract School Year
        if (preg_match('/(?:School Year|S\.Y\.)[\s:]+(\d{4}-\d{4})/i', $text, $matches)) {
            $studentInfo['school_year'] = $matches[1];
        }
        
        // Extract General Average
        if (preg_match('/(?:General Average|Average)[\s:]+(\d+\.?\d*)/i', $text, $matches)) {
            $studentInfo['general_average'] = $matches[1];
        }
        
        return $studentInfo;
    }
}
?>