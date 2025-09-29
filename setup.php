<?php
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 1) {
        $db_host = $_POST['db_host'];
        $db_name = $_POST['db_name'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];
        $base_url = rtrim($_POST['base_url'], '/');

        // 1. Test Database Connection
        try {
            $conn = new mysqli($db_host, $db_user, $db_pass);
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
        } catch (Exception $e) {
            $error = "Database connection failed: " . $e->getMessage();
        }

        if (!$error) {
            // 2. Create config.php
            $config_content = "<?php\n\n";
            $config_content .= "define('BASEURL', '" . addslashes($base_url) . "');\n\n";
            $config_content .= "// DB\n";
            $config_content .= "define('DB_HOST', '" . addslashes($db_host) . "');\n";
            $config_content .= "define('DB_USER', '" . addslashes($db_user) . "');\n";
            $config_content .= "define('DB_PASS', '" . addslashes($db_pass) . "');\n";
            $config_content .= "define('DB_NAME', '" . addslashes($db_name) . "');\n";

            if (!file_put_contents('app/config/config.php', $config_content)) {
                $error = "Could not write to config file. Please check permissions.";
            }
        }

        if (!$error) {
            // 3. Create Database and Tables
            $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
            $conn->select_db($db_name);

            $sql = "
            CREATE TABLE IF NOT EXISTS students (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                course VARCHAR(255) NOT NULL
            );

            CREATE TABLE IF NOT EXISTS admin (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL
            );

            CREATE TABLE IF NOT EXISTS attendance (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                student_id INT(11) NOT NULL,
                attendance_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
            );";

            if (!$conn->multi_query($sql)) {
                $error = "Error creating tables: " . $conn->error;
            }
            while ($conn->next_result()) {;} // Clear results
        }

        if (!$error) {
            // 4. Create Admin User
            $admin_user = 'admin';
            $admin_pass = password_hash('admin', PASSWORD_DEFAULT);

            $stmt = $conn->prepare("SELECT id FROM admin WHERE username = ?");
            $stmt->bind_param("s", $admin_user);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) {
                 $stmt_insert = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
                 $stmt_insert->bind_param("ss", $admin_user, $admin_pass);
                 if (!$stmt_insert->execute()) {
                     $error = "Error creating admin user: " . $stmt_insert->error;
                 }
                 $stmt_insert->close();
            }
            $stmt->close();
        }

        if (!$error) {
            // Redirect to success page
            header('Location: setup.php?step=2');
            exit;
        }

        $conn->close();
    }
}

// Dynamically determine the base URL for the setup page itself
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_path = str_replace('/setup.php', '', $_SERVER['SCRIPT_NAME']);
$suggested_base_url = $protocol . $host . $script_path . '/public';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Wizard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .setup-container { max-width: 600px; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container setup-container">
        <div class="card">
            <div class="card-header">
                <h3>Student Attendance - Setup Wizard</h3>
            </div>
            <div class="card-body">
                <?php if ($step === 1): ?>
                    <h5 class="card-title">Step 1: Database Configuration</h5>
                    <p>Enter your database details below. The setup will create the configuration file and set up the necessary tables.</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="setup.php?step=1">
                        <div class="mb-3">
                            <label for="base_url" class="form-label">Base URL</label>
                            <input type="text" class="form-control" id="base_url" name="base_url" value="<?= htmlspecialchars($suggested_base_url) ?>" required>
                            <div class="form-text">This should be the public URL to your application's public folder.</div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="db_host" class="form-label">Database Host</label>
                            <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                        </div>
                        <div class="mb-3">
                            <label for="db_name" class="form-label">Database Name</label>
                            <input type="text" class="form-control" id="db_name" name="db_name" value="student_attendance" required>
                        </div>
                        <div class="mb-3">
                            <label for="db_user" class="form-label">Database User</label>
                            <input type="text" class="form-control" id="db_user" name="db_user" required>
                        </div>
                        <div class="mb-3">
                            <label for="db_pass" class="form-label">Database Password</label>
                            <input type="password" class="form-control" id="db_pass" name="db_pass">
                        </div>
                        <button type="submit" class="btn btn-primary">Complete Setup</button>
                    </form>
                <?php elseif ($step === 2): ?>
                    <h5 class="card-title">Setup Complete!</h5>
                    <div class="alert alert-success">
                        Configuration file has been created and the database is set up.
                    </div>
                    <p><strong>Default Admin Credentials:</strong></p>
                    <ul>
                        <li><strong>Username:</strong> admin</li>
                        <li><strong>Password:</strong> admin</li>
                    </ul>
                    <p class="text-danger">For security, please delete the <strong>setup.php</strong> file now.</p>
                    <a href="public/" class="btn btn-primary">Go to Homepage</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>