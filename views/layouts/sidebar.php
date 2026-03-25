<?php
// Get current page for active state
$current_page = $_GET['controller'] ?? 'dashboard';
?>
<div class="col-md-3 col-lg-2 px-0">
    <div class="sidebar">
        <div class="sidebar-header">
            <h5 class="mb-0">Menu</h5>
        </div>
        <div class="user-info">
            <strong><?php echo $_SESSION['user_name']; ?></strong>
            <br>
            <small class="text-muted"><?php echo ucfirst($_SESSION['user_role']); ?></small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>" 
                   href="index.php?controller=dashboard&action=index">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <?php if($_SESSION['user_role'] == 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'user' ? 'active' : ''; ?>" 
                   href="index.php?controller=user&action=index">
                    <i class="bi bi-people"></i> User Management
                </a>
            </li>
            
            <!-- Activity Logs Link (Admin Only) -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'activitylog' ? 'active' : ''; ?>" 
                   href="index.php?controller=activitylog&action=index">
                    <i class="bi bi-clock-history"></i> Activity Logs
                </a>
            </li>
            <?php endif; ?>



            <?php if($_SESSION['user_role'] == 'admin'): ?>
<li class="nav-item">
    <a class="nav-link <?php echo $current_page == 'documenttype' ? 'active' : ''; ?>" 
       href="index.php?controller=documenttype&action=index">
        <i class="fas fa-tags"></i> Document Types
    </a>
</li>
<?php endif; ?>



            
            <li class="nav-item">
    <a class="nav-link <?php echo in_array($current_page, ['school']) ? 'active' : ''; ?>" 
       href="#schoolsSubmenu" data-bs-toggle="collapse" aria-expanded="<?php echo in_array($current_page, ['school']) ? 'true' : 'false'; ?>">
        <i class="bi bi-building"></i> Schools <span class="float-end">▼</span>
    </a>
    <ul class="collapse <?php echo in_array($current_page, ['school']) ? 'show' : ''; ?> list-unstyled" id="schoolsSubmenu">
        <li><a class="nav-link ps-4 <?php echo $current_page == 'school' && $action == 'index' ? 'active' : ''; ?>" 
               href="index.php?controller=school&action=index">View Schools</a></li>
        <li><a class="nav-link ps-4 <?php echo $current_page == 'school' && $action == 'create' ? 'active' : ''; ?>" 
               href="index.php?controller=school&action=create">Add School</a></li>
    </ul>
</li>

            
            <li class="nav-item">
    <a class="nav-link <?php echo in_array($current_page, ['document']) ? 'active' : ''; ?>" 
       href="#documentsSubmenu" data-bs-toggle="collapse" aria-expanded="<?php echo in_array($current_page, ['document']) ? 'true' : 'false'; ?>">
        <i class="bi bi-file-text"></i> Documents <span class="float-end">▼</span>
    </a>
    <ul class="collapse <?php echo in_array($current_page, ['document']) ? 'show' : ''; ?> list-unstyled" id="documentsSubmenu">
        <li><a class="nav-link ps-4 <?php echo $current_page == 'document' && $action == 'index' ? 'active' : ''; ?>" 
               href="index.php?controller=document&action=index">All Documents</a></li>
        <li><a class="nav-link ps-4 <?php echo $current_page == 'document' && $action == 'upload' ? 'active' : ''; ?>" 
               href="index.php?controller=document&action=upload">Upload Document</a></li>
    </ul>
</li>
            
            <li class="nav-item">
                <a class="nav-link" href="index.php?controller=auth&action=logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>