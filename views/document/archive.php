<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Document Archive</h2>
    <div>
        <a href="index.php?controller=document&action=index" class="btn btn-primary">
            <i class="bi bi-folder"></i> Active Documents
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter Archive</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="controller" value="document">
            <input type="hidden" name="action" value="archive">
            
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="File name or school" 
                       value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
            </div>
            
            <div class="col-md-4">
                <label for="document_type" class="form-label">Document Type</label>
                <select class="form-select" id="document_type" name="document_type">
                    <option value="">All Types</option>
                    <?php foreach($documentTypes as $type): ?>
                        <option value="<?php echo htmlspecialchars($type['type_name']); ?>" 
                            <?php echo ($filters['document_type'] ?? '') == $type['type_name'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['type_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 me-2">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="index.php?controller=document&action=archive" class="btn btn-secondary w-100">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stats-card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
            <div class="stats-number"><?php echo number_format($total_documents); ?></div>
            <div class="stats-label">Archived Documents</div>
        </div>
    </div>
</div>

<!-- Archived Documents Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Archived Document List (Showing <?php echo count($documents); ?> of <?php echo number_format($total_documents); ?> items)</h5>
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
                        <th>Deleted By</th>
                        <th>Deleted Date</th>
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
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($doc['document_type']); ?></span>
                            </td>
                            <td><?php echo round($doc['file_size'] / 1024, 2); ?> KB</td>
                            <td><?php echo htmlspecialchars($doc['uploader_name'] ?? 'Unknown'); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($doc['delete_at'])); ?></td>
                            <td>
                                <a href="index.php?controller=document&action=restoreFromArchive&id=<?php echo $doc['id']; ?>" 
                                   class="btn btn-sm btn-success" 
                                   onclick="return confirm('Restore this document from archive?')"
                                   title="Restore">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                </a>
                                <a href="index.php?controller=document&action=permanentDelete&id=<?php echo $doc['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('WARNING: This will permanently delete the document from the system and cannot be undone. Continue?')"
                                   title="Permanent Delete">
                                    <i class="bi bi-trash"></i> Delete Permanently
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No archived documents found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php 
        $current_page = isset($current_page) ? (int)$current_page : 1;
        $total_pages = isset($total_pages) ? (int)$total_pages : 1;
        $limit = isset($limit) ? (int)$limit : 20;
        ?>
        
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=document&action=archive&page=<?php echo $current_page-1; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
                
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=document&action=archive&page=1&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">1</a>
                    </li>
                    <?php if($start_page > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?controller=document&action=archive&page=<?php echo $i; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if($end_page < $total_pages): ?>
                    <?php if($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=document&action=archive&page=<?php echo $total_pages; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $total_pages; ?></a>
                    </li>
                <?php endif; ?>
                
                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=document&action=archive&page=<?php echo $current_page+1; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <!-- Items per page selector -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing <?php echo ($current_page - 1) * $limit + 1; ?> to <?php echo min($current_page * $limit, $total_documents); ?> of <?php echo $total_documents; ?> entries
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show:</label>
                <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                    <?php 
                    $base_url = "?controller=document&action=archive&page=1";
                    if(!empty($filters)) {
                        $base_url .= '&' . http_build_query($filters);
                    }
                    ?>
                    <option value="<?php echo $base_url; ?>&limit=10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="<?php echo $base_url; ?>&limit=20" <?php echo $limit == 20 ? 'selected' : ''; ?>>20</option>
                    <option value="<?php echo $base_url; ?>&limit=50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="<?php echo $base_url; ?>&limit=100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                </select>
            </div>
        </div>
    </div>
</div>