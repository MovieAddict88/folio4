<?php
session_start();

// If the user is not logged in, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include necessary files
require_once '../config/config.php';
require_once '../app/functions.php'; // We will create this file for helper functions

// The page variable will determine which admin view to show
$page = $_GET['page'] ?? 'dashboard';

// Simple router to load the correct admin view
$allowed_pages = [
    'dashboard',
    'profile',
    'education',
    'experience',
    'skills',
    'projects',
    'testimonials',
    'downloads',
    'messages'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard'; // Default to dashboard if page is not allowed
}

// Page titles for the header
$page_titles = [
    'dashboard' => 'Admin Dashboard',
    'profile' => 'Manage Profile & Settings',
    'education' => 'Manage Education',
    'experience' => 'Manage Experience',
    'skills' => 'Manage Skills',
    'projects' => 'Manage Projects',
    'testimonials' => 'Manage Testimonials',
    'downloads' => 'Manage Downloads',
    'messages' => 'View Contact Messages'
];
$page_title = $page_titles[$page] ?? 'Admin Panel';

// Include the header
include '../views/admin/header.php'; // We will create this next
?>

<div class="admin-wrapper">
    <!-- Sidebar Navigation -->
    <?php include '../views/admin/sidebar.php'; // We will create this next ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-header">
            <h1><?php echo htmlspecialchars($page_title); ?></h1>
        </div>
        <div class="content-body">
            <?php
            // Load the corresponding view file from the 'views/admin/' directory
            $view_file = "../views/admin/{$page}.php";
            if (file_exists($view_file)) {
                include $view_file;
            } else {
                echo "<p>View file not found for page: " . htmlspecialchars($page) . "</p>";
                // Default to dashboard content if specific view is missing
                include '../views/admin/dashboard.php';
            }
            ?>
        </div>
    </div>
</div>

<?php
// Include the footer
include '../views/admin/footer.php'; // We will create this next
?>