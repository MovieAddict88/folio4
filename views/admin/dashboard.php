<?php
// This is the main dashboard view.
// We can add logic here to fetch summary data, like counts of projects, messages, etc.

// For now, it will be a welcome message and a set of quick links.

// Example of how you might fetch data in the future:
// require_once __DIR__ . '/../../app/database.php';
// $project_count = pdo()->query("SELECT COUNT(*) FROM projects")->fetchColumn();
// $message_count = pdo()->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();

$project_count = 0; // Placeholder
$message_count = 0; // Placeholder
$testimonial_count = 0; // Placeholder
?>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    .dashboard-card {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
    }
    .dashboard-card h3 {
        margin-top: 0;
        font-size: 1.2em;
        color: #1C2B4A;
    }
    .dashboard-card .count {
        font-size: 2.5em;
        font-weight: 700;
        color: #E2B714;
    }
    .dashboard-card a {
        display: inline-block;
        margin-top: 15px;
        background-color: #1C2B4A;
        color: #fff;
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
    }
    .dashboard-card a:hover {
        background-color: #3a4a6b;
    }
</style>

<h2>Welcome, Administrator!</h2>
<p>This is your central hub to manage all the content on your portfolio website. Use the links in the sidebar or the quick access cards below to get started.</p>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <h3>Manage Projects</h3>
        <p class="count"><?php echo $project_count; ?></p>
        <a href="admin.php?page=projects">Go to Projects</a>
    </div>
    <div class="dashboard-card">
        <h3>Contact Messages</h3>
        <p class="count"><?php echo $message_count; ?></p>
        <a href="admin.php?page=messages">View Messages</a>
    </div>
    <div class="dashboard-card">
        <h3>Testimonials</h3>
        <p class="count"><?php echo $testimonial_count; ?></p>
        <a href="admin.php?page=testimonials">Manage Testimonials</a>
    </div>
    <div class="dashboard-card">
        <h3>Update Profile</h3>
        <p style="font-size: 2.5em; color: #E2B714;">✎</p>
        <a href="admin.php?page=profile">Edit Profile</a>
    </div>
</div>