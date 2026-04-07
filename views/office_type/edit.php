<div class="mb-4">
    <h2>Edit Office Type</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?controller=officetype&action=update">
            <input type="hidden" name="id" value="<?= $type['id'] ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type_name" class="form-label">Office Type Name *</label>
                    <input type="text" class="form-control" id="type_name" name="type_name" 
                           value="<?= htmlspecialchars($type['type_name']) ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="1" <?= $type['status'] == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $type['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($type['description']) ?></textarea>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update Office Type</button>
                    <a href="index.php?controller=officetype&action=index" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>