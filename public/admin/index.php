<?php
require_once '../../src/init.php';

// If config doesn't exist, redirect to installer
if (!file_exists(CONFIG_FILE)) {
    header('Location: ../../install.php');
    exit;
}

require_once SRC_PATH . 'admin_auth.php';

// Basic routing
$page = $_GET['page'] ?? 'dashboard';

// Whitelist of allowed pages
$allowed_pages = ['dashboard', 'profile', 'portfolio', 'documents'];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

include_once VIEW_PATH . 'partials/admin_header.php';
include_once VIEW_PATH . 'admin/' . $page . '.php';
include_once VIEW_PATH . 'partials/admin_footer.php';