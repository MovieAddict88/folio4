<?php
// Fetch some stats for the dashboard
$total_portfolio_items = $pdo->query("SELECT count(*) FROM portfolio_items")->fetchColumn();
$total_documents = $pdo->query("SELECT count(*) FROM documents")->fetchColumn();
$profile = $pdo->query("SELECT * FROM profile WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

?>

<h1 class="h2 mb-4">Dashboard</h1>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Portfolio Items</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $total_portfolio_items; ?></h5>
                <p class="card-text">Total projects in your portfolio.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Protected Documents</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo $total_documents; ?></h5>
                <p class="card-text">Total downloadable documents.</p>
            </div>
        </div>
    </div>
     <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">PHP Version</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo PHP_VERSION; ?></h5>
                <p class="card-text">Current server PHP version.</p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        Quick Overview
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center">
            <img src="../<?php echo htmlspecialchars($profile['profile_picture'] ?? 'https://via.placeholder.com/150'); ?>" class="rounded-circle me-3" width="80" height="80" alt="Profile Picture">
            <div>
                <h5 class="card-title mb-0"><?php echo htmlspecialchars($profile['full_name'] ?? 'Your Name'); ?></h5>
                <p class="card-text text-muted"><?php echo htmlspecialchars($profile['title'] ?? 'Your Title'); ?></p>
            </div>
        </div>
        <hr>
        <p>Welcome to your admin panel. From here, you can manage all aspects of your portfolio website.</p>
        <a href="index.php?page=profile" class="btn btn-secondary">Edit Profile</a>
        <a href="index.php?page=portfolio" class="btn btn-secondary">Manage Portfolio</a>
    </div>
</div>