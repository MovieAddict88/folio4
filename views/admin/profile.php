<?php
// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $title = $_POST['title'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $current_picture = $_POST['current_picture'] ?? '';
    $profile_picture = $current_picture;

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = UPLOADS_PATH;
        $file_name = time() . '_' . basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $file_name;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
        if ($check !== false) {
            // Allow certain file formats
            if (in_array($image_file_type, ['jpg', 'png', 'jpeg', 'gif'])) {
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    $profile_picture = 'uploads/' . $file_name;
                    // Optionally, delete the old picture
                    if (!empty($current_picture) && file_exists(PUBLIC_PATH . $current_picture)) {
                        unlink(PUBLIC_PATH . $current_picture);
                    }
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                    $message_type = 'danger';
                }
            } else {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $message_type = 'danger';
            }
        } else {
            $message = "File is not an image.";
            $message_type = 'danger';
        }
    }

    if (empty($message)) {
        $stmt = $pdo->prepare("UPDATE profile SET full_name = ?, title = ?, bio = ?, profile_picture = ? WHERE id = 1");
        if ($stmt->execute([$full_name, $title, $bio, $profile_picture])) {
            $message = 'Profile updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update profile.';
            $message_type = 'danger';
        }
    }
}

// Fetch current profile data
$profile = $pdo->query("SELECT * FROM profile WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<h1 class="h2 mb-4">Manage Profile</h1>

<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?>">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5>Profile Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=profile" enctype="multipart/form-data">
             <div class="mb-3">
                <label class="form-label">Current Profile Picture</label>
                <div>
                    <img src="../<?php echo htmlspecialchars($profile['profile_picture'] ?? 'https://via.placeholder.com/150'); ?>" class="rounded" width="150" alt="Profile Picture">
                </div>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Upload New Picture</label>
                <input class="form-control" type="file" id="profile_picture" name="profile_picture">
                <input type="hidden" name="current_picture" value="<?php echo htmlspecialchars($profile['profile_picture']); ?>">
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($profile['full_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Title / Profession</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($profile['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="bio" class="form-label">Biography</label>
                <textarea class="form-control" id="bio" name="bio" rows="5" required><?php echo htmlspecialchars($profile['bio']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>