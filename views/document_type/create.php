<div class="mb-4">
    <h2>Add Document Type</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?controller=documenttype&action=store">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type_name" class="form-label">Document Type Name *</label>
                    <input type="text" class="form-control" id="type_name" name="type_name" 
                           placeholder="e.g., Form 137, Birth Certificate, etc." required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                              placeholder="Optional description of this document type"></textarea>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Create Document Type</button>
                    <a href="index.php?controller=documenttype&action=index" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>