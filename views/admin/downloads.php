<?php
// Ensure the user is logged in and functions are available
if (!isset($_SESSION['user_id'])) {
    exit('Access Denied');
}
require_once __DIR__ . '/../../app/functions.php';

$pdo = pdo();
$message = '';

// Handle POST requests (upload, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // UPLOAD NEW FILE
    if ($action === 'upload') {
        $password = $_POST['password'];

        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK && !empty($password)) {
            $upload_dir = __DIR__ . '/../../public/uploads/downloads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_name = basename($_FILES['document']['name']);
            $file_path = 'public/uploads/downloads/' . time() . '_' . $file_name;

            if (move_uploaded_file($_FILES['document']['tmp_name'], __DIR__ . '/../../' . $file_path)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO downloads (file_name, file_path, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$file_name, $file_path, $password_hash]);

                $message = '<div style="color: green; margin-bottom: 15px;">File uploaded and password set successfully!</div>';
            } else {
                $message = '<div style="color: red; margin-bottom: 15px;">Failed to move uploaded file.</div>';
            }
        } else {
            $message = '<div style="color: red; margin-bottom: 15px;">Please select a file and enter a password.</div>';
        }
    }

    // DELETE FILE
    if ($action === 'delete') {
        $id = $_POST['id'] ?? null;
        if ($id) {
            // First, get the file path to delete the physical file
            $stmt = $pdo->prepare("SELECT file_path FROM downloads WHERE id = ?");
            $stmt->execute([$id]);
            $file_path = $stmt->fetchColumn();

            if ($file_path && file_exists(__DIR__ . '/../../' . $file_path)) {
                unlink(__DIR__ . '/../../' . $file_path);
            }

            // Then, delete the database record
            $stmt = $pdo->prepare("DELETE FROM downloads WHERE id = ?");
            $stmt->execute([$id]);
            $message = '<div style="color: green; margin-bottom: 15px;">File deleted successfully!</div>';
        }
    }
}

// Fetch all downloadable files
$items = $pdo->query("SELECT * FROM downloads ORDER BY id DESC")->fetchAll();
?>

<?php echo $message; ?>

<!-- Form for Uploading a New File -->
<h3>Upload New Protected Document</h3>
<form action="admin.php?page=downloads" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="upload">
    <div class="form-group">
        <label for="document">Select Document</label>
        <input type="file" id="document" name="document" required>
    </div>
    <div class="form-group">
        <label for="password">Set Download Password</label>
        <input type="text" id="password" name="password" required>
        <small>The user will need this exact password to download the file.</small>
    </div>
    <button type="submit" class="btn">Upload and Protect</button>
</form>

<hr style="margin: 40px 0;">

<!-- Table of Existing Files -->
<h3>Manage Protected Downloads</h3>
<table>
    <thead>
        <tr>
            <th>File Name</th>
            <th>Download Count</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="3" style="text-align: center;">No protected files found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo e($item['file_name']); ?></td>
                    <td><?php echo e($item['download_count']); ?></td>
                    <td class="actions">
                        <form action="admin.php?page=downloads" method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this file? This cannot be undone.');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn-danger" style="background:none; border:none; color:red; cursor:pointer; padding:0; font-size: inherit;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>