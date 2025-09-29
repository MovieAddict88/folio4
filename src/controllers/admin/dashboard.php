<?php
// src/controllers/admin/dashboard.php

// Ensure user is authenticated
require_once __DIR__ . '/base.php';

$action = $GLOBALS['admin_action_name'] ?? 'index';

switch ($action) {
    case 'index':
    default:
        dashboard_index();
        break;
}

function dashboard_index() {
    // For now, the dashboard will be simple.
    // Later, we can add stats like number of projects, documents, etc.
    $data = [
        'title' => 'Dashboard',
        'layout' => 'admin' // We will use the main admin layout
    ];

    echo render_view('admin/dashboard', $data);
}