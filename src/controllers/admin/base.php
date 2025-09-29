<?php
// src/controllers/admin/base.php

// This file should be included by all admin controllers that require authentication.

require_once ROOT_PATH . 'lib/db.php';
require_once ROOT_PATH . 'lib/helpers.php';

ensure_session_started();

// If the user is not logged in, redirect them to the login page.
if (!isset($_SESSION['user_id'])) {
    redirect('admin/auth/login');
}

// You can also add other common functionalities here,
// for example, loading user data from the database.
$current_user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'];