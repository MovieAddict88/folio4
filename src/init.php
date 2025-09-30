<?php
// Start session
session_start();

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define core paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('SRC_PATH', ROOT_PATH . 'src/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('VIEW_PATH', ROOT_PATH . 'views/');
define('DATABASE_PATH', ROOT_PATH . 'database/');
define('UPLOADS_PATH', PUBLIC_PATH . 'uploads/');

// Define core files
define('CONFIG_FILE', SRC_PATH . 'config.php');

// Create uploads directory if it doesn't exist
if (!is_dir(UPLOADS_PATH)) {
    mkdir(UPLOADS_PATH, 0755, true);
}

// Load database configuration if it exists
if (file_exists(CONFIG_FILE)) {
    require_once CONFIG_FILE;
}