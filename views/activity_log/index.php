<?php
// Debug - show what's in the logs array
echo "<!-- DEBUG START -->\n";
echo "<!-- logs exists: " . (isset($logs) ? 'YES' : 'NO') . " -->\n";
echo "<!-- logs type: " . (isset($logs) ? gettype($logs) : 'N/A') . " -->\n";
echo "<!-- logs count: " . (isset($logs) ? count($logs) : 'N/A') . " -->\n";
echo "<!-- current_page: " . (isset($current_page) ? $current_page : 'NOT SET') . " -->\n";
echo "<!-- total_logs: " . (isset($total_logs) ? $total_logs : 'NOT SET') . " -->\n";

if (isset($logs) && is_array($logs) && count($logs) > 0) {
    echo "<!-- First log structure: " . print_r($logs[0], true) . " -->\n";
} else {
    echo "<!-- Logs array is empty or not set -->\n";
    
    // Show all variables in scope for debugging
    echo "<!-- All variables in scope: " . print_r(array_keys(get_defined_vars()), true) . " -->\n";
}
echo "<!-- DEBUG END -->\n";
?>






<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Activity Logs</h2>

</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter Logs</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="controller" value="activitylog">
            <input type="hidden" name="action" value="index">
            
            <div class="col-md-3">
                <label for="user_id" class="form-label">User</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">All Users</option>
                    <?php foreach($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>" 
                            <?php echo ($filters['user_id'] ?? '') == $user['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="action" class="form-label">Action</label>
                <select class="form-select" id="action" name="action">
                    <option value="">All Actions</option>
                    <?php foreach($actions as $action): ?>
                        <option value="<?php echo htmlspecialchars($action); ?>" 
                            <?php echo ($filters['action'] ?? '') == $action ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($action); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" class="form-control" id="date_from" name="date_from" 
                       value="<?php echo $filters['date_from'] ?? ''; ?>">
            </div>
            
            <div class="col-md-2">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" class="form-control" id="date_to" name="date_to" 
                       value="<?php echo $filters['date_to'] ?? ''; ?>">
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All</option>
                    <option value="success" <?php echo ($filters['status'] ?? '') == 'success' ? 'selected' : ''; ?>>Success</option>
                    <option value="failed" <?php echo ($filters['status'] ?? '') == 'failed' ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="index.php?controller=activitylog&action=index" class="btn btn-secondary">Clear Filters</a>
            </div>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Log Entries (Total: <?php echo number_format($total_logs); ?>)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Controller/Method</th>
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
                                <?php if($log['username']): ?>
                                    <a href="index.php?controller=activitylog&action=userActivity&user_id=<?php echo $log['user_id']; ?>">
                                        <?php echo htmlspecialchars($log['username']); ?>
                                    </a>
                                <?php else: ?>
                                    <em>System</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($log['action']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars(substr($log['description'], 0, 100)) . (strlen($log['description']) > 100 ? '...' : ''); ?></td>
                            <td><?php echo $log['ip_address']; ?></td>
                            <td>
                                <small><?php echo $log['controller']; ?>/<?php echo $log['method']; ?></small>
                            </td>
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
                            <td colspan="9" class="text-center">No activity logs found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=activitylog&action=index&page=<?php echo $current_page-1; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">Previous</a>
                </li>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if($i >= $current_page - 2 && $i <= $current_page + 2): ?>
                        <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                            <a class="page-link" href="?controller=activitylog&action=index&page=<?php echo $i; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=activitylog&action=index&page=<?php echo $current_page+1; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">Next</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>