<section id="skills">
    <div class="container">
        <h2>Skills</h2>
        <div class="skills-container">
            <?php if (mysqli_num_rows($skills) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($skills)): ?>
                    <div class="skill-item">
                        <p><?php echo htmlspecialchars($row['name']); ?></p>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: <?php echo $row['level']; ?>%;"></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No skills listed yet. Please add entries in the admin panel.</p>
            <?php endif; ?>
        </div>
    </div>
</section>