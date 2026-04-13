<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User Management</h2>
    <a href="index.php?controller=user&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New User
    </a>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter Users</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php">
            <input type="hidden" name="controller" value="user">
            <input type="hidden" name="action" value="index">
            
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Name, username or email" 
                           value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                
                <div class="col-md-2">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">All Roles</option>
                        <option value="admin" <?php echo ($filters['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="uploader" <?php echo ($filters['role'] ?? '') == 'uploader' ? 'selected' : ''; ?>>Uploader</option>
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
                
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="index.php?controller=user&action=index" class="btn btn-secondary flex-grow-1">
                            <i class="bi bi-arrow-repeat"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Summary -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stats-number"><?php echo (int)$total_users; ?></div>
            <div class="stats-label">Total Users</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
            <div class="stats-number"><?php echo (int)$admin_count; ?></div>
            <div class="stats-label">Admins</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8 0%, #6c757d 100%);">
            <div class="stats-number"><?php echo (int)$uploader_count; ?></div>
            <div class="stats-label">Uploaders</div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">User List (Showing <?php echo count($users); ?> of <?php echo (int)$total_users; ?> users)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($users) > 0): ?>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge <?php echo $user['role'] == 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="index.php?controller=user&action=view&id=<?php echo $user['id']; ?>" 
                                   class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="index.php?controller=user&action=edit&id=<?php echo $user['id']; ?>" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?controller=user&action=delete&id=<?php echo $user['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('WARNING: This will permanently delete this user and cannot be undone! Continue?')"
                                   title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php 
        $current_page = (int)$current_page;
        $total_pages = (int)$total_pages;
        $limit = (int)$limit;
        $total_users = (int)$total_users;
        ?>
        
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=user&action=index&page=<?php echo $current_page-1; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
                
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=user&action=index&page=1&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">1</a>
                    </li>
                    <?php if($start_page > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?controller=user&action=index&page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if($end_page < $total_pages): ?>
                    <?php if($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?controller=user&action=index&page=<?php echo $total_pages; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"><?php echo $total_pages; ?></a>
                    </li>
                <?php endif; ?>
                
                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?controller=user&action=index&page=<?php echo $current_page+1; ?>&limit=<?php echo $limit; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <!-- Items per page selector -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Showing <?php echo ($current_page - 1) * $limit + 1; ?> to <?php echo min($current_page * $limit, $total_users); ?> of <?php echo $total_users; ?> entries
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show:</label>
                <select class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                    <?php 
                    $base_url = "?controller=user&action=index&page=1&sort={$sort_by}&order={$sort_order}";
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