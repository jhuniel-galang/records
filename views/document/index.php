<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Document Management</h2>
    <div>
        <a href="index.php?controller=document&action=archive" class="btn btn-secondary me-2">
            <i class="bi bi-archive"></i> Archive
        </a>
        <a href="index.php?controller=document&action=upload" class="btn btn-primary">
            <i class="bi bi-cloud-upload"></i> Upload Document
        </a>
    </div>
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
                <!-- In the table header, add new columns -->
<thead>
    <tr>
        <th>ID</th>
        <th>Document Title</th>
        <th>Year</th>
        <th>School</th>
        <th>Type</th>
        <th>Upload Date</th>
        <th>Actions</th>
    </tr>
</thead>

<!-- In the table body, add the new fields -->
<tbody>
    <?php if(count($documents) > 0): ?>
        <?php foreach($documents as $doc): ?>
        <tr>
            <td><?php echo $doc['id']; ?></td>
            <td><strong><?php echo htmlspecialchars($doc['doc_title'] ?: 'Untitled'); ?></strong></td>
            <td><?php echo htmlspecialchars($doc['doc_year'] ?: 'N/A'); ?></td>
            <td><?php echo htmlspecialchars($doc['school_name']); ?></td>
            <td><span class="badge bg-info"><?php echo htmlspecialchars($doc['document_type']); ?></span></td>


            <td><?php echo date('Y-m-d H:i', strtotime($doc['uploader_at'])); ?></td>
            <td>
                <a href="index.php?controller=document&action=view&id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-info">View</a>
                <a href="index.php?controller=document&action=edit&id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="index.php?controller=document&action=delete&id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Move this document to archive?')">Archive</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
</div>