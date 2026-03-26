<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>School Management</h2>
    <a href="index.php?controller=school&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New School
    </a>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter Schools</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3">
            <input type="hidden" name="controller" value="school">
            <input type="hidden" name="action" value="index">
            
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="School name, address or principal" 
                       value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
            </div>
            
            <div class="col-md-2">
                <label for="level" class="form-label">Level</label>
                <select class="form-select" id="level" name="level">
                    <option value="">All Levels</option>
                    <option value="Elementary" <?php echo ($filters['level'] ?? '') == 'Elementary' ? 'selected' : ''; ?>>Elementary</option>
                    <option value="HS" <?php echo ($filters['level'] ?? '') == 'HS' ? 'selected' : ''; ?>>High School (HS)</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="1" <?php echo ($filters['status'] ?? '') == '1' ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo ($filters['status'] ?? '') == '0' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="created_by" class="form-label">Created By</label>
                <select class="form-select" id="created_by" name="created_by">
                    <option value="">All Users</option>
                    <?php foreach($creators as $creator): ?>
                        <option value="<?php echo $creator['id']; ?>" 
                            <?php echo ($filters['created_by'] ?? '') == $creator['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($creator['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="index.php?controller=school&action=index" class="btn btn-secondary w-100">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stats-number"><?php echo number_format((int)$total_schools); ?></div>
            <div class="stats-label">Total Schools</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <div class="stats-number"><?php echo number_format((int)$elementary_count); ?></div>
            <div class="stats-label">Elementary Schools</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
            <div class="stats-number"><?php echo number_format((int)$hs_count); ?></div>
            <div class="stats-label">High Schools</div>
        </div>
    </div>
</div>

<!-- Schools Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">School List (Showing <?php echo count($schools); ?> of <?php echo number_format((int)$total_schools); ?> schools)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="?controller=school&action=index&sort=id&order=<?php echo $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>" class="text-decoration-none text-dark">
                                ID
                                <?php if($sort_by == 'id'): ?>
                                    <i class="bi bi-caret-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?>-fill"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?controller=school&action=index&sort=school_name&order=<?php echo $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>" class="text-decoration-none text-dark">
                                School Name
                                <?php if($sort_by == 'school_name'): ?>
                                    <i class="bi bi-caret-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?>-fill"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?controller=school&action=index&sort=level&order=<?php echo $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>" class="text-decoration-none text-dark">
                                Level
                                <?php if($sort_by == 'level'): ?>
                                    <i class="bi bi-caret-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?>-fill"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Address</th>
                        <th>Principal</th>
                        <th>Created By</th>
                        <th>
                            <a href="?controller=school&action=index&sort=status&order=<?php echo $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&limit=<?php echo $limit; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>" class="text-decoration-none text-dark">
                                Status
                                <?php if($sort_by == 'status'): ?>
                                    <i class="bi bi-caret-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?>-fill"></i>
                                <?php endif; ?>
                            </a>
                        </th>
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
        
        <!-- Pagination -->
        <?php 
        $current_page = isset($current_page) ? (int)$current_page : 1;
        $total_pages = isset($total_pages) ? (int)$total_pages : 1;
        $limit = isset($limit) ? (int)$limit : 10;
        $total_schools = isset($total_schools) ? (int)$total_schools : 0;
        ?>
        
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=school&action=index&page=<?php echo $current_page-1; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
                
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=school&action=index&page=1&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">1</a>
                    </li>
                    <?php if($start_page > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?controller=school&action=index&page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if($end_page < $total_pages): ?>
                    <?php if($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=school&action=index&page=<?php echo $total_pages; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $total_pages; ?></a>
                    </li>
                <?php endif; ?>
                
                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=school&action=index&page=<?php echo $current_page+1; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <!-- Items per page selector -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing <?php echo ($current_page - 1) * $limit + 1; ?> to <?php echo min($current_page * $limit, $total_schools); ?> of <?php echo $total_schools; ?> entries
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show:</label>
                <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                    <?php 
                    $base_url = "?controller=school&action=index&page=1&sort={$sort_by}&order={$sort_order}";
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