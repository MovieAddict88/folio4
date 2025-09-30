<?php
// This script should be included at the top of every admin-only page.

// If not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Optional: Add session expiration logic here if needed
// For example, check if a session variable for login time is older than X minutes.
?>