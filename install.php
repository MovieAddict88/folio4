<?php
// install.php

$config_path = __DIR__ . '/config/config.php';
$config_sample_path = __DIR__ . '/config-sample.php';
$step = 1;
$errors = [];
$success = false;

// --- STEP 1: Check for config.php ---
if (!file_exists($config_path)) {
    $step = 1;
} else {
    // --- STEP 2: config.php exists, try to set up the database ---
    $step = 2;
    require_once $config_path;

    // Check if the constants are default, which means user hasn't edited the file.
    if (DB_HOST === 'your_database_host' || DB_NAME === 'your_database_name') {
        $errors[] = 'It looks like you have not yet updated your database credentials in <code>config/config.php</code>. Please edit the file before proceeding.';
    } else {
        try {
            // Test DB connection
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Check if installation is already done by checking for the users table
            $stmt = $pdo->query("SELECT 1 FROM `users` LIMIT 1");
            if ($stmt !== false && $stmt->fetch()) {
                 $success = true; // Already installed
            } else {
                // --- Create Database Tables ---
                // Users Table
                $pdo->exec("CREATE TABLE IF NOT EXISTS `users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(255) NOT NULL, `password` varchar(255) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                // Settings Table
                $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `value` text, PRIMARY KEY (`id`), UNIQUE KEY `name` (`name`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                // Portfolio Items Table
                $pdo->exec("CREATE TABLE IF NOT EXISTS `portfolio_items` ( `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(255) NOT NULL, `description` text, `image` varchar(255) DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                // Documents Table
                $pdo->exec("CREATE TABLE IF NOT EXISTS `documents` ( `id` int(11) NOT NULL AUTO_INCREMENT, `filename` varchar(255) NOT NULL, `original_filename` varchar(255) NOT NULL, `password` varchar(255) DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

                // --- Add Default Admin User ---
                $admin_user = 'admin';
                $admin_pass = 'password'; // A default, insecure password. User should change this immediately.
                $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->execute([$admin_user, $hashed_password]);

                // Add a default name setting
                $stmt = $pdo->prepare("INSERT INTO settings (name, value) VALUES ('your_name', 'Your Name')");
                $stmt->execute();

                $success = true;
            }

        } catch (PDOException $e) {
            if ($e->getCode() === 1049) { // SQLSTATE[HY000] [1049] Unknown database
                 $errors[] = "The database '<strong>" . DB_NAME . "</strong>' does not exist. Please create it first and then refresh this page.";
            } else if ($e->getCode() === 1045) { // SQLSTATE[HY000] [1045] Access denied
                 $errors[] = "Database access denied. Please double-check your database user and password in <code>config/config.php</code>.";
            } else {
                $errors[] = 'Database setup failed: ' . $e->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Setup</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 700px; margin: 40px auto; padding: 20px; background-color: #f4f4f4; }
        .container { background-color: #fff; padding: 30px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        code { background-color: #eee; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .btn { display: inline-block; background-color: #007BFF; color: #fff; padding: 10px 15px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; }
        .btn:hover { background-color: #0056b3; }
        .error, .success, .info { padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid transparent; }
        .error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb; }
        ul { padding-left: 20px; }
        li { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Portfolio Installation</h1>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>Setup failed!</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; // Not escaping because we control the error messages and need HTML ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <?php if ($step === 1): ?>
            <div class="info">
                <h3>Step 1: Create Configuration File</h3>
                <p>Welcome! To get started, you need to create your configuration file.</p>
                <ol>
                    <li>Locate the file named <code>config-sample.php</code> in the root directory of the project.</li>
                    <li>Rename or copy this file to <code>config.php</code> and place it inside the <code>/config</code> directory.</li>
                    <li>Open <code>config/config.php</code> in a text editor and fill in your database connection details (host, database name, user, and password).</li>
                    <li>After you have created and edited the file, refresh this page.</li>
                </ol>
            </div>
        <?php elseif ($step === 2 && $success): ?>
             <div class="success">
                <strong>Installation successful!</strong>
                <p>The database tables have been created and the default admin user has been added.</p>
                <p><strong>Your Admin Credentials:</strong></p>
                <ul>
                    <li>Username: <code>admin</code></li>
                    <li>Password: <code>password</code></li>
                </ul>
                <p><strong>IMPORTANT:</strong> Please log in and change this default password immediately from the "Settings" page.</p>
                <a href="public/" class="btn">Go to your Portfolio</a>
            </div>
        <?php elseif ($step === 2 && !empty($errors)): ?>
             <div class="info">
                <h3>Step 2: Database Setup</h3>
                <p>The <code>config/config.php</code> file was found, but we ran into an issue. Please resolve the error(s) listed above and then refresh this page to try again.</p>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>