<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure PDF Viewer - <?php echo htmlspecialchars($document->file_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #1a1a1a;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            overflow: hidden;
        }

        .viewer-header {
            background: #2c3e50;
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #e74c3c;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 60px;
        }

        .viewer-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 400;
        }

        .viewer-header h5 i {
            color: #e74c3c;
            margin-right: 10px;
        }

        .document-info {
            display: flex;
            gap: 20px;
            font-size: 0.9rem;
        }

        .document-info span {
            color: #ecf0f1;
        }

        .document-info .label {
            color: #95a5a6;
            margin-right: 5px;
        }

        .btn-danger-custom {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .btn-danger-custom:hover {
            background: #c0392b;
            color: white;
        }

        #viewerContainer {
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: auto;
            background: #525659;
        }

        #viewer {
            margin: 30px auto;
            width: fit-content;
        }

        .page {
            margin: 20px auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            background: white;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .page canvas {
            display: block;
            margin: 0 auto;
            pointer-events: none; /* Disable interactions with canvas */
            user-select: none;
            -webkit-user-drag: none;
        }

        /* Watermark on each page */
        .page::after {
            content: "CONFIDENTIAL";
            position: absolute;
            bottom: 20%;
            right: 10%;
            font-size: 60px;
            font-weight: bold;
            color: rgba(231, 76, 60, 0.1);
            transform: rotate(-15deg);
            pointer-events: none;
            z-index: 10;
            white-space: nowrap;
        }

        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
        }

        .spinner {
            border: 4px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top: 4px solid #e74c3c;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .page-count {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            z-index: 1001;
            pointer-events: none;
        }

        /* Navigation controls - minimal, only for page navigation */
        .nav-controls {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.7);
            padding: 8px 15px;
            border-radius: 30px;
            z-index: 1001;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .nav-controls button {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            padding: 5px 10px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .nav-controls button:hover {
            color: #e74c3c;
        }

        .nav-controls button:disabled {
            color: #7f8c8d;
            cursor: not-allowed;
        }

        .nav-controls span {
            color: white;
            font-size: 0.9rem;
        }

        /* Zoom controls */
        .zoom-controls {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0,0,0,0.7);
            padding: 8px 15px;
            border-radius: 30px;
            z-index: 1001;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .zoom-controls button {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            padding: 5px 10px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .zoom-controls button:hover {
            color: #e74c3c;
        }

        .zoom-controls span {
            color: white;
            font-size: 0.9rem;
            min-width: 60px;
            text-align: center;
        }

        /* Disable text selection everywhere */
        * {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body oncontextmenu="return false;" onkeydown="return disableShortcuts(event)">
    <div class="viewer-header">
        <h5>
            <i class="bi bi-file-lock"></i>
            CONFIDENTIAL DOCUMENT VIEWER - NO DOWNLOAD ALLOWED
        </h5>
        <div class="document-info">
            <span><span class="label">File:</span> <?php echo htmlspecialchars($document->file_name); ?></span>
            <span><span class="label">Type:</span> <?php echo htmlspecialchars($document->document_type); ?></span>
            <span><span class="label">School:</span> <?php echo htmlspecialchars($document->school_name); ?></span>
        </div>
        <a href="index.php?controller=document&action=view&id=<?php echo $document->id; ?>" class="btn-danger-custom">
            <i class="bi bi-arrow-left"></i> Exit Viewer
        </a>
    </div>

    <div id="viewerContainer">
        <div id="viewer"></div>
    </div>

    <!-- Minimal navigation controls -->
    <div class="nav-controls">
        <button id="prev" disabled><i class="bi bi-chevron-left"></i></button>
        <span id="page_num">1</span> / <span id="page_count">0</span>
        <button id="next" disabled><i class="bi bi-chevron-right"></i></button>
    </div>

    <!-- Zoom controls -->
    <div class="zoom-controls">
        <button id="zoomOut"><i class="bi bi-dash-circle"></i></button>
        <span id="zoomLevel">100%</span>
        <button id="zoomIn"><i class="bi bi-plus-circle"></i></button>
    </div>

    <!-- Loading indicator -->
    <div id="loading" class="loading">
        <div class="spinner"></div>
        <div>Loading document...</div>
    </div>

    <script src="assets/pdfjs/build/pdf.js"></script>
    <script>
        // Disable keyboard shortcuts
        function disableShortcuts(e) {
            // Disable Ctrl+S, Ctrl+P, Ctrl+Shift+I, Ctrl+U, F12, etc.
            if ((e.ctrlKey && (e.key === 's' || e.key === 'p' || e.key === 'u' || e.key === 'S')) || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') || 
                e.key === 'F12' || e.key === 'PrintScreen' ||
                (e.ctrlKey && e.key === 'PrintScreen')) {
                e.preventDefault();
                return false;
            }
        }

        // Disable print
        window.addEventListener('beforeprint', function(e) {
            e.preventDefault();
            alert('Printing is disabled for confidential documents');
            return false;
        });

        // Override print function
        window.print = function() {
            alert('Printing is disabled for confidential documents');
            return false;
        };

        // PDF.js configuration
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'assets/pdfjs/build/pdf.worker.js';

        const url = 'index.php?controller=document&action=preview&id=<?php echo $document->id; ?>&t=' + new Date().getTime();
        
        let pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = 1.0,
            canvas = document.createElement('canvas'),
            ctx = canvas.getContext('2d');

        const viewer = document.getElementById('viewer');
        const loading = document.getElementById('loading');
        const prevBtn = document.getElementById('prev');
        const nextBtn = document.getElementById('next');
        const zoomInBtn = document.getElementById('zoomIn');
        const zoomOutBtn = document.getElementById('zoomOut');
        const zoomLevelSpan = document.getElementById('zoomLevel');
        const pageNumSpan = document.getElementById('page_num');
        const pageCountSpan = document.getElementById('page_count');

        /**
         * Render the page
         */
        function renderPage(num) {
            pageRendering = true;
            
            // Update page number display
            pageNumSpan.textContent = num;

            // Hide loading indicator
            loading.style.display = 'block';

            // Using promise to fetch the page
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Render PDF page into canvas context
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                const renderTask = page.render(renderContext);
                
                renderTask.promise.then(function() {
                    pageRendering = false;
                    
                    // Create a new div for this page
                    const pageDiv = document.createElement('div');
                    pageDiv.className = 'page';
                    pageDiv.id = 'page-' + num;
                    
                    // Clone the canvas to avoid reference issues
                    const newCanvas = canvas.cloneNode(true);
                    pageDiv.appendChild(newCanvas);
                    
                    // Clear viewer and add the page
                    viewer.innerHTML = '';
                    viewer.appendChild(pageDiv);
                    
                    // Update navigation buttons
                    updateButtons();
                    
                    // Hide loading indicator
                    loading.style.display = 'none';

                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });
        }

        /**
         * Queue rendering of a page
         */
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        /**
         * Go to previous page
         */
        function onPrevPage() {
            if (pageNum <= 1) {
                return;
            }
            pageNum--;
            queueRenderPage(pageNum);
        }

        /**
         * Go to next page
         */
        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) {
                return;
            }
            pageNum++;
            queueRenderPage(pageNum);
        }

        /**
         * Zoom in
         */
        function zoomIn() {
            scale += 0.25;
            zoomLevelSpan.textContent = Math.round(scale * 100) + '%';
            queueRenderPage(pageNum);
        }

        /**
         * Zoom out
         */
        function zoomOut() {
            if (scale > 0.5) {
                scale -= 0.25;
                zoomLevelSpan.textContent = Math.round(scale * 100) + '%';
                queueRenderPage(pageNum);
            }
        }

        /**
         * Update navigation buttons state
         */
        function updateButtons() {
            prevBtn.disabled = (pageNum <= 1);
            nextBtn.disabled = (pageNum >= pdfDoc.numPages);
            pageCountSpan.textContent = pdfDoc.numPages;
        }

        /**
         * Load PDF document
         */
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            pageCountSpan.textContent = pdfDoc.numPages;
            
            // Initial render
            renderPage(pageNum);
        }).catch(function(error) {
            console.error('Error loading PDF: ' + error);
            loading.innerHTML = '<div style="color: #e74c3c;">Error loading PDF document</div>';
        });

        // Event listeners
        prevBtn.addEventListener('click', onPrevPage);
        nextBtn.addEventListener('click', onNextPage);
        zoomInBtn.addEventListener('click', zoomIn);
        zoomOutBtn.addEventListener('click', zoomOut);

        // Disable right click on canvas
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

        // Disable drag and drop
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });

        // Disable selection
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
            return false;
        });

        // Prevent image saving via drag
        document.addEventListener('dragover', function(e) {
            e.preventDefault();
            return false;
        });

        document.addEventListener('drop', function(e) {
            e.preventDefault();
            return false;
        });

        // Additional protection
        window.addEventListener('keydown', function(e) {
            // Disable Ctrl+P (Print)
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                e.stopPropagation();
                alert('Printing is disabled for confidential documents');
                return false;
            }
            
            // Disable Ctrl+S (Save)
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                e.stopPropagation();
                alert('Saving is disabled for confidential documents');
                return false;
            }
            
            // Disable F12, Ctrl+Shift+I, etc.
            if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                e.preventDefault();
                return false;
            }
        });

        // Prevent leaving page accidentally
        window.addEventListener('beforeunload', function(e) {
            // No confirmation needed, but keeps the security
            return undefined;
        });
    </script>
</body>
</html>