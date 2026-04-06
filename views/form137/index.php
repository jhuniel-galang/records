<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Form 137 Management</h2>
    <div>
        <a href="index.php?controller=document&action=upload" class="btn btn-primary">
            <i class="bi bi-cloud-upload"></i> Upload Form 137
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter Form 137</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="controller" value="form137">
            <input type="hidden" name="action" value="index">
            
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="File name or student name" 
                       value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
            </div>
            
            <div class="col-md-3">
                <label for="school_name" class="form-label">School</label>
                <select class="form-select" id="school_name" name="school_name">
                    <option value="">All Schools</option>
                    <?php foreach($schools as $school): ?>
                        <option value="<?php echo htmlspecialchars($school['school_name']); ?>" 
                            <?php echo ($filters['school_name'] ?? '') == $school['school_name'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($school['school_name']); ?>
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
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="index.php?controller=form137&action=index" class="btn btn-secondary w-100">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stats-number"><?php echo number_format($total_records); ?></div>
            <div class="stats-label">Total Form 137</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <div class="stats-number"><?php echo number_format(count($records)); ?></div>
            <div class="stats-label">Displaying</div>
        </div>
    </div>
</div>

<!-- Form 137 Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Form 137 Documents (Showing <?php echo count($records); ?> of <?php echo number_format($total_records); ?>)</h5>
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
                    </thead>
                <tbody>
                    <?php if(count($records) > 0): ?>
                        <?php foreach($records as $record): ?>
                        <tr>
                            <td><?php echo $record['id']; ?></td>
                            <td>
                                <i class="bi bi-file-pdf text-danger"></i> 
                                <?php echo htmlspecialchars($record['file_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($record['school_name'] ?: 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-danger"><?php echo htmlspecialchars($record['document_type']); ?></span>
                            </td>
                            <td><?php echo round($record['file_size'] / 1024, 2); ?> KB</td>
                            <td><?php echo htmlspecialchars($record['uploader_name'] ?? 'Unknown'); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($record['uploader_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="index.php?controller=form137&action=view&id=<?php echo $record['id']; ?>" 
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="index.php?controller=document&action=preview&id=<?php echo $record['id']; ?>" 
                                       class="btn btn-sm btn-success" title="Preview PDF" target="_blank">
                                        <i class="bi bi-file-earmark"></i>
                                    </a>
                                    <a href="index.php?controller=form137&action=printForm&id=<?php echo $record['id']; ?>" 
                                       class="btn btn-sm btn-warning" title="Print Form 137" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" title="Certificates">
                                        <i class="bi bi-award"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="index.php?controller=form137&action=certificate&id=<?php echo $record['id']; ?>&type=good_moral" target="_blank">Good Moral Certificate</a></li>
                                        <li><a class="dropdown-item" href="index.php?controller=form137&action=certificate&id=<?php echo $record['id']; ?>&type=enrollment" target="_blank">Enrollment Certificate</a></li>
                                        <li><a class="dropdown-item" href="index.php?controller=form137&action=certificate&id=<?php echo $record['id']; ?>&type=completion" target="_blank">Completion Certificate</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No Form 137 documents found</td>
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
                    <a class="page-link" href="?controller=form137&action=index&page=<?php echo $current_page-1; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
                
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=form137&action=index&page=1&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">1</a>
                    </li>
                    <?php if($start_page > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?controller=form137&action=index&page=<?php echo $i; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if($end_page < $total_pages): ?>
                    <?php if($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=form137&action=index&page=<?php echo $total_pages; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $total_pages; ?></a>
                    </li>
                <?php endif; ?>
                
                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=form137&action=index&page=<?php echo $current_page+1; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <!-- Items per page selector -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing <?php echo ($current_page - 1) * $limit + 1; ?> to <?php echo min($current_page * $limit, $total_records); ?> of <?php echo $total_records; ?> entries
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show:</label>
                <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                    <?php 
                    $base_url = "?controller=form137&action=index&page=1";
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