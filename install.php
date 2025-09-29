<?php
// install.php

// Simple check to see if the config file already exists.
// If it does, we'll assume the installation is complete and redirect to the admin panel.
if (file_exists('config/config.php')) {
    header('Location: public/admin/');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get database credentials from the form
    $db_host = $_POST['db_host'] ?? '';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';
    $admin_user = $_POST['admin_user'] ?? 'admin';
    $admin_pass = $_POST['admin_pass'] ?? '';

    // --- Validation ---
    if (empty($db_host)) $errors[] = 'Database host is required.';
    if (empty($db_name)) $errors[] = 'Database name is required.';
    if (empty($db_user)) $errors[] = 'Database user is required.';
    // DB password can be empty for local setups
    if (empty($admin_pass)) $errors[] = 'Admin password is required.';


    if (empty($errors)) {
        // --- 1. Test Database Connection ---
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $errors[] = 'Database connection failed: ' . $e->getMessage();
        }

        if (empty($errors)) {
            // --- 2. Create Config File ---
            $config_content = "<?php
define('DB_HOST', '$db_host');
define('DB_NAME', '$db_name');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');

define('ROOT_PATH', dirname(__DIR__) . '/');
define('BASE_URL', 'http://' . \$_SERVER['HTTP_HOST'] . str_replace('install.php', '', \$_SERVER['SCRIPT_NAME']));
";
            if (!file_put_contents('config/config.php', $config_content)) {
                $errors[] = 'Could not write config file. Please check file permissions.';
            } else {
                 // --- 3. Create Database Tables ---
                try {
                    // Users Table
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS `users` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `username` varchar(255) NOT NULL,
                          `password` varchar(255) NOT NULL,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                    ");

                    // Settings Table (for profile info, etc.)
                     $pdo->exec("
                        CREATE TABLE IF NOT EXISTS `settings` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `name` varchar(255) NOT NULL,
                          `value` text,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `name` (`name`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                    ");

                    // Portfolio Items Table
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS `portfolio_items` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `title` varchar(255) NOT NULL,
                          `description` text,
                          `image` varchar(255) DEFAULT NULL,
                           `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                    ");

                    // Documents Table
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS `documents` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `filename` varchar(255) NOT NULL,
                          `original_filename` varchar(255) NOT NULL,
                          `password` varchar(255) DEFAULT NULL,
                          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                    ");

                    // --- 4. Add Admin User ---
                    $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                    $stmt->execute([$admin_user, $hashed_password]);

                    $success = true;

                } catch (PDOException $e) {
                    $errors[] = 'Error creating database tables: ' . $e->getMessage();
                    // Clean up config file if table creation fails
                    unlink('config/config.php');
                }
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
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 40px auto; padding: 20px; background-color: #f4f4f4; }
        .container { background-color: #fff; padding: 30px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box; }
        .btn { display: inline-block; background-color: #007BFF; color: #fff; padding: 10px 15px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; }
        .btn:hover { background-color: #0056b3; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 3px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 3px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Portfolio Setup</h1>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>Setup failed!</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">
                <strong>Installation successful!</strong>
                <p>Your configuration file has been created and the database has been set up.</p>
                <p>You can now log in to the admin panel with the credentials you provided.</p>
                <a href="public/" class="btn">Go to your Portfolio</a>
            </div>
        <?php else: ?>
            <p>Welcome! Please provide your database details below to get started.</p>
            <form action="install.php" method="POST">
                <fieldset>
                    <legend>Database Settings</legend>
                    <div class="form-group">
                        <label for="db_host">Database Host</label>
                        <input type="text" id="db_host" name="db_host" value="127.0.0.1" required>
                    </div>
                    <div class="form-group">
                        <label for="db_name">Database Name</label>
                        <input type="text" id="db_name" name="db_name" required>
                    </div>
                    <div class="form-group">
                        <label for="db_user">Database User</label>
                        <input type="text" id="db_user" name="db_user" required>
                    </div>
                    <div class="form-group">
                        <label for="db_pass">Database Password</label>
                        <input type="password" id="db_pass" name="db_pass">
                    </div>
                </fieldset>
                <fieldset>
                     <legend>Admin Account</legend>
                     <div class="form-group">
                        <label for="admin_user">Admin Username</label>
                        <input type="text" id="admin_user" name="admin_user" value="admin" required>
                    </div>
                     <div class="form-group">
                        <label for="admin_pass">Admin Password</label>
                        <input type="password" id="admin_pass" name="admin_pass" required>
                    </div>
                </fieldset>
                <button type="submit" class="btn">Install Now</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>