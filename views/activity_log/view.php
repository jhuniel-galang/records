<div class="mb-4">
    <h2>Activity Log Details</h2>
</div>

<div class="card">
    <div class="card-header">
        <h5>Log Entry #<?php echo $log['id']; ?></h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width: 200px;">ID</th>
                <td><?php echo $log['id']; ?></td>
            </tr>
            <tr>
                <th>User</th>
                <td>
                    <?php if($log['username']): ?>
                        <a href="index.php?controller=activitylog&action=userActivity&user_id=<?php echo $log['user_id']; ?>">
                            <?php echo htmlspecialchars($log['username']); ?> (ID: <?php echo $log['user_id']; ?>)
                        </a>
                    <?php else: ?>
                        <em>System</em>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Action</th>
                <td><span class="badge bg-info"><?php echo htmlspecialchars($log['action']); ?></span></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?php echo nl2br(htmlspecialchars($log['description'])); ?></td>
            </tr>
            <tr>
                <th>Controller</th>
                <td><?php echo $log['controller']; ?></td>
            </tr>
            <tr>
                <th>Method</th>
                <td><?php echo $log['method']; ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php if($log['status'] == 'success'): ?>
                        <span class="badge bg-success">Success</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Failed</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Created At</th>
                <td><?php echo date('F d, Y H:i:s', strtotime($log['created_at'])); ?></td>
            </tr>
        </table>
        
        <?php if($log['old_data']): ?>
        <div class="mt-4">
            <h5>Old Data</h5>
            <pre class="bg-light p-3 rounded"><?php print_r(json_decode($log['old_data'], true)); ?></pre>
        </div>
        <?php endif; ?>
        
        <?php if($log['new_data']): ?>
        <div class="mt-4">
            <h5>New Data</h5>
            <pre class="bg-light p-3 rounded"><?php print_r(json_decode($log['new_data'], true)); ?></pre>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="index.php?controller=activitylog&action=index" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>