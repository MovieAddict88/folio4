<?php
// Documents Logic
$message = '';
$message_type = '';

// Handle Delete
if (isset($_GET['delete_doc'])) {
    $id_to_delete = $_GET['delete_doc'];
    // First, get the file path to delete the file
    $stmt = $pdo->prepare("SELECT file_path FROM documents WHERE id = ?");
    $stmt->execute([$id_to_delete]);
    $doc = $stmt->fetch();
    if ($doc && !empty($doc['file_path']) && file_exists(PUBLIC_PATH . $doc['file_path'])) {
        unlink(PUBLIC_PATH . $doc['file_path']);
    }

    $delete_stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
    if ($delete_stmt->execute([$id_to_delete])) {
        $message = 'Document deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Failed to delete document.';
        $message_type = 'danger';
    }
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['document_name'])) {
    $document_name = $_POST['document_name'] ?? '';
    $password = $_POST['password'] ?? '';
    $file_path = '';

    if (empty($document_name) || empty($password)) {
        $message = "Document name and password are required.";
        $message_type = 'danger';
    } elseif (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] != 0) {
        $message = "File upload is required and there was an error with the upload.";
        $message_type = 'danger';
    } else {
        $target_dir = UPLOADS_PATH;
        $file_name = time() . '_' . basename($_FILES["document_file"]["name"]);
        $target_file = $target_dir . $file_name;

        // No need for image checks, just move the file
        if (move_uploaded_file($_FILES["document_file"]["tmp_name"], $target_file)) {
            $file_path = 'uploads/' . $file_name;
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO documents (document_name, file_path, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$document_name, $file_path, $hashed_password])) {
                $message = 'Document uploaded and secured successfully!';
                $message_type = 'success';
            } else {
                $message = 'Failed to save document information to the database.';
                $message_type = 'danger';
                unlink($target_file); // Clean up uploaded file
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
            $message_type = 'danger';
        }
    }
}

// Fetch all documents
$documents = $pdo->query("SELECT id, document_name, file_path, upload_date FROM documents ORDER BY upload_date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="h2 mb-4">Manage Protected Documents</h1>

<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?>">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <h5>Upload New Document</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=documents" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="document_name" class="form-label">Document Name</label>
                <input type="text" class="form-control" id="document_name" name="document_name" required>
                <div class="form-text">This name will be shown publicly (e.g., "My Resume").</div>
            </div>
            <div class="mb-3">
                <label for="document_file" class="form-label">Document File</label>
                <input class="form-control" type="file" id="document_file" name="document_file" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Set Download Password</label>
                <input type="text" class="form-control" id="password" name="password" required>
                <div class="form-text">The user will need this password to download the file.</div>
            </div>
            <button type="submit" class="btn btn-primary">Upload and Protect</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>Existing Documents</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>File Path</th>
                        <th>Upload Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No documents uploaded yet.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($doc['document_name']); ?></td>
                        <td><a href="../<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank">View File</a></td>
                        <td><?php echo $doc['upload_date']; ?></td>
                        <td>
                            <a href="index.php?page=documents&delete_doc=<?php echo $doc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This will delete the file permanently.')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>