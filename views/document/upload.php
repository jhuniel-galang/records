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
                
                <div class="col-md-6 mb-3">
                    <label for="school_name" class="form-label">School *</label>
                    <select class="form-control" id="school_name" name="school_name" required>
                        <option value="">Select School</option>
                        <?php foreach($schools as $school): ?>
                            <option value="<?php echo htmlspecialchars($school['school_name']); ?>">
                                <?php echo htmlspecialchars($school['school_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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