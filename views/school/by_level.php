<div class="mb-4">
    <h2><?php echo $level; ?> Schools</h2>
    <a href="index.php?controller=school&action=index" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to All Schools
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><?php echo $level; ?> School List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>School Name</th>
                        <th>Address</th>
                        <th>Principal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($schools) > 0): ?>
                        <?php foreach($schools as $school): ?>
                        <tr>
                            <td><?php echo $school['id']; ?></td>
                            <td><?php echo htmlspecialchars($school['school_name']); ?></td>
                            <td><?php echo htmlspecialchars($school['address']); ?></td>
                            <td><?php echo htmlspecialchars($school['principal_name']); ?></td>
                            <td>
                                <a href="index.php?controller=school&action=view&id=<?php echo $school['id']; ?>" 
                                   class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="index.php?controller=school&action=edit&id=<?php echo $school['id']; ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No <?php echo $level; ?> schools found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>