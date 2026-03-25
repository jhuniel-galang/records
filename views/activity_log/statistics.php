<div class="mb-4">
    <h2>Activity Statistics</h2>
</div>

<!-- Time Range Selector -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3 align-items-center">
            <input type="hidden" name="controller" value="activitylog">
            <input type="hidden" name="action" value="statistics">
            
            <div class="col-auto">
                <label for="days" class="col-form-label">Show statistics for last:</label>
            </div>
            <div class="col-auto">
                <select class="form-select" id="days" name="days" onchange="this.form.submit()">
                    <option value="7" <?php echo $days == 7 ? 'selected' : ''; ?>>7 days</option>
                    <option value="14" <?php echo $days == 14 ? 'selected' : ''; ?>>14 days</option>
                    <option value="30" <?php echo $days == 30 ? 'selected' : ''; ?>>30 days</option>
                    <option value="90" <?php echo $days == 90 ? 'selected' : ''; ?>>90 days</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <!-- Total Logs Card -->
    <div class="col-md-3 mb-4">
        <div class="stats-card">
            <div class="stats-number"><?php echo number_format($stats['total']); ?></div>
            <div class="stats-label">Total Activities</div>
        </div>
    </div>
    
    <!-- Success Rate Card -->
    <div class="col-md-3 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <?php 
            $success_count = 0;
            foreach($stats['by_status'] as $status) {
                if($status['status'] == 'success') $success_count = $status['count'];
            }
            $success_rate = $stats['total'] > 0 ? round(($success_count / $stats['total']) * 100, 1) : 0;
            ?>
            <div class="stats-number"><?php echo $success_rate; ?>%</div>
            <div class="stats-label">Success Rate</div>
        </div>
    </div>
    
    <!-- Unique Actions Card -->
    <div class="col-md-3 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
            <div class="stats-number"><?php echo count($stats['by_action']); ?></div>
            <div class="stats-label">Unique Actions</div>
        </div>
    </div>
    
    <!-- Active Users Card -->
    <div class="col-md-3 mb-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8 0%, #6c757d 100%);">
            <div class="stats-number"><?php echo count($stats['by_user']); ?></div>
            <div class="stats-label">Active Users</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Activities by Day -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Activities by Day (Last <?php echo $days; ?> Days)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($stats['by_day'] as $day): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($day['date'])); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $day['count']; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Actions -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Most Frequent Actions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($stats['by_action'] as $action): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($action['action']); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo $action['count']; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Most Active Users -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Most Active Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Activities</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($stats['by_user'] as $user): ?>
                            <tr>
                                <td>
                                    <a href="index.php?controller=activitylog&action=userActivity&user_id=<?php 
                                        // We need to get user_id - this is a limitation, we might need to join tables
                                        echo '1'; // Placeholder
                                    ?>">
                                        <?php echo htmlspecialchars($user['username'] ?: 'System'); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?php echo $user['count']; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Status Distribution -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5>Status Distribution</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($stats['by_status'] as $status): ?>
                            <tr>
                                <td>
                                    <?php if($status['status'] == 'success'): ?>
                                        <span class="badge bg-success">Success</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Failed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $status['count']; ?></td>
                                <td>
                                    <?php 
                                    $percentage = $stats['total'] > 0 ? round(($status['count'] / $stats['total']) * 100, 1) : 0;
                                    echo $percentage . '%';
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="index.php?controller=activitylog&action=index" class="btn btn-secondary">Back to Logs</a>
</div>