<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Document Management</h2>
    <a href="index.php?controller=document&action=upload" class="btn btn-primary">
        <i class="bi bi-cloud-upload"></i> Upload Document
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stats-number"><?php echo $total_documents; ?></div>
            <div class="stats-label">Total Documents</div>
        </div>
    </div>
    <?php foreach($stats as $stat): ?>
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <div class="stats-number"><?php echo $stat['total']; ?></div>
            <div class="stats-label"><?php echo htmlspecialchars($stat['document_type']); ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Documents Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Document List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>File Name</th>
                        <th>School</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Uploaded By</th>
                        <th>Upload Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($documents) > 0): ?>
                        <?php foreach($documents as $doc): ?>
                        <tr>
                            <td><?php echo $doc['id']; ?></td>
                            <td>
                                <i class="bi bi-file-<?php 
                                    echo $doc['file_type'] == 'pdf' ? 'pdf' : 
                                        (in_array($doc['file_type'], ['jpg', 'jpeg', 'png', 'gif']) ? 'image' : 'text'); 
                                ?>"></i>
                                <?php echo htmlspecialchars($doc['file_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($doc['school_name']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($doc['document_type']); ?></span>
                            </td>
                            <td><?php echo round($doc['file_size'] / 1024, 2); ?> KB</td>
                            <td><?php echo htmlspecialchars($doc['uploader_name'] ?? 'Unknown'); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($doc['uploader_at'])); ?></td>
                            <td>
                                <a href="index.php?controller=document&action=view&id=<?php echo $doc['id']; ?>" 
                                   class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="index.php?controller=document&action=edit&id=<?php echo $doc['id']; ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if($doc['status'] == 1): ?>
                                    <a href="index.php?controller=document&action=delete&id=<?php echo $doc['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this document?')"
                                       title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?controller=document&action=restore&id=<?php echo $doc['id']; ?>" 
                                       class="btn btn-sm btn-secondary" 
                                       onclick="return confirm('Restore this document?')"
                                       title="Restore">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No documents found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>