<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/config.php';

$message = '';

// Function to generate a random password
function generate_password($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

// Handle form submission for adding new document
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_document'])) {
    $filename_display = $_POST['filename'];
    $generated_password = generate_password();
    $hashed_password = password_hash($generated_password, PASSWORD_DEFAULT);
    $filepath = '';

    // Handle file upload
    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $allowed_ext = ['pdf', 'doc', 'docx', 'zip', 'rar'];
        $file_info = pathinfo($_FILES['document']['name']);
        $ext = strtolower($file_info['extension']);

        if (in_array($ext, $allowed_ext)) {
            $filepath = uniqid() . '.' . $ext;
            $upload_dir = '../uploads/';
            if (!move_uploaded_file($_FILES['document']['tmp_name'], $upload_dir . $filepath)) {
                $message = 'Error uploading file.';
                $filepath = ''; // Clear filepath on failure
            }
        } else {
            $message = 'Invalid file type. Allowed types: ' . implode(', ', $allowed_ext);
        }
    } else {
        $message = 'Please select a file to upload.';
    }

    if (empty($message) && !empty($filepath)) {
        $sql = "INSERT INTO documents (filename, filepath, password) VALUES (?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $filename_display, $filepath, $hashed_password);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Document uploaded successfully! The password is: <strong>' . $generated_password . '</strong> (Please save this password, it cannot be recovered).';
            } else {
                $message = 'Error saving document to database: ' . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];

    // First, get the filepath to delete the file from the server
    $sql_file = "SELECT filepath FROM documents WHERE id = ?";
    if($stmt_file = mysqli_prepare($link, $sql_file)) {
        mysqli_stmt_bind_param($stmt_file, "i", $id_to_delete);
        mysqli_stmt_execute($stmt_file);
        $result_file = mysqli_stmt_get_result($stmt_file);
        if($row_file = mysqli_fetch_assoc($result_file)) {
            if(!empty($row_file['filepath']) && file_exists('../uploads/' . $row_file['filepath'])) {
                unlink('../uploads/' . $row_file['filepath']);
            }
        }
        mysqli_stmt_close($stmt_file);
    }

    // Now delete the record from the database
    $sql = "DELETE FROM documents WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_to_delete);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Document deleted successfully!';
        } else {
            $message = 'Error deleting document: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all documents
$sql = "SELECT id, filename FROM documents ORDER BY id DESC";
$document_entries = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Documents</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 800px; margin: 0 auto; }
        nav { background: #f4f4f4; padding: 1em; }
        nav a { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em;}
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .message { padding: 1em; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 1em; }
        .error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;}
        form { border: 1px solid #ddd; padding: 1em; margin-top: 1em; }
    </style>
</head>
<body>
    <div class="container">
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="about.php">About</a>
            <a href="education.php">Education</a>
            <a href="experience.php">Experience</a>
            <a href="skills.php">Skills</a>
            <a href="projects.php">Projects</a>
            <a href="documents.php">Documents</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php" style="float:right;">Logout</a>
        </nav>
        <h1>Manage Protected Documents</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="documents.php" method="post" enctype="multipart/form-data">
            <h2>Upload New Document</h2>
            <p>A secure password will be automatically generated for the document.</p>
            <input type="hidden" name="add_document" value="1">
            <div>
                <label for="filename">Display Name (e.g., "My Resume"):</label><br>
                <input type="text" name="filename" id="filename" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="document">Document File:</label><br>
                <input type="file" name="document" id="document" required>
            </div>
            <br>
            <div>
                <input type="submit" value="Upload and Generate Password">
            </div>
        </form>

        <h2>Existing Documents</h2>
        <table>
            <tr>
                <th>Display Name</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($document_entries)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['filename']); ?></td>
                <td>
                    <a href="documents.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure? This will permanently delete the file and its password.')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>