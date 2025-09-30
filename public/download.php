<?php
require_once '../src/init.php';

// If config doesn't exist, redirect to installer
if (!file_exists(CONFIG_FILE)) {
    header('Location: ../install.php');
    exit;
}

$error = '';
$doc = null;
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid document request.");
}

// Fetch document details
$stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
$stmt->execute([$id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doc) {
    die("Document not found.");
}

// Handle password submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (password_verify($password, $doc['password'])) {
        $file_path = PUBLIC_PATH . $doc['file_path'];

        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($doc['document_name']) . '.' . pathinfo($file_path, PATHINFO_EXTENSION) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            flush(); // Flush system output buffer
            readfile($file_path);
            exit;
        } else {
            $error = 'File not found on server. Please contact the administrator.';
        }
    } else {
        $error = 'Incorrect password. Please try again.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .download-form {
            width: 100%;
            max-width: 450px;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="download-form text-center">
        <h2 class="mb-3">Password Required</h2>
        <p>You are trying to download: <strong><?php echo htmlspecialchars($doc['document_name']); ?></strong></p>
        <p class="text-muted">Please enter the password provided to you to start the download.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="download.php?id=<?php echo $id; ?>">
            <div class="mb-3">
                <label for="password" class="form-label visually-hidden">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Download File</button>
             <a href="index.php#documents" class="btn btn-link mt-2">Cancel</a>
        </form>
    </div>
</body>
</html>