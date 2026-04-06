<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Form 137 - <?php echo htmlspecialchars($record['file_name']); ?></title>
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            padding: 20px;
        }
        
        .print-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .action-buttons {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn-print {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 10px;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .document-content {
            border: 1px solid #ddd;
            padding: 30px;
            background: white;
        }
        
        iframe {
            width: 100%;
            height: 80vh;
            border: none;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="action-buttons no-print">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="index.php?controller=form137&action=view&id=<?php echo $record['id']; ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        <div class="document-content">
            <iframe src="index.php?controller=document&action=preview&id=<?php echo $record['id']; ?>" 
                    style="width: 100%; height: 80vh; border: none;"></iframe>
        </div>
    </div>
</body>
</html>