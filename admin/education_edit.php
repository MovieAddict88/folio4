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
    header('Location: education.php');
    exit;
}

// Handle form submission for updating an entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_education'])) {
    $degree = $_POST['degree'];
    $institution = $_POST['institution'];
    $start_year = $_POST['start_year'];
    $end_year = $_POST['end_year'];

    $sql = "UPDATE education SET degree = ?, institution = ?, start_year = ?, end_year = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssiii", $degree, $institution, $start_year, $end_year, $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Education entry updated successfully!';
        } else {
            $message = 'Error updating entry: ' . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch the specific education entry
$sql = "SELECT degree, institution, start_year, end_year FROM education WHERE id = ?";
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
    <title>Admin - Edit Education</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 800px; margin: 0 auto; }
        nav { background: #f4f4f4; padding: 1em; }
        nav a { margin-right: 15px; }
        .message { padding: 1em; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 1em; }
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
        <h1>Edit Education Entry</h1>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="education_edit.php?id=<?php echo $id; ?>" method="post">
             <input type="hidden" name="update_education" value="1">
            <div>
                <label for="degree">Degree/Certificate:</label><br>
                <input type="text" name="degree" id="degree" value="<?php echo htmlspecialchars($entry['degree']); ?>" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="institution">Institution:</label><br>
                <input type="text" name="institution" id="institution" value="<?php echo htmlspecialchars($entry['institution']); ?>" required style="width: 95%;">
            </div>
            <br>
            <div>
                <label for="start_year">Start Year:</label><br>
                <input type="number" name="start_year" id="start_year" value="<?php echo $entry['start_year']; ?>" required>
            </div>
            <br>
            <div>
                <label for="end_year">End Year:</label><br>
                <input type="number" name="end_year" id="end_year" value="<?php echo $entry['end_year']; ?>" required>
            </div>
            <br>
            <div>
                <input type="submit" value="Update Entry">
                <a href="education.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>