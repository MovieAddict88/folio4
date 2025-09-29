<?php
session_start();
require_once '../config/config.php';
require_once '../app/functions.php';

$pdo = pdo();
$id = $_GET['id'] ?? null;
$error = '';
$file_info = null;

if (!$id) {
    die("No file specified.");
}

// Get file info from DB
$stmt = $pdo->prepare("SELECT * FROM downloads WHERE id = ?");
$stmt->execute([$id]);
$file_info = $stmt->fetch();

if (!$file_info) {
    die("File not found.");
}

// Handle password submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    if (password_verify($password, $file_info['password_hash'])) {
        // Password is correct, increment download count and serve the file
        $stmt = $pdo->prepare("UPDATE downloads SET download_count = download_count + 1 WHERE id = ?");
        $stmt->execute([$id]);

        $file_path = __DIR__ . '/../' . $file_info['file_path'];
        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_info['file_name']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        } else {
            die("File not found on server.");
        }
    } else {
        $error = "Invalid password. Please try again.";
    }
}

// If we are here, it means the user needs to enter a password.
$page_title = "Download " . e($file_info['file_name']);
$style = "
    body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
    .container { background-color: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 450px; text-align: center; }
    h1 { color: #1C2B4A; margin-bottom: 15px; }
    p { color: #6b7280; margin-bottom: 25px; }
    .form-group { margin-bottom: 20px; }
    input[type='password'] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; text-align: center; }
    .btn { background-color: #1C2B4A; color: #fff; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
    .btn:hover { background-color: #E2B714; color: #1C2B4A; }
    .error { color: #D8000C; background-color: #FFD2D2; padding: 10px; border-radius: 4px; margin-top: 20px; }
";
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
        <h1>Password Required</h1>
        <p>To download the file "<strong><?php echo e($file_info['file_name']); ?></strong>", please enter the password provided.</p>
        <form action="download.php?id=<?php echo $id; ?>" method="post">
            <div class="form-group">
                <input type="password" name="password" placeholder="Enter Password" required autofocus>
            </div>
            <button type="submit" class="btn">Verify & Download</button>
        </form>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <p style="margin-top: 20px; font-size: 0.9em;"><a href="index.php#downloads">Go back to portfolio</a></p>
    </div>
</body>
</html>