<div class="mb-4">
    <h2>Upload Document</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?controller=document&action=store" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="document" class="form-label">Select Document *</label>
                    <input type="file" class="form-control" id="document" name="document" required>
                    <small class="text-muted">Allowed types: PDF, Images (JPG, PNG, GIF), DOC, DOCX, XLS, XLSX. Max size: 10MB</small>
                </div>
                
                <!-- Duplicate warning -->
                <div id="duplicate-warning" class="alert alert-warning" style="display: none;">
                    <i class="bi bi-exclamation-triangle"></i> 
                    <span id="duplicate-message"></span>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="doc_title" class="form-label">Document Title *</label>
                    <input type="text" class="form-control" id="doc_title" name="doc_title" 
                           placeholder="Enter document title" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="doc_year" class="form-label">Document Year</label>
                    <select class="form-control" id="doc_year" name="doc_year">
                        <option value="">Select Year</option>
                        <?php for($y = date('Y'); $y >= 2000; $y--): ?>
                            <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <!-- School Selection -->
                <div class="col-md-6 mb-3">
                    <label for="school_name" class="form-label">School/Office *</label>
                    <select class="form-control" id="school_name" name="school_name" required>
                        <option value="">Select School/Office</option>
                        <?php foreach($schools as $school): ?>
                            <option value="<?php echo htmlspecialchars($school['school_name']); ?>"
                                    data-office-type="<?php echo htmlspecialchars($school['office_type_name'] ?? ''); ?>">
                                [<?php echo htmlspecialchars($school['office_type_name'] ?? 'N/A'); ?>] 
                                <?php echo htmlspecialchars($school['school_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted">Select the school or office this document belongs to</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="document_type" class="form-label">Document Type *</label>
                    <select class="form-control" id="document_type" name="document_type" required>
                        <option value="">Select Type</option>
                        <?php foreach($documentTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type['type_name']) ?>">
                                <?= htmlspecialchars($type['type_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                    <a href="index.php?controller=document&action=index" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Real-time duplicate title checking
    let checkTimeout;
    
    document.getElementById('doc_title').addEventListener('input', function() {
        clearTimeout(checkTimeout);
        const doc_title = this.value;
        const school_name = document.getElementById('school_name').value;
        
        if (doc_title.length > 2 && school_name) {
            checkTimeout = setTimeout(function() {
                fetch('index.php?controller=document&action=checkDuplicate&doc_title=' + encodeURIComponent(doc_title) + '&school_name=' + encodeURIComponent(school_name))
                    .then(response => response.json())
                    .then(data => {
                        if (data.duplicate) {
                            let errorDiv = document.getElementById('title-error');
                            if (!errorDiv) {
                                const div = document.createElement('div');
                                div.id = 'title-error';
                                div.className = 'text-danger mt-1';
                                div.innerHTML = '<small><i class="bi bi-exclamation-triangle"></i> A document with this title already exists for this school!</small>';
                                document.getElementById('doc_title').parentNode.appendChild(div);
                            }
                        } else {
                            const errorDiv = document.getElementById('title-error');
                            if (errorDiv) errorDiv.remove();
                        }
                    });
            }, 500);
        }
    });
</script>