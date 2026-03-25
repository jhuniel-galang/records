<div class="mb-4">
    <h2>User Activity: <?php echo htmlspecialchars($user->username); ?></h2>
    <p class="text-muted">Showing last 100 activities</p>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>User Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Name:</strong> <?php echo htmlspecialchars($user->name); ?>
            </div>
            <div class="col-md-3">
                <strong>Username:</strong> <?php echo htmlspecialchars($user->username); ?>
            </div>
            <div class="col-md-3">
                <strong>Email:</strong> <?php echo htmlspecialchars($user->email); ?>
            </div>
            <div class="col-md-3">
                <strong>Role:</strong> 
                <span class="badge <?php echo $user->role == 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                    <?php echo ucfirst($user->role); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Activity History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($logs) > 0): ?>
                        <?php foreach($logs as $log): ?>
                        <tr>
                            <td><?php echo $log['id']; ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($log['action']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars(substr($log['description'], 0, 100)) . (strlen($log['description']) > 100 ? '...' : ''); ?></td>
                            <td><?php echo $log['ip_address']; ?></td>
                            <td>
                                <?php if($log['status'] == 'success'): ?>
                                    <span class="badge bg-success">Success</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Failed</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                            <td>
                                <a href="index.php?controller=activitylog&action=view&id=<?php echo $log['id']; ?>" 
                                   class="btn btn-sm btn-info" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No activity logs found for this user</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <a href="index.php?controller=activitylog&action=index" class="btn btn-secondary">Back to All Logs</a>
        </div>
    </div>
</div>