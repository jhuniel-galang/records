<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>School Management</h2>
    <a href="index.php?controller=school&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New School
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stats-number"><?php echo $total_schools; ?></div>
            <div class="stats-label">Total Schools</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <div class="stats-number"><?php echo $elementary_count; ?></div>
            <div class="stats-label">Elementary Schools</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
            <div class="stats-number"><?php echo $hs_count; ?></div>
            <div class="stats-label">High Schools</div>
        </div>
    </div>
</div>

<!-- Schools Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">School List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>School Name</th>
                        <th>Level</th>
                        <th>Address</th>
                        <th>Principal</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($schools) > 0): ?>
                        <?php foreach($schools as $school): ?>
                        <tr>
                            <td><?php echo $school['id']; ?></td>
                            <td><?php echo htmlspecialchars($school['school_name']); ?></td>
                            <td>
                                <span class="badge <?php echo $school['level'] == 'Elementary' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo $school['level']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($school['address']); ?></td>
                            <td><?php echo htmlspecialchars($school['principal_name']); ?></td>
                            <td><?php echo htmlspecialchars($school['creator_name'] ?? 'System'); ?></td>
                            <td>
                                <?php if($school['status'] == 1): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?controller=school&action=view&id=<?php echo $school['id']; ?>" 
                                   class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="index.php?controller=school&action=edit&id=<?php echo $school['id']; ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if($school['status'] == 1): ?>
                                    <a href="index.php?controller=school&action=delete&id=<?php echo $school['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to deactivate this school?')"
                                       title="Deactivate">
                                        <i class="bi bi-building-x"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?controller=school&action=activate&id=<?php echo $school['id']; ?>" 
                                       class="btn btn-sm btn-success" 
                                       onclick="return confirm('Are you sure you want to activate this school?')"
                                       title="Activate">
                                        <i class="bi bi-building-check"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No schools found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>