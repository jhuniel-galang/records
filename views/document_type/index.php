<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Document Types Management</h2>
    <a href="index.php?controller=documenttype&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Document Type
    </a>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stats-number"><?= $total_types ?></div>
            <div class="stats-label">Total Document Types</div>
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
                <a href="index.php?controller=documenttype&action=edit&id=<?= $type['id'] ?>" 
                   class="btn btn-sm btn-warning" title="Edit">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="index.php?controller=documenttype&action=delete&id=<?= $type['id'] ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('WARNING: This will permanently delete this document type and cannot be undone! Continue?')"
                   title="Delete">
                    <i class="bi bi-trash"></i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" class="text-center">No document types found</td>
        </tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
</div>