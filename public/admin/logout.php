<?php
require_once '../../src/init.php';

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php?logged_out=true');
exit;