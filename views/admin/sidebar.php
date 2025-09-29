<div class="sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="admin.php?page=dashboard" class="<?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="admin.php?page=profile" class="<?php echo ($page === 'profile') ? 'active' : ''; ?>">
                Profile & Settings
            </a>
        </li>
        <li>
            <a href="admin.php?page=education" class="<?php echo ($page === 'education') ? 'active' : ''; ?>">
                Education
            </a>
        </li>
        <li>
            <a href="admin.php?page=experience" class="<?php echo ($page === 'experience') ? 'active' : ''; ?>">
                Experience
            </a>
        </li>
        <li>
            <a href="admin.php?page=skills" class="<?php echo ($page === 'skills') ? 'active' : ''; ?>">
                Skills
            </a>
        </li>
        <li>
            <a href="admin.php?page=projects" class="<?php echo ($page === 'projects') ? 'active' : ''; ?>">
                Projects
            </a>
        </li>
        <li>
            <a href="admin.php?page=testimonials" class="<?php echo ($page === 'testimonials') ? 'active' : ''; ?>">
                Testimonials
            </a>
        </li>
        <li>
            <a href="admin.php?page=downloads" class="<?php echo ($page === 'downloads') ? 'active' : ''; ?>">
                Downloads
            </a>
        </li>
        <li>
            <a href="admin.php?page=messages" class="<?php echo ($page === 'messages') ? 'active' : ''; ?>">
                Contact Messages
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="logout.php">Logout</a> | <a href="index.php" target="_blank">View Site</a>
    </div>
</div>