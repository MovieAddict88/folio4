<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Configuration & Helpers ---

define('MIN_PHP_VERSION', '7.4.0');
define('REQUIRED_EXTENSIONS', ['pdo_mysql', 'json']);
define('CONFIG_FILE', 'src/config.php');
define('SQL_SCHEMA_FILE', 'database/schema.sql');

function check_php_version() {
    return version_compare(PHP_VERSION, MIN_PHP_VERSION, '>=');
}

function check_extensions() {
    $missing = [];
    foreach (REQUIRED_EXTENSIONS as $ext) {
        if (!extension_loaded($ext)) {
            $missing[] = $ext;
        }
    }
    return $missing;
}

function write_config_file($host, $dbname, $user, $pass) {
    $config_content = "<?php
define('DB_HOST', '{$host}');
define('DB_NAME', '{$dbname}');
define('DB_USER', '{$user}');
define('DB_PASS', '{$pass}');
define('DB_CHARSET', 'utf8mb4');

try {
    \$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException \$e) {
    die('Database connection failed: ' . \$e->getMessage());
}
";
    return file_put_contents(CONFIG_FILE, $config_content);
}

function test_db_connection($host, $dbname, $user, $pass) {
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function setup_database($pdo) {
    try {
        $sql = file_get_contents(SQL_SCHEMA_FILE);
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

// --- Installation Steps ---

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$errors = [];

// Prevent re-installation
if (file_exists(CONFIG_FILE)) {
    $step = 'complete';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) { // Database Setup
        $db_host = $_POST['db_host'] ?? 'localhost';
        $db_name = $_POST['db_name'] ?? '';
        $db_user = $_POST['db_user'] ?? '';
        $db_pass = $_POST['db_pass'] ?? '';

        if (empty($db_name) || empty($db_user)) {
            $errors[] = 'Database Name and Username are required.';
        } else {
            $conn_test = test_db_connection($db_host, $db_name, $db_user, $db_pass);
            if ($conn_test !== true) {
                $errors[] = "Database connection failed: " . $conn_test;
            } else {
                if (!write_config_file($db_host, $db_name, $db_user, $db_pass)) {
                     $errors[] = 'Could not write config file. Please check file permissions for the `src` directory.';
                } else {
                    // Create tables
                    require_once CONFIG_FILE;
                    $db_setup_result = setup_database($pdo);
                    if ($db_setup_result !== true) {
                        $errors[] = "Database setup failed: " . $db_setup_result;
                        unlink(CONFIG_FILE); // Clean up on failure
                    } else {
                        header('Location: install.php?step=3');
                        exit;
                    }
                }
            }
        }
    }
    if ($step === 3) { // Admin Account
        $admin_user = $_POST['admin_user'] ?? '';
        $admin_pass = $_POST['admin_pass'] ?? '';

        if (empty($admin_user) || empty($admin_pass)) {
            $errors[] = 'Admin username and password cannot be empty.';
        } else {
            require_once CONFIG_FILE;
            $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
            if ($stmt->execute([$admin_user, $hashed_password])) {
                // Installation complete, self-destruct
                        // unlink(__FILE__); // Temporarily disable for testing
                        header('Location: public/admin/login.php?installed=true');
                exit;
            } else {
                $errors[] = "Failed to create admin user.";
            }
        }
    }
}

// --- Views ---

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .installer-container { max-width: 600px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .list-group-item.active { z-index: 2; color: #fff; background-color: #0d6efd; border-color: #0d6efd; }
        .list-group-item.disabled { color: #6c757d; background-color: #e9ecef; border-color: #dee2e6; }
        .alert { margin-top: 20px; }
    </style>
</head>
<body>
<div class="installer-container">
    <h2 class="text-center mb-4">Portfolio Installer</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Error!</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($step === 1): // Requirements Check ?>
        <h3>Step 1: Pre-installation Check</h3>
        <ul class="list-group mt-3">
            <?php
            $php_ok = check_php_version();
            $ext_missing = check_extensions();
            $all_ok = $php_ok && empty($ext_missing);
            ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                PHP Version (<?php echo MIN_PHP_VERSION; ?>+ required)
                <span class="badge bg-<?php echo $php_ok ? 'success' : 'danger'; ?> rounded-pill"><?php echo PHP_VERSION; ?></span>
            </li>
            <?php foreach (REQUIRED_EXTENSIONS as $ext): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php echo $ext; ?> extension
                <span class="badge bg-<?php echo in_array($ext, $ext_missing) ? 'danger' : 'success'; ?> rounded-pill"><?php echo in_array($ext, $ext_missing) ? 'Missing' : 'Installed'; ?></span>
            </li>
            <?php endforeach; ?>
             <li class="list-group-item d-flex justify-content-between align-items-center">
                Config File Writable (`src/`)
                <span class="badge bg-<?php echo is_writable('src/') ? 'success' : 'danger'; ?> rounded-pill"><?php echo is_writable('src/') ? 'Writable' : 'Not Writable'; ?></span>
            </li>
        </ul>
        <?php if ($all_ok && is_writable('src/')): ?>
            <a href="?step=2" class="btn btn-primary mt-4 w-100">Next Step</a>
        <?php else: ?>
            <div class="alert alert-danger mt-4">Please fix the issues above before proceeding.</div>
        <?php endif; ?>

    <?php elseif ($step === 2): // Database Configuration ?>
        <h3>Step 2: Database Setup</h3>
        <p>Please provide your database connection details.</p>
        <form method="POST" action="?step=2">
            <div class="mb-3">
                <label for="db_host" class="form-label">Database Host</label>
                <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
            </div>
            <div class="mb-3">
                <label for="db_name" class="form-label">Database Name</label>
                <input type="text" class="form-control" id="db_name" name="db_name" required>
            </div>
            <div class="mb-3">
                <label for="db_user" class="form-label">Database Username</label>
                <input type="text" class="form-control" id="db_user" name="db_user" required>
            </div>
            <div class="mb-3">
                <label for="db_pass" class="form-label">Database Password</label>
                <input type="password" class="form-control" id="db_pass" name="db_pass">
            </div>
            <button type="submit" class="btn btn-primary w-100">Connect & Create Tables</button>
        </form>

    <?php elseif ($step === 3): // Create Admin User ?>
        <h3>Step 3: Create Admin Account</h3>
        <p>The database is set up. Now, create your admin account.</p>
        <form method="POST" action="?step=3">
            <div class="mb-3">
                <label for="admin_user" class="form-label">Admin Username</label>
                <input type="text" class="form-control" id="admin_user" name="admin_user" required>
            </div>
            <div class="mb-3">
                <label for="admin_pass" class="form-label">Admin Password</label>
                <input type="password" class="form-control" id="admin_pass" name="admin_pass" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Create Admin & Finish</button>
        </form>

    <?php elseif ($step === 'complete'): ?>
         <h3>Installation Complete!</h3>
         <div class="alert alert-warning">
             <p>The configuration file `src/config.php` already exists.</p>
             <p>For security reasons, please <strong>delete the `install.php` file</strong> from your server.</p>
         </div>
         <a href="public/" class="btn btn-success w-100">Go to Homepage</a>
    <?php endif; ?>

</div>
</body>
</html>