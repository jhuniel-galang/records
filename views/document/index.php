<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Document Management</h2>
    <div>
        <a href="index.php?controller=document&action=archive" class="btn btn-secondary me-2">
             Archive
        </a>
        <a href="index.php?controller=document&action=upload" class="btn btn-primary">
             Upload Document
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter Documents</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="controller" value="document">
            <input type="hidden" name="action" value="index">
            
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Title, school, or file name" 
                       value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
            </div>
            
            <div class="col-md-3">
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
            
            <div class="col-md-2">
                <label for="doc_year" class="form-label">Year</label>
                <select class="form-select" id="doc_year" name="doc_year">
                    <option value="">All Years</option>
                    <?php foreach($available_years as $year): ?>
                        <option value="<?php echo $year; ?>" 
                            <?php echo ($filters['doc_year'] ?? '') == $year ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
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
                     Filter
                </button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="index.php?controller=document&action=index" class="btn btn-secondary w-100">
                     Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stats-number"><?php echo number_format($total_documents); ?></div>
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
        <h5 class="mb-0">Document List (Showing <?php echo count($documents); ?> of <?php echo number_format($total_documents); ?> documents)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="?controller=document&action=index&sort=id&order=<?php echo $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>" class="text-decoration-none text-dark">
                                ID <?php if($sort_by == 'id'): ?><?php echo $sort_order == 'ASC' ? '↑' : '↓'; ?><?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?controller=document&action=index&sort=doc_title&order=<?php echo $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>" class="text-decoration-none text-dark">
                                Document Title <?php if($sort_by == 'doc_title'): ?><?php echo $sort_order == 'ASC' ? '↑' : '↓'; ?><?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?controller=document&action=index&sort=doc_year&order=<?php echo $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>" class="text-decoration-none text-dark">
                                Year <?php if($sort_by == 'doc_year'): ?><?php echo $sort_order == 'ASC' ? '↑' : '↓'; ?><?php endif; ?>
                            </a>
                        </th>
                        <th>School</th>
                        <th>Type</th>
                        <th>
                            <a href="?controller=document&action=index&sort=uploader_at&order=<?php echo $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>" class="text-decoration-none text-dark">
                                Upload Date <?php if($sort_by == 'uploader_at'): ?><?php echo $sort_order == 'ASC' ? '↑' : '↓'; ?><?php endif; ?>
                            </a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
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
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No documents found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php 
        $current_page = isset($current_page) ? (int)$current_page : 1;
        $total_pages = isset($total_pages) ? (int)$total_pages : 1;
        $limit = isset($limit) ? (int)$limit : 10;
        $total_documents = isset($total_documents) ? (int)$total_documents : 0;
        
        $start_entry = ($current_page - 1) * $limit + 1;
        if ($start_entry < 1) $start_entry = 1;
        $end_entry = min($current_page * $limit, $total_documents);
        ?>
        
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=document&action=index&page=<?php echo max(1, $current_page-1); ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        ← Previous
                    </a>
                </li>
                
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=document&action=index&page=1&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">1</a>
                    </li>
                    <?php if($start_page > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?controller=document&action=index&page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if($end_page < $total_pages): ?>
                    <?php if($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=document&action=index&page=<?php echo $total_pages; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $total_pages; ?></a>
                    </li>
                <?php endif; ?>
                
                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=document&action=index&page=<?php echo min($total_pages, $current_page+1); ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        Next →
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <!-- Items per page selector -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing <?php echo $start_entry; ?> to <?php echo $end_entry; ?> of <?php echo $total_documents; ?> entries
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show:</label>
                <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                    <?php 
                    $base_url = "?controller=document&action=index&page=1&sort={$sort_by}&order={$sort_order}";
                    if(!empty($filters)) {
                        $base_url .= '&' . http_build_query($filters);
                    }
                    ?>
                    <option value="<?php echo $base_url; ?>&limit=10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="<?php echo $base_url; ?>&limit=25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                    <option value="<?php echo $base_url; ?>&limit=50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                    <option value="<?php echo $base_url; ?>&limit=100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                </select>
            </div>
        </div>
    </div>
</div>