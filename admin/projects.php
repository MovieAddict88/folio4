<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/config.php';

$message = '';

// Handle form submission for adding new entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_project'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $project_link = $_POST['project_link'];
    $image = '';

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            $image = uniqid() . '.' . $filetype;
            $upload_dir = '../uploads/';
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image)) {
                $message = 'Error uploading file.';
                $image = ''; // Clear image name on failure
            }
        } else {
            $message = 'Invalid file type.';
        }
    }

    if (empty($message)) {
        $sql = "INSERT INTO projects (title, description, image, project_link) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $title, $description, $image, $project_link);
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Project added successfully!';
            } else {
                $message = 'Error adding project: ' . mysqli_error($link);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];

    // First, get the image filename to delete it from the server
    $sql_img = "SELECT image FROM projects WHERE id = ?";
    if($stmt_img = mysqli_prepare($link, $sql_img)) {
        mysqli_stmt_bind_param($stmt_img, "i", $id_to_delete);
        mysqli_stmt_execute($stmt_img);
        $result_img = mysqli_stmt_get_result($stmt_img);
        if($row_img = mysqli_fetch_assoc($result_img)) {
            if(!empty($row_img['image']) && file_exists('../uploads/' . $row_img['image'])) {
                unlink('../uploads/' . $row_img['image']);
            }
        }
        mysqli_stmt_close($stmt_img);
    }

    // Now delete the record from the database
    $sql = "DELETE FROM projects WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_to_delete);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Project deleted successfully!';
        } else {
            $message = 'Error deleting project: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all projects
$sql = "SELECT id, title, description, image, project_link FROM projects ORDER BY id DESC";
$project_entries = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Projects</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 800px; margin: 0 auto; }
        nav { background: #f4f4f4; padding: 1em; }
        nav a { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em;}
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
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
        <h1>Manage Projects</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="projects.php" method="post" enctype="multipart/form-data">
            <h2>Add New Project</h2>
            <input type="hidden" name="add_project" value="1">
            <div>
                <label for="title">Title:</label><br>
                <input type="text" name="title" id="title" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="description">Description:</label><br>
                <textarea name="description" id="description"></textarea>
            </div>
            <br>
             <div>
                <label for="project_link">Project Link:</label><br>
                <input type="url" name="project_link" id="project_link" style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="image">Image:</label><br>
                <input type="file" name="image" id="image">
            </div>
            <br>
            <div>
                <input type="submit" value="Add Project">
            </div>
        </form>

        <h2>Existing Projects</h2>
        <table>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($project_entries)): ?>
            <tr>
                <td>
                    <?php if ($row['image']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" width="100">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td>
                    <a href="projects_edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                    <a href="projects.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>