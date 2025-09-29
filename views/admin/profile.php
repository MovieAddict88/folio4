<?php
// Ensure the user is logged in and functions are available
if (!isset($_SESSION['user_id'])) {
    exit('Access Denied');
}
require_once __DIR__ . '/../../app/functions.php';

$pdo = pdo();
$message = '';

// Handle form submission for updating profile settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A whitelist of settings that can be updated from this form
    $allowed_settings = [
        'hero_title', 'hero_tagline', 'about_me', 'education_philosophy',
        'contact_email', 'contact_address', 'social_facebook', 'social_tiktok',
        'social_youtube', 'social_instagram'
    ];

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE profile SET setting_value = ? WHERE setting_key = ?");

        foreach ($allowed_settings as $key) {
            if (isset($_POST[$key])) {
                $stmt->execute([$_POST[$key], $key]);
            }
        }

        // Handle file upload for hero image
        if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $filename = 'profile_' . time() . '_' . basename($_FILES['hero_image']['name']);
            $target_file = $upload_dir . $filename;

            // Basic validation for image type
            $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            if (in_array($image_type, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target_file)) {
                    // Update database with new image path, relative to public dir
                    $image_path = 'uploads/' . $filename;
                    $stmt->execute([$image_path, 'hero_image']);
                }
            }
        }

        $pdo->commit();
        $message = '<div style="color: green; margin-bottom: 15px;">Profile updated successfully!</div>';
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = '<div style="color: red; margin-bottom: 15px;">Error updating profile: ' . $e->getMessage() . '</div>';
    }
}

// Fetch all current profile settings from the database
$stmt = $pdo->query("SELECT setting_key, setting_value FROM profile");
$settings_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Helper function to safely get a setting value
function get_setting($key) {
    global $settings_raw;
    return e($settings_raw[$key] ?? '');
}
?>

<?php echo $message; ?>

<form action="admin.php?page=profile" method="post" enctype="multipart/form-data">

    <h3>Hero Section</h3>
    <div class="form-group">
        <label for="hero_title">Hero Title (Your Name)</label>
        <input type="text" id="hero_title" name="hero_title" value="<?php echo get_setting('hero_title'); ?>">
    </div>
    <div class="form-group">
        <label for="hero_tagline">Hero Tagline</label>
        <input type="text" id="hero_tagline" name="hero_tagline" value="<?php echo get_setting('hero_tagline'); ?>">
    </div>
    <div class="form-group">
        <label for="hero_image">Hero Image (Profile Picture)</label>
        <input type="file" id="hero_image" name="hero_image">
        <?php
        $current_image = get_setting('hero_image');
        if ($current_image && file_exists(__DIR__ . '/../../public/' . $current_image)): ?>
            <div style="margin-top: 10px;">
                <img src="<?php echo '../public/' . $current_image; ?>" alt="Current Profile Image" style="max-width: 150px; height: auto; border-radius: 5px;">
                <p><small>Current image: <?php echo $current_image; ?></small></p>
            </div>
        <?php else: ?>
            <p><small>No image uploaded.</small></p>
        <?php endif; ?>
    </div>

    <hr style="margin: 30px 0;">

    <h3>About Section</h3>
    <div class="form-group">
        <label for="about_me">About Me</label>
        <textarea id="about_me" name="about_me"><?php echo get_setting('about_me'); ?></textarea>
    </div>
    <div class="form-group">
        <label for="education_philosophy">Education/Work Philosophy</label>
        <textarea id="education_philosophy" name="education_philosophy"><?php echo get_setting('education_philosophy'); ?></textarea>
    </div>

    <hr style="margin: 30px 0;">

    <h3>Contact & Social Media</h3>
    <div class="form-group">
        <label for="contact_email">Contact Email</label>
        <input type="email" id="contact_email" name="contact_email" value="<?php echo get_setting('contact_email'); ?>">
    </div>
    <div class="form-group">
        <label for="contact_address">Contact Address</label>
        <input type="text" id="contact_address" name="contact_address" value="<?php echo get_setting('contact_address'); ?>">
    </div>
    <div class="form-group">
        <label for="social_facebook">Facebook URL</label>
        <input type="text" id="social_facebook" name="social_facebook" value="<?php echo get_setting('social_facebook'); ?>">
    </div>
    <div class="form-group">
        <label for="social_tiktok">TikTok URL</label>
        <input type="text" id="social_tiktok" name="social_tiktok" value="<?php echo get_setting('social_tiktok'); ?>">
    </div>
    <div class="form-group">
        <label for="social_youtube">YouTube URL</label>
        <input type="text" id="social_youtube" name="social_youtube" value="<?php echo get_setting('social_youtube'); ?>">
    </div>
    <div class="form-group">
        <label for="social_instagram">Instagram URL</label>
        <input type="text" id="social_instagram" name="social_instagram" value="<?php echo get_setting('social_instagram'); ?>">
    </div>

    <button type="submit" class="btn">Save Settings</button>
</form>