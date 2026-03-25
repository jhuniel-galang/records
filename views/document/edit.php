<div class="mb-4">
    <h2>Edit Document</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?controller=document&action=update">
            <input type="hidden" name="id" value="<?php echo $document->id; ?>">
            
            <div class="row">
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
                            <option value="<?= htmlspecialchars($type['type_name']) ?>" 
                                <?= $document->document_type == $type['type_name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['type_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks" rows="3"><?php echo htmlspecialchars($document->remarks); ?></textarea>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Document</button>
                    <a href="index.php?controller=document&action=view&id=<?php echo $document->id; ?>" class="btn btn-info">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>