<div class="mb-4">
    <h2>Edit Document</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?controller=document&action=update">
            <input type="hidden" name="id" value="<?php echo $document->id; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="doc_title" class="form-label">Document Title *</label>
                    <input type="text" class="form-control" id="doc_title" name="doc_title" 
                           value="<?php echo htmlspecialchars($document->doc_title); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="doc_year" class="form-label">Document Year</label>
                    <select class="form-control" id="doc_year" name="doc_year">
                        <option value="">Select Year</option>
                        <?php for($y = date('Y'); $y >= 2000; $y--): ?>
                            <option value="<?php echo $y; ?>" <?php echo $document->doc_year == $y ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="school_name" class="form-label">School *</label>
                    <select class="form-control" id="school_name" name="school_name" required>
                        <option value="">Select School</option>
                        <?php foreach($schools as $school): ?>
                            <option value="<?php echo htmlspecialchars($school['school_name']); ?>"
                                <?php echo $document->school_name == $school['school_name'] ? 'selected' : ''; ?>>
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
                            <option value="<?php echo htmlspecialchars($type['type_name']); ?>"
                                <?php echo $document->document_type == $type['type_name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="file_name" class="form-label">File Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($document->file_name); ?>" disabled>
                    <small class="text-muted">File name cannot be changed</small>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo htmlspecialchars($document->remarks); ?></textarea>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Document</button>
                    <a href="index.php?controller=document&action=index" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>