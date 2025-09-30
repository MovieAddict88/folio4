<section id="about">
    <div class="container">
        <h2>About Me</h2>
        <div class="about-content">
            <?php if ($about && !empty($about['profile_picture'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($about['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
            <?php endif; ?>
            <p><?php echo $about ? nl2br(htmlspecialchars($about['content'])) : 'Welcome to my portfolio. Please add content in the admin panel.'; ?></p>
        </div>
    </div>
</section>