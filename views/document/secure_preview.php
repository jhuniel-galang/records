<?php
$file_ext = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
$is_pdf = ($file_ext === 'pdf');
$is_image = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: <?= htmlspecialchars($document->file_name) ?> | Records Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #7209b7;
            --success: #4cc9f0;
            --warning: #f72585;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a2e;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Header */
        .preview-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            z-index: 100;
            flex-shrink: 0;
            gap: 10px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .back-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateX(-3px);
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0;
        }

        .file-info i {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .file-details {
            min-width: 0;
        }

        .file-details h2 {
            font-size: 1.2rem;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 400px;
        }

        .file-details p {
            font-size: 0.85rem;
            opacity: 0.9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .security-badge {
            background: rgba(0,0,0,0.3);
            padding: 8px 16px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            border: 1px solid rgba(255,255,255,0.2);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .security-badge i {
            color: #4cc9f0;
        }

        /* Main Container */
        .main-container {
            flex: 1;
            display: flex;
            overflow: hidden;
            position: relative;
        }

        /* Preview Section */
        .preview-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #2d2d3a;
            min-width: 0;
            height: 100%;
        }

        /* Scrollable Preview Area */
        .scrollable-preview {
            flex: 1;
            overflow: auto;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            background: #0f0f1a;
            position: relative;
            min-height: 0;
        }

        /* PDF Viewer */
        .pdf-viewer {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #pdf-frame {
            width: 100%;
            height: 100%;
            min-height: 500px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
        }

        /* Image Viewer */
        .image-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
        }

        .secure-image {
            display: block;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
            border-radius: 4px;
            max-width: 100%;
            height: auto;
        }

        /* Info Panel */
        .info-panel {
            width: 300px;
            background: rgba(26, 26, 46, 0.98);
            backdrop-filter: blur(10px);
            border-left: 1px solid rgba(255,255,255,0.1);
            color: white;
            padding: 20px;
            overflow-y: auto;
            flex-shrink: 0;
            height: 100%;
        }

        .info-panel h3 {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            top: 0;
            background: rgba(26, 26, 46, 0.98);
            backdrop-filter: blur(10px);
            z-index: 5;
            font-size: 1.1rem;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-size: 0.75rem;
            color: #a0a0c0;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            background: rgba(255,255,255,0.08);
            padding: 10px 12px;
            border-radius: 8px;
            word-break: break-word;
            font-size: 0.9rem;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .remarks-box {
            background: rgba(255,255,255,0.05);
            padding: 12px;
            border-radius: 8px;
            margin-top: 5px;
            font-style: italic;
            border-left: 3px solid var(--primary);
            font-size: 0.9rem;
        }

        .security-note {
            margin-top: 25px;
            padding: 15px;
            background: rgba(247, 37, 133, 0.15);
            border-radius: 8px;
            border-left: 3px solid var(--warning);
            font-size: 0.85rem;
        }

        /* Loading Spinner */
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
            z-index: 10;
            background: rgba(0,0,0,0.7);
            padding: 30px;
            border-radius: 10px;
        }

        .loading i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: min(100px, 10vw);
            font-weight: bold;
            color: rgba(255,255,255,0.03);
            white-space: nowrap;
            pointer-events: none;
            z-index: 1;
            text-transform: uppercase;
            letter-spacing: 10px;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.2);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }
            
            .info-panel {
                width: 100%;
                border-left: none;
                border-top: 1px solid rgba(255,255,255,0.1);
                max-height: 250px;
            }
            
            .header-left {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .file-details h2 {
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="preview-header">
        <div class="header-left">
            <a href="index.php?controller=document&action=view&id=<?= $document->id ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <div class="file-info">
                <i class="fas fa-<?= $is_pdf ? 'file-pdf' : 'file-image' ?>" style="color: <?= $is_pdf ? '#ff6b6b' : '#4cc9f0' ?>"></i>
                <div class="file-details">
                    <h2><?= htmlspecialchars($document->file_name) ?></h2>
                    <p>Uploaded by: <?= htmlspecialchars($document->uploader_name ?? 'Unknown') ?> • <?= date('M d, Y h:i A', strtotime($document->uploader_at)) ?></p>
                </div>
            </div>
        </div>
        <div class="security-badge">
            <i class="fas fa-lock"></i> Secure View Only
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Watermark -->
        <div class="watermark">CONFIDENTIAL</div>

        <!-- Preview Section -->
        <div class="preview-section">
            <div class="scrollable-preview" id="scrollablePreview">
                <?php if ($is_pdf): ?>
                <!-- PDF Viewer using iframe -->
                <div class="pdf-viewer">
                    <iframe id="pdf-frame" src="index.php?controller=document&action=preview&id=<?= $document->id ?>" 
                            title="PDF Viewer"
                            oncontextmenu="return false;">
                    </iframe>
                </div>
                
                <?php elseif ($is_image): ?>
                <!-- Image Viewer -->
                <div class="image-wrapper">
                    <img src="index.php?controller=document&action=preview&id=<?= $document->id ?>" 
                         class="secure-image" 
                         alt="<?= htmlspecialchars($document->file_name) ?>"
                         oncontextmenu="return false;"
                         ondragstart="return false;">
                </div>
                
                <?php else: ?>
                <!-- Unsupported format -->
                <div style="text-align: center; color: white; padding: 30px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--warning); margin-bottom: 1rem;"></i>
                    <h2>Preview not available</h2>
                    <p style="margin-top: 1rem; color: var(--gray);">This file type cannot be previewed.</p>
                    <p style="font-size: 0.9rem;">File type: <?= strtoupper($file_ext) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="info-panel">
            <h3><i class="fas fa-info-circle"></i> Document Information</h3>
            
            <div class="info-item">
                <div class="info-label">Document Title</div>
                <div class="info-value">
                    <i class="fas fa-file-alt" style="margin-right: 8px; opacity: 0.7;"></i>
                    <?= htmlspecialchars($document->doc_title ?: 'Untitled') ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Document Year</div>
                <div class="info-value">
                    <i class="fas fa-calendar" style="margin-right: 8px; opacity: 0.7;"></i>
                    <?= htmlspecialchars($document->doc_year ?: 'N/A') ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">School/Office</div>
                <div class="info-value">
                    <i class="fas fa-school" style="margin-right: 8px; opacity: 0.7;"></i>
                    <?= htmlspecialchars($document->school_name ?: 'N/A') ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Document Type</div>
                <div class="info-value">
                    <i class="fas fa-tag" style="margin-right: 8px; opacity: 0.7;"></i>
                    <?= htmlspecialchars($document->document_type ?: 'N/A') ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">File Type</div>
                <div class="info-value">
                    <i class="fas fa-<?= $is_pdf ? 'file-pdf' : 'file' ?>" style="margin-right: 8px; opacity: 0.7;"></i>
                    <?= strtoupper($file_ext) ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">File Size</div>
                <div class="info-value">
                    <i class="fas fa-weight-hanging" style="margin-right: 8px; opacity: 0.7;"></i>
                    <?php
                    $size = $document->file_size;
                    if ($size >= 1073741824) {
                        echo number_format($size / 1073741824, 2) . ' GB';
                    } elseif ($size >= 1048576) {
                        echo number_format($size / 1048576, 2) . ' MB';
                    } elseif ($size >= 1024) {
                        echo number_format($size / 1024, 2) . ' KB';
                    } else {
                        echo $size . ' bytes';
                    }
                    ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Uploaded By</div>
                <div class="info-value">
                    <i class="fas fa-user" style="margin-right: 8px; opacity: 0.7;"></i>
                    <?= htmlspecialchars($document->uploader_name ?? 'Unknown') ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Upload Date</div>
                <div class="info-value">
                    <i class="fas fa-calendar-alt" style="margin-right: 8px; opacity: 0.7;"></i>
                    <?= date('F d, Y', strtotime($document->uploader_at)) ?><br>
                    <i class="fas fa-clock" style="margin-right: 8px; opacity: 0.7;"></i>
                    <?= date('h:i A', strtotime($document->uploader_at)) ?>
                </div>
            </div>
            
            <?php if (!empty($document->remarks)): ?>
            <div class="info-item">
                <div class="info-label">Remarks</div>
                <div class="remarks-box">
                    <i class="fas fa-quote-left" style="opacity: 0.5; margin-right: 5px;"></i>
                    <?= nl2br(htmlspecialchars($document->remarks)) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="security-note">
                <i class="fas fa-lock"></i>
                <strong>Secure View Only</strong>
                <p style="margin-top: 8px; font-size: 0.8rem; line-height: 1.4;">
                    This document is protected. Downloading, printing, and saving are disabled for security purposes.
                </p>
                <hr style="margin: 12px 0; border-color: rgba(255,255,255,0.1);">
                <p style="font-size: 0.75rem;">
                    <i class="fas fa-eye"></i> Previewed: <?= date('F d, Y h:i A') ?>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Disable keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey && (e.key === 's' || e.key === 'p' || e.key === 'u')) || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                e.key === 'F12' ||
                e.key === 'PrintScreen') {
                e.preventDefault();
                return false;
            }
        });

        // Disable right click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Disable drag and drop
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });

        // Disable print
        window.addEventListener('beforeprint', function(e) {
            e.preventDefault();
            alert('Printing is disabled for security.');
            return false;
        });

        window.print = function() {
            alert('Printing is disabled for security.');
            return false;
        };

        <?php if ($is_image): ?>
        document.querySelectorAll('.secure-image').forEach(img => {
            img.addEventListener('contextmenu', e => e.preventDefault());
            img.addEventListener('dragstart', e => e.preventDefault());
        });
        <?php endif; ?>
    </script>
</body>
</html>