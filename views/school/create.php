<div class="mb-4">
    <h2>Add New School</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?controller=school&action=store">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="school_name" class="form-label">School Name *</label>
                    <input type="text" class="form-control" id="school_name" name="school_name" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="level" class="form-label">Level *</label>
                    <select class="form-control" id="level" name="level" required>
                        <option value="">Select Level</option>
                        <option value="Elementary">Elementary</option>
                        <option value="HS">High School (HS)</option>
                    </select>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="address" class="form-label">Address *</label>
                    <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="principal_name" class="form-label">Principal Name *</label>
                    <input type="text" class="form-control" id="principal_name" name="principal_name" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Create School</button>
                    <a href="index.php?controller=school&action=index" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>