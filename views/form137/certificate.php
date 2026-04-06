<?php
$extracted = $record['extracted_data'] ?? [];
$student_name = $extracted['student_name'] ?? 
                preg_replace('/\.pdf$/i', '', $record['file_name']);
$school = $record['school_name'] ?? $extracted['school'] ?? 'N/A';
$grade = $extracted['grade_level'] ?? 'N/A';
$school_year = $extracted['school_year'] ?? date('Y') . '-' . (date('Y')+1);
$lrn = $extracted['lrn'] ?? 'N/A';
$general_average = $extracted['general_average'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - <?php echo htmlspecialchars($student_name); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
            .certificate-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .certificate-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 40px;
            position: relative;
        }
        
        .certificate-border {
            border: 2px solid #daa520;
            padding: 30px;
            position: relative;
        }
        
        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .certificate-title {
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #8b4513;
            margin: 20px 0;
        }
        
        .certificate-subtitle {
            font-size: 16px;
            font-style: italic;
        }
        
        .certificate-body {
            text-align: center;
            margin: 40px 0;
            line-height: 2;
        }
        
        .student-name {
            font-size: 28px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px solid #daa520;
            display: inline-block;
            padding: 0 20px;
        }
        
        .certificate-text {
            font-size: 18px;
            margin: 25px 0;
        }
        
        .certificate-details {
            margin: 30px 0;
            text-align: left;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
        }
        
        .detail-row {
            margin: 12px 0;
            display: flex;
            border-bottom: 1px solid #eee;
            padding: 5px 0;
        }
        
        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }
        
        .signature-line {
            text-align: center;
            width: 250px;
        }
        
        .signature-line hr {
            margin: 30px 0 5px;
            width: 200px;
            border: none;
            border-top: 1px solid #000;
        }
        
        .action-buttons {
            text-align: center;
            padding: 20px;
            background: #f0f0f0;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        
        .btn-print, .btn-back {
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
            border: none;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-print {
            background: #28a745;
            color: white;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
        }
        
        .btn-print:hover, .btn-back:hover {
            opacity: 0.8;
        }
        
        .watermark {
            position: fixed;
            bottom: 50px;
            right: 50px;
            font-size: 60px;
            font-weight: bold;
            color: rgba(0,0,0,0.03);
            transform: rotate(-15deg);
            pointer-events: none;
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Print Certificate
            </button>
            <a href="index.php?controller=form137&action=view&id=<?php echo $record['id']; ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        <div class="certificate-border">
            <div class="certificate-header">
                <div style="font-size: 12px;">Republic of the Philippines</div>
                <div style="font-size: 14px; font-weight: bold;">Department of Education</div>
                <div style="font-size: 12px;"><?php echo htmlspecialchars($setting->get('school_name')); ?></div>
                <div style="font-size: 10px;"><?php echo htmlspecialchars($setting->get('division_address')); ?></div>
                
                <div class="certificate-title">
                    <?php 
                    if($type == 'good_moral') echo "CERTIFICATE OF GOOD MORAL";
                    elseif($type == 'enrollment') echo "CERTIFICATE OF ENROLLMENT";
                    else echo "CERTIFICATE OF COMPLETION";
                    ?>
                </div>
            </div>
            
            <div class="certificate-body">
                <div class="certificate-text">This is to certify that</div>
                <div class="student-name"><?php echo strtoupper(htmlspecialchars($student_name)); ?></div>
                
                <?php if($type == 'good_moral'): ?>
                <div class="certificate-text">
                    has conducted himself/herself with good moral character and 
                    has shown exemplary behavior during his/her stay in this institution.
                </div>
                <?php elseif($type == 'enrollment'): ?>
                <div class="certificate-text">
                    is officially enrolled in <strong>Grade <?php echo htmlspecialchars($grade); ?></strong> 
                    for the School Year <strong><?php echo htmlspecialchars($school_year); ?></strong>
                    at <strong><?php echo htmlspecialchars($school); ?></strong>.
                </div>
                <?php else: ?>
                <div class="certificate-text">
                    has successfully completed the requirements for <strong>Grade <?php echo htmlspecialchars($grade); ?></strong> 
                    for the School Year <strong><?php echo htmlspecialchars($school_year); ?></strong>
                    with an average of <strong><?php echo htmlspecialchars($general_average); ?></strong>.
                </div>
                <?php endif; ?>
                
                <div class="certificate-details">
                    <div class="detail-row">
                        <span class="detail-label">LRN:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($lrn); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">School:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($school); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Grade Level:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($grade); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">School Year:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($school_year); ?></span>
                    </div>
                </div>
                
                <div class="certificate-text">
                    Issued this <strong><?php echo date('jS'); ?></strong> day of <strong><?php echo date('F Y'); ?></strong>.
                </div>
            </div>
            
            <div class="signature-area">
                <div class="signature-line">
                    <hr>
                    <div>Class Adviser</div>
                </div>
                <div class="signature-line">
                    <hr>
                    <div><?php echo htmlspecialchars($setting->get('principal_name')); ?></div>
                    <div style="font-size: 10px;">Schools Division Superintendent</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="watermark no-print">CONFIDENTIAL</div>
</body>
</html>