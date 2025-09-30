<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/config.php';

$message = '';
$entry = null;
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: projects.php');
    exit;
}

// Handle form submission for updating an entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_project'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $project_link = $_POST['project_link'];
    $current_image = $_POST['current_image'];
    $image = $current_image;

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $image = uniqid() . '.' . $filetype;
            $upload_dir = '../uploads/';
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image)) {
                // Delete old image if a new one is uploaded
                if (!empty($current_image) && file_exists($upload_dir . $current_image)) {
                    unlink($upload_dir . $current_image);
                }
            } else {
                $message = 'Error uploading file.';
                $image = $current_image; // Revert to old image on failure
            }
        } else {
            $message = 'Invalid file type.';
        }
    }

    if (empty($message)) {
        $sql = "UPDATE projects SET title = ?, description = ?, image = ?, project_link = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $image, $project_link, $id);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Project updated successfully!';
            } else {
                $message = 'Error updating project: ' . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Fetch the specific project entry
$sql = "SELECT title, description, image, project_link FROM projects WHERE id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $entry = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$entry) {
    echo "Entry not found.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Edit Project</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 800px; margin: 0 auto; }
        nav { background: #f4f4f4; padding: 1em; }
        nav a { margin-right: 15px; }
        .message { padding: 1em; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 1em; }
        form { border: 1px solid #ddd; padding: 1em; margin-top: 1em; }
        textarea { width: 95%; height: 100px; }
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
        <h1>Edit Project</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="projects_edit.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="update_project" value="1">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($entry['image']); ?>">
            <div>
                <label for="title">Title:</label><br>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($entry['title']); ?>" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="description">Description:</label><br>
                <textarea name="description" id="description"><?php echo htmlspecialchars($entry['description']); ?></textarea>
            </div>
            <br>
            <div>
                <label for="project_link">Project Link:</label><br>
                <input type="url" name="project_link" id="project_link" value="<?php echo htmlspecialchars($entry['project_link']); ?>" style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="image">Image:</label><br>
                <?php if ($entry['image']): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($entry['image']); ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" width="150"><br>
                    <small>Upload a new image to replace the current one.</small><br>
                <?php endif; ?>
                <input type="file" name="image" id="image">
            </div>
            <br>
            <div>
                <input type="submit" value="Update Project">
                <a href="projects.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>