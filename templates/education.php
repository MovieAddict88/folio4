<section id="education">
    <div class="container">
        <h2>Education</h2>
        <div class="timeline">
            <?php if (mysqli_num_rows($education) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($education)): ?>
                    <div class="timeline-item">
                        <h3><?php echo htmlspecialchars($row['degree']); ?></h3>
                        <h4><?php echo htmlspecialchars($row['institution']); ?></h4>
                        <p class="timeline-date"><?php echo $row['start_year']; ?> - <?php echo $row['end_year']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No education history listed yet. Please add entries in the admin panel.</p>
            <?php endif; ?>
        </div>
    </div>
</section>