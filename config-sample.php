<?php
// config-sample.php

/**
 * Database Configuration
 *
 * Instructions:
 * 1. Rename this file to `config.php` and place it in the `config/` directory.
 * 2. Fill in your database host, name, user, and password below.
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for the portfolio */
define('DB_HOST', 'your_database_host');

/** The name of the database for the portfolio */
define('DB_NAME', 'your_database_name');

/** MySQL database username */
define('DB_USER', 'your_database_user');

/** MySQL database password */
define('DB_PASS', 'your_database_password');


// --- No need to edit below this line ---

/** Absolute path to the project root. */
define('ROOT_PATH', dirname(__DIR__) . '/');

/** Base URL of the site. */
// This attempts to auto-detect the URL. You may need to hardcode it if it doesn't work correctly.
// Example: define('BASE_URL', 'http://yourdomain.com/portfolio/');
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . str_replace(['/install.php', '/public/index.php'], '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', rtrim($base_url, '/') . '/');