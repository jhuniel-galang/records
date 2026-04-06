<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Document Types Management</h2>
    <a href="index.php?controller=documenttype&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Document Type
    </a>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stats-number"><?= $total_types ?></div>
            <div class="stats-label">Total Document Types</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <div class="stats-number"><?= $active_count ?></div>
            <div class="stats-label">Active Types</div>
        </div>
    </div>
</div>

<!-- Document Types Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Document Types List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type Name</th>
                        <th>Description</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($types) > 0): ?>
                        <?php foreach($types as $type): ?>
                        <tr>
                            <td><?= $type['id'] ?></td>
                            <td><strong><?= htmlspecialchars($type['type_name']) ?></strong></td>
                            <td><?= htmlspecialchars($type['description'] ?: 'No description') ?></td>
                            <td><?= htmlspecialchars($type['created_by_name'] ?? 'System') ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($type['created_at'])) ?></td>
                            <td>
                                <?php if($type['status'] == 1): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?controller=documenttype&action=edit&id=<?= $type['id'] ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if($type['status'] == 1): ?>
                                    <a href="index.php?controller=documenttype&action=delete&id=<?= $type['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to deactivate this document type?')"
                                       title="Deactivate">
                                        <i class="bi bi-slash-circle"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?controller=documenttype&action=activate&id=<?= $type['id'] ?>" 
                                       class="btn btn-sm btn-success" 
                                       onclick="return confirm('Activate this document type?')"
                                       title="Activate">
                                        <i class="bi bi-check-circle"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No document types found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>