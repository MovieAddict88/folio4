<style>
    .form-section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 30px; }
    .form-group { margin-bottom: 20px; }
    label { display: block; font-weight: 600; margin-bottom: 5px; }
    input[type="text"], input[type="email"], input[type="password"], textarea {
        width: 100%;
        max-width: 500px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    textarea {
        min-height: 120px;
        resize: vertical;
    }
    .btn-save { display: inline-block; background-color: #007bff; color: #fff; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 16px; }
    .btn-save:hover { background-color: #0056b3; }
    .current-image { max-width: 150px; border-radius: 50%; display: block; margin-top: 10px; }
</style>

<form action="<?= BASE_URL ?>admin/settings/save" method="POST" enctype="multipart/form-data">

    <div class="form-section">
        <h3>Profile Information</h3>
        <div class="form-group">
            <label for="your_name">Your Name</label>
            <input type="text" id="your_name" name="settings[your_name]" value="<?= e($settings['your_name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="your_title">Your Title / Tagline</label>
            <input type="text" id="your_title" name="settings[your_title]" value="<?= e($settings['your_title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="bio">Short Bio / About Me</label>
            <textarea id="bio" name="settings[bio]"><?= e($settings['bio'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            <?php if (!empty($settings['profile_picture'])): ?>
                <p><strong>Current Picture:</strong></p>
                <img src="<?= BASE_URL ?>public/uploads/<?= e($settings['profile_picture']) ?>" alt="Current Profile Picture" class="current-image">
            <?php endif; ?>
        </div>
    </div>

    <div class="form-section">
        <h3>Change Password</h3>
         <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password">
        </div>
    </div>

    <button type="submit" class="btn-save">Save Settings</button>
</form>