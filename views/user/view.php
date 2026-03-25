<div class="mb-4">
    <h2>User Details</h2>
</div>

<div class="card">
    <div class="card-header">
        <h5><?php echo htmlspecialchars($user->name); ?></h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width: 200px;">ID</th>
                <td><?php echo $user->id; ?></td>
            </tr>
            <tr>
                <th>Full Name</th>
                <td><?php echo htmlspecialchars($user->name); ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?php echo htmlspecialchars($user->username); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($user->email); ?></td>
            </tr>
            <tr>
                <th>Role</th>
                <td>
                    <span class="badge <?php echo $user->role == 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                        <?php echo ucfirst($user->role); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php if($user->status == 1): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Created At</th>
                <td><?php echo date('F d, Y H:i:s', strtotime($user->created_at)); ?></td>
            </tr>
        </table>
        
        <div class="mt-3">
            <a href="index.php?controller=user&action=edit&id=<?php echo $user->id; ?>" 
               class="btn btn-warning">Edit</a>
            <a href="index.php?controller=user&action=index" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>