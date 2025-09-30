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
    header('Location: experience.php');
    exit;
}

// Handle form submission for updating an entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_experience'])) {
    $job_title = $_POST['job_title'];
    $company = $_POST['company'];
    $start_date = $_POST['start_date'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;
    $description = $_POST['description'];

    $sql = "UPDATE experience SET job_title = ?, company = ?, start_date = ?, end_date = ?, description = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssssi", $job_title, $company, $start_date, $end_date, $description, $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Experience entry updated successfully!';
        } else {
            $message = 'Error updating entry: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch the specific experience entry
$sql = "SELECT job_title, company, start_date, end_date, description FROM experience WHERE id = ?";
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
    <title>Admin - Edit Experience</title>
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
        <h1>Edit Experience Entry</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="experience_edit.php?id=<?php echo $id; ?>" method="post">
            <input type="hidden" name="update_experience" value="1">
            <div>
                <label for="job_title">Job Title:</label><br>
                <input type="text" name="job_title" id="job_title" value="<?php echo htmlspecialchars($entry['job_title']); ?>" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="company">Company:</label><br>
                <input type="text" name="company" id="company" value="<?php echo htmlspecialchars($entry['company']); ?>" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="start_date">Start Date:</label><br>
                <input type="date" name="start_date" id="start_date" value="<?php echo $entry['start_date']; ?>" required>
            </div>
            <br>
            <div>
                <label for="end_date">End Date (leave blank if current):</label><br>
                <input type="date" name="end_date" id="end_date" value="<?php echo $entry['end_date']; ?>">
            </div>
            <br>
            <div>
                <label for="description">Description:</label><br>
                <textarea name="description" id="description"><?php echo htmlspecialchars($entry['description']); ?></textarea>
            </div>
            <br>
            <div>
                <input type="submit" value="Update Entry">
                <a href="experience.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>