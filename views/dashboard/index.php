<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-card mb-4">
        <div class="welcome-content">
            <div>
                <h2 class="mb-2">Welcome back, <?php echo $user_name; ?>!</h2>
                <p class="mb-0">You are logged in as <span class="badge bg-light text-dark"><?php echo ucfirst($user_role); ?></span></p>
            </div>
            <div class="welcome-icon">
                <i class="bi bi-person-circle"></i>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="bi bi-file-text"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_documents); ?></h3>
                    <p>Total Documents</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_schools); ?></h3>
                    <p>Schools/Offices</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="bi bi-tags"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_office_types); ?></h3>
                    <p>Office Types</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_users_count); ?></h3>
                    <p>System Users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section for Document Type Analytics -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Document Type Analytics</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="controller" value="dashboard">
                <input type="hidden" name="action" value="index">
                
                <div class="col-md-3">
                    <label for="doc_year" class="form-label">Document Year</label>
                    <select class="form-select" id="doc_year" name="doc_year">
                        <option value="">All Years</option>
                        <?php foreach($available_years as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo ($filters['doc_year'] ?? '') == $year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="office_type_id" class="form-label">Office Type</label>
                    <select class="form-select" id="office_type_id" name="office_type_id">
                        <option value="">All Office Types</option>
                        <?php foreach($office_types as $type): ?>
                            <option value="<?php echo $type['id']; ?>" <?php echo ($filters['office_type_id'] ?? '') == $type['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="school_id" class="form-label">School/Office</label>
                    <select class="form-select" id="school_id" name="school_id">
                        <option value="">All Schools/Offices</option>
                        <?php foreach($all_schools as $school_item): ?>
                            <option value="<?php echo $school_item['id']; ?>" <?php echo ($filters['school_id'] ?? '') == $school_item['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($school_item['school_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                         Apply Filters
                    </button>
                    <a href="index.php?controller=dashboard&action=index" class="btn btn-secondary w-100">
                         Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Document Type Analytics Table -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Document Type Analytics</h5>
                    <small class="text-muted">Number of documents per document type</small>
                </div>
                
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Document Type</th>
                            <th>Number of Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($document_type_stats) > 0): ?>
                            <?php foreach($document_type_stats as $stat): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="type-color" style="background: <?php 
                                            $colors = ['#667eea', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1', '#fd7e14'];
                                            echo $colors[array_rand($colors)];
                                        ?>;"></div>
                                        <strong><?php echo htmlspecialchars($stat['document_type']); ?></strong>
                                    </div>
                                 </td>
                                <td>
                                    <span class="badge badge-primary"><?php echo number_format($stat['total']); ?></span>
                                 </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                                    <p class="mt-2 text-muted">No documents found with selected filters</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th><?php echo number_format($total_documents); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Filter Section for Schools by Document Count -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Schools by Document Count</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="controller" value="dashboard">
                <input type="hidden" name="action" value="index">
                
                <div class="col-md-4">
                    <label for="school_year" class="form-label">Document Year</label>
                    <select class="form-select" id="school_year" name="school_year">
                        <option value="">All Years</option>
                        <?php foreach($available_years as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo ($school_filters['doc_year'] ?? '') == $year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="school_doc_type" class="form-label">Document Type</label>
                    <select class="form-select" id="school_doc_type" name="school_doc_type">
                        <option value="">All Document Types</option>
                        <?php foreach($document_types as $doc_type): ?>
                            <option value="<?php echo htmlspecialchars($doc_type['type_name']); ?>" 
                                <?php echo ($school_filters['document_type'] ?? '') == $doc_type['type_name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($doc_type['type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                         Apply Filters
                    </button>
                    <a href="index.php?controller=dashboard&action=index" class="btn btn-secondary w-100">
                         Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Schools by Document Count Table -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Schools by Document Count</h5>
                    <small class="text-muted">Number of documents per school/office</small>
                </div>
                
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>School/Office Name</th>
                            <th>Number of Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($schools_by_document_count) > 0): ?>
                            <?php $rank = 1; ?>
                            <?php foreach($schools_by_document_count as $school_stat): ?>
                            <tr>
                                <td>
                                    <?php 
                                    if($rank == 1) echo '<span class="rank-icon">🥇</span>';
                                    elseif($rank == 2) echo '<span class="rank-icon">🥈</span>';
                                    elseif($rank == 3) echo '<span class="rank-icon">🥉</span>';
                                    else echo '<span class="rank-number">' . $rank . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($school_stat['school_name']); ?></strong>
                                 </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo number_format($school_stat['doc_count']); ?></span>
                                 </td>
                             </tr>
                            <?php $rank++; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <tr>
                                <td colspan="3" class="text-center py-4">
                                    <i class="bi bi-building" style="font-size: 48px; color: #ccc;"></i>
                                    <p class="mt-2 text-muted">No schools/offices found with selected filters</p>
                                 </td>
                             </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                         <tr>
                            <th colspan="2">Total Schools with Documents</th>
                            <th><?php echo number_format(count($schools_by_document_count)); ?></th>
                         </tr>
                    </tfoot>
                 </table>
            </div>
        </div>
    </div>

    <!-- Top Schools and Documents by Year -->
    <div class="row">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Top Schools by Document Count</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                 <tr>
                                    <th>School/Office</th>
                                    <th>Office Type</th>
                                    <th>Documents</th>
                                 </tr>
                            </thead>
                            <tbody>
                                <?php if(count($top_schools) > 0): ?>
                                    <?php 
                                    $rank_colors = ['🥇', '🥈', '🥉', '📄', '📄'];
                                    $rank = 0;
                                    foreach($top_schools as $school_item): 
                                    ?>
                                     <tr>
                                         <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2" style="font-size: 1.2rem;"><?php echo $rank_colors[$rank++]; ?></span>
                                                <strong><?php echo htmlspecialchars($school_item['school_name']); ?></strong>
                                            </div>
                                          </td>
                                         <td>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($school_item['office_type_name'] ?? 'N/A'); ?></span>
                                          </td>
                                         <td>
                                            <span class="badge bg-success"><?php echo number_format($school_item['doc_count']); ?></span>
                                          </td>
                                     </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                     <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <i class="bi bi-bar-chart" style="font-size: 48px; color: #ccc;"></i>
                                            <p class="mt-2 text-muted">No data available</p>
                                         </td>
                                     </tr>
                                <?php endif; ?>
                            </tbody>
                         </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar me-2"></i>Documents by Year</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                 <tr>
                                    <th>Year</th>
                                    <th>Number of Documents</th>
                                 </tr>
                            </thead>
                            <tbody>
                                <?php if(count($documents_by_year) > 0): ?>
                                    <?php foreach($documents_by_year as $year_stat): ?>
                                     <tr>
                                         <td>
                                            <strong><?php echo $year_stat['doc_year'] ?: 'No Year'; ?></strong>
                                          </td>
                                         <td>
                                            <span class="badge bg-primary"><?php echo number_format($year_stat['total']); ?></span>
                                          </td>
                                     </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                     <tr>
                                        <td colspan="2" class="text-center py-4">
                                            <i class="bi bi-calendar-x" style="font-size: 48px; color: #ccc;"></i>
                                            <p class="mt-2 text-muted">No data available</p>
                                         </td>
                                     </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                 <tr>
                                    <th>Total</th>
                                    <th><?php echo number_format($total_documents); ?></th>
                                 </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 0;
}

/* Welcome Card */
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 25px 30px;
    color: white;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
}

.welcome-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.welcome-icon i {
    font-size: 60px;
    opacity: 0.8;
}

/* Statistics Cards */
.stat-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
}

.stat-icon.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.stat-icon.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.stat-icon.bg-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6c757d 100%);
}

.stat-info h3 {
    margin: 0;
    font-size: 28px;
    font-weight: bold;
    color: #2c3e50;
}

.stat-info p {
    margin: 0;
    color: #7f8c8d;
    font-size: 14px;
}

/* Card Styles */
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.card-header {
    background: white;
    border-bottom: 1px solid #eef2f7;
    padding: 18px 25px;
    font-weight: 600;
}

.card-header h5 {
    color: #2c3e50;
    font-weight: 600;
}

/* Table Styles */
.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f8f9fa;
    border-bottom: 2px solid #eef2f7;
    color: #2c3e50;
    font-weight: 600;
    padding: 12px 15px;
}

.table tbody td {
    padding: 12px 15px;
    vertical-align: middle;
}

/* Type Color Indicator */
.type-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
    margin-right: 10px;
}

/* Rank Styles */
.rank-icon {
    font-size: 1.5rem;
}

.rank-number {
    font-size: 1.1rem;
    font-weight: bold;
    color: #6c757d;
}

/* Badge Styles */
.badge {
    padding: 6px 12px;
    font-weight: 500;
    border-radius: 8px;
}

.badge-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

/* Table Footer */
tfoot.table-light th {
    background: #f8f9fa;
    padding: 12px 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .welcome-content {
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-icon {
        margin-top: 15px;
    }
    
    .stat-card {
        margin-bottom: 15px;
    }
    
    .stat-info h3 {
        font-size: 22px;
    }
}
</style>