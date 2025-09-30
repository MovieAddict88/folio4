<section id="projects">
    <div class="container">
        <h2>Projects</h2>
        <div class="projects-grid">
            <?php if (mysqli_num_rows($projects) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($projects)): ?>
                    <div class="project-item">
                        <?php if (!empty($row['image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <?php endif; ?>
                        <div class="project-info">
                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                            <?php if (!empty($row['project_link'])): ?>
                                <a href="<?php echo htmlspecialchars($row['project_link']); ?>" target="_blank" class="btn">View Project</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No projects listed yet. Please add entries in the admin panel.</p>
            <?php endif; ?>
        </div>
    </div>
</section>