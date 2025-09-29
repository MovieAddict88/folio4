<?php
session_start();

$page_title = "Project Installer";
$style = "
    body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; color: #333; }
    .container { background-color: #fff; max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h1 { color: #1C2B4A; text-align: center; }
    .form-group { margin-bottom: 20px; }
    label { display: block; margin-bottom: 8px; font-weight: 500; }
    input[type='text'], input[type='password'], input[type='email'] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
    .btn { background-color: #1C2B4A; color: #fff; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
    .btn:hover { background-color: #E2B714; color: #1C2B4A; }
    .error { color: #D8000C; background-color: #FFD2D2; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;}
    .success { color: #4F8A10; background-color: #DFF2BF; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;}
    .info { color: #00529B; background-color: #BDE5F8; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;}
";

// Redirect if already installed
if (file_exists('config/config.php')) {
    $content = "<div class='info'><strong>Already Installed!</strong><p>The configuration file already exists. If you want to re-install, please delete the <code>config/config.php</code> file.</p><p><a href='public/'>Go to Homepage</a></p></div>";
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- FORM SUBMITTED, PROCESS INSTALLATION ---

    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];

    // 1. CREATE CONFIG FILE
    $config_content = "<?php
// Database Configuration
define('DB_HOST', '{$db_host}');
define('DB_NAME', '{$db_name}');
define('DB_USER', '{$db_user}');
define('DB_PASS', '{$db_pass}');

// Site Configuration
define('SITE_URL', 'http://' . \$_SERVER['HTTP_HOST']);
define('APP_PATH', __DIR__);

// Password for downloads
define('DOWNLOAD_ENCRYPTION_KEY', 'your-secret-encryption-key'); // You should change this
?>";

    if (!is_dir('config')) {
        mkdir('config', 0755, true);
    }

    if (file_put_contents('config/config.php', $config_content)) {
        // Set permissions as requested
        chmod('config/config.php', 0755);

        // 2. CONNECT TO DATABASE AND CREATE TABLES
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL Schema
            $sql = file_get_contents('schema.sql'); // We will create this file next
            $pdo->exec($sql);

            // 3. CREATE ADMIN USER
            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$admin_email, $hashed_password]);

            // Installation successful
            $content = "<div class='success'><strong>Installation Complete!</strong><p>Your portfolio has been set up successfully.</p><p><strong>Admin Email:</strong> {$admin_email}<br><strong>Password:</strong> Your chosen password</p><p>Please delete the <code>install.php</code> and <code>schema.sql</code> files for security.</p><p><a href='public/admin.php'>Go to Admin Login</a></p></div>";

        } catch (PDOException $e) {
            // Clean up config file on error
            unlink('config/config.php');
            $content = "<div class='error'><strong>Database Error!</strong><p>Could not connect to the database or create tables. Please check your credentials and try again.</p><p><strong>Error:</strong> " . $e->getMessage() . "</p><a href='install.php'>Try Again</a></div>";
        }
    } else {
        $content = "<div class='error'><strong>Configuration Error!</strong><p>Could not write the configuration file. Please check folder permissions.</p><a href='install.php'>Try Again</a></div>";
    }

} else {
    // --- SHOW INSTALLATION FORM ---
    $content = "
        <form action='install.php' method='post'>
            <h1>Database Setup</h1>
            <p style='text-align:center;'>Enter your database credentials. These will be used to create your portfolio's tables.</p>
            <div class='form-group'>
                <label for='db_host'>Database Host</label>
                <input type='text' id='db_host' name='db_host' value='localhost' required>
            </div>
            <div class='form-group'>
                <label for='db_name'>Database Name</label>
                <input type='text' id='db_name' name='db_name' required>
            </div>
            <div class='form-group'>
                <label for='db_user'>Database User</label>
                <input type='text' id='db_user' name='db_user' required>
            </div>
            <div class='form-group'>
                <label for='db_pass'>Database Password</label>
                <input type='password' id='db_pass' name='db_pass'>
            </div>
            <hr style='margin: 30px 0; border: 1px solid #eee;'>
            <h1>Admin Account Setup</h1>
             <p style='text-align:center;'>Create your administrator account.</p>
            <div class='form-group'>
                <label for='admin_email'>Admin Email</label>
                <input type='email' id='admin_email' name='admin_email' required>
            </div>
            <div class='form-group'>
                <label for='admin_password'>Admin Password</label>
                <input type='password' id='admin_password' name='admin_password' required>
            </div>
            <button type='submit' class='btn'>Install Now</button>
        </form>
    ";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style><?php echo $style; ?></style>
</head>
<body>
    <div class="container">
        <h1>Portfolio Installer</h1>
        <?php echo $content; ?>
    </div>
</body>
</html>