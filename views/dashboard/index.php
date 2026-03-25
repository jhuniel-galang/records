<div class="row">
    <div class="col-md-12">
        <div class="alert alert-success">
            <h4>Welcome, <?php echo $user_name; ?>!</h4>
            <p>You are logged in as <strong><?php echo ucfirst($user_role); ?></strong></p>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Your Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Username:</strong> <?php echo $_SESSION['user_username']; ?></p>
                        <p><strong>Role:</strong> <?php echo ucfirst($user_role); ?></p>
                        <p><strong>User ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Available Schools</h5>
                    </div>
                    <div class="card-body">
                        <?php if(count($schools) > 0): ?>
                            <ul class="list-group">
                                <?php foreach($schools as $school): ?>
                                    <li class="list-group-item">
                                        <strong><?php echo $school['school_name']; ?></strong><br>
                                        <small><?php echo $school['level']; ?> - <?php echo $school['address']; ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No schools available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Dashboard Overview</h5>
                    </div>
                    <div class="card-body">
                        <p>This is your dashboard. More features will be added soon!</p>
                        <div class="alert alert-info">
                            <strong>Temporary Dashboard:</strong> You can now:
                            <ul class="mt-2">
                                <li>View your profile information</li>
                                <li>See the list of schools</li>
                                <li>Navigate through the system</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>