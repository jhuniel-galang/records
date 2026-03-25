<div class="mb-4">
    <h2>Edit School</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?controller=school&action=update">
            <input type="hidden" name="id" value="<?php echo $school->id; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="school_name" class="form-label">School Name *</label>
                    <input type="text" class="form-control" id="school_name" name="school_name" 
                           value="<?php echo htmlspecialchars($school->school_name); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="level" class="form-label">Level *</label>
                    <select class="form-control" id="level" name="level" required>
                        <option value="Elementary" <?php echo $school->level == 'Elementary' ? 'selected' : ''; ?>>Elementary</option>
                        <option value="HS" <?php echo $school->level == 'HS' ? 'selected' : ''; ?>>High School (HS)</option>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="address" class="form-label">Address *</label>
                    <textarea class="form-control" id="address" name="address" rows="2" required><?php echo htmlspecialchars($school->address); ?></textarea>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="principal_name" class="form-label">Principal Name *</label>
                    <input type="text" class="form-control" id="principal_name" name="principal_name" 
                           value="<?php echo htmlspecialchars($school->principal_name); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="1" <?php echo $school->status == 1 ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo $school->status == 0 ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update School</button>
                    <a href="index.php?controller=school&action=index" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>