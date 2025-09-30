<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}
require_once '../includes/config.php';

$message = '';

// Handle form submission for adding new entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_experience'])) {
    $job_title = $_POST['job_title'];
    $company = $_POST['company'];
    $start_date = $_POST['start_date'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;
    $description = $_POST['description'];

    $sql = "INSERT INTO experience (job_title, company, start_date, end_date, description) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssss", $job_title, $company, $start_date, $end_date, $description);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Experience entry added successfully!';
        } else {
            $message = 'Error adding entry: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    $sql = "DELETE FROM experience WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id_to_delete);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Experience entry deleted successfully!';
        } else {
            $message = 'Error deleting entry: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all experience entries
$sql = "SELECT id, job_title, company, start_date, end_date, description FROM experience ORDER BY start_date DESC";
$experience_entries = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Experience</title>
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
        <h1>Manage Experience</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="experience.php" method="post">
            <h2>Add New Experience Entry</h2>
            <input type="hidden" name="add_experience" value="1">
            <div>
                <label for="job_title">Job Title:</label><br>
                <input type="text" name="job_title" id="job_title" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="company">Company:</label><br>
                <input type="text" name="company" id="company" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="start_date">Start Date:</label><br>
                <input type="date" name="start_date" id="start_date" required>
            </div>
            <br>
            <div>
                <label for="end_date">End Date (leave blank if current):</label><br>
                <input type="date" name="end_date" id="end_date">
            </div>
            <br>
            <div>
                <label for="description">Description:</label><br>
                <textarea name="description" id="description"></textarea>
            </div>
            <br>
            <div>
                <input type="submit" value="Add Entry">
            </div>
        </form>

        <h2>Existing Entries</h2>
        <table>
            <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Dates</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($experience_entries)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                <td><?php echo htmlspecialchars($row['company']); ?></td>
                <td><?php echo $row['start_date'] . ' to ' . ($row['end_date'] ? $row['end_date'] : 'Present'); ?></td>
                <td>
                    <a href="experience_edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                    <a href="experience.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>