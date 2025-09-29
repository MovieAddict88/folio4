<?php
// public/index.php

// Check if the application is installed
if (!file_exists(__DIR__ . '/../config/config.php')) {
    // If not, redirect to the installation script
    header('Location: ../install.php');
    exit;
}

// Load configuration and bootstrap the application
require_once __DIR__ . '/../config/config.php';
require_once ROOT_PATH . 'lib/helpers.php'; // We will create this file later

// --- Basic Routing ---
// This is a very simple router. For a larger application, a dedicated library would be better.
$request_uri = $_SERVER['REQUEST_URI'];
$script_path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
$route = str_replace($script_path, '', $request_uri);
$route = trim($route, '/');
$route_parts = explode('/', $route);

// Default controller and action
$controller_name = !empty($route_parts[0]) ? $route_parts[0] : 'portfolio'; // 'portfolio' is the default
$action_name = $route_parts[1] ?? 'index';
$param = $route_parts[2] ?? null;

// Route to the admin area
if ($controller_name === 'admin') {
    $admin_controller_name = $action_name ?? 'dashboard';
    $admin_action_name = $param ?? 'index';
    $admin_param = $route_parts[3] ?? null;

    $admin_controller_file = ROOT_PATH . "src/controllers/admin/{$admin_controller_name}.php";

    if (file_exists($admin_controller_file)) {
        require_once $admin_controller_file;
        // The convention could be that the file defines functions for actions
        // e.g., dashboard_index(), dashboard_settings(), etc.
        // For now, we'll just include the file.
    } else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "Admin controller <strong>" . htmlspecialchars($admin_controller_name) . "</strong> not found.";
    }
} else {
    // Route to the public-facing site
    $controller_file = ROOT_PATH . "src/controllers/{$controller_name}.php";

    if (file_exists($controller_file)) {
        require_once $controller_file;
         // Again, we can adopt a convention for action functions later.
    } else {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "Controller <strong>" . htmlspecialchars($controller_name) . "</strong> not found.";
    }
}