<div class="mb-4">
    <h2>School Details</h2>
</div>

<div class="card">
    <div class="card-header">
        <h5><?php echo htmlspecialchars($school->school_name); ?></h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width: 200px;">ID</th>
                <td><?php echo $school->id; ?></td>
            </tr>
            <tr>
                <th>School Name</th>
                <td><?php echo htmlspecialchars($school->school_name); ?></td>
            </tr>
            <tr>
                <th>Level</th>
                <td>
                    <span class="badge <?php echo $school->level == 'Elementary' ? 'bg-success' : 'bg-warning'; ?>">
                        <?php echo $school->level; ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo nl2br(htmlspecialchars($school->address)); ?></td>
            </tr>
            <tr>
                <th>Principal Name</th>
                <td><?php echo htmlspecialchars($school->principal_name); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php if($school->status == 1): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Created By</th>
                <td><?php echo htmlspecialchars($school->creator_name ?? 'System'); ?> (ID: <?php echo $school->created_by; ?>)</td>
            </tr>
            <tr>
                <th>Created At</th>
                <td><?php echo date('F d, Y H:i:s', strtotime($school->created_at)); ?></td>
            </tr>
            <tr>
                <th>Last Updated</th>
                <td><?php echo $school->updated_at ? date('F d, Y H:i:s', strtotime($school->updated_at)) : 'Never'; ?></td>
            </tr>
        </table>
        
        <div class="mt-3">
            <a href="index.php?controller=school&action=edit&id=<?php echo $school->id; ?>" 
               class="btn btn-warning">Edit</a>
            <a href="index.php?controller=school&action=index" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>