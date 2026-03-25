<div class="mb-4">
    <h2>Edit User</h2>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="index.php?controller=user&action=update">
            <input type="hidden" name="id" value="<?php echo $user->id; ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?php echo htmlspecialchars($user->name); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?php echo htmlspecialchars($user->username); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user->email); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role *</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="uploader" <?php echo $user->role == 'uploader' ? 'selected' : ''; ?>>Uploader</option>
                        <option value="admin" <?php echo $user->role == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="1" <?php echo $user->status == 1 ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo $user->status == 0 ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="index.php?controller=user&action=index" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>