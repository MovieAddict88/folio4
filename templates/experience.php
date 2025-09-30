<section id="experience">
    <div class="container">
        <h2>Work Experience</h2>
        <div class="timeline">
            <?php if (mysqli_num_rows($experience) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($experience)): ?>
                    <div class="timeline-item">
                        <h3><?php echo htmlspecialchars($row['job_title']); ?></h3>
                        <h4><?php echo htmlspecialchars($row['company']); ?></h4>
                        <p class="timeline-date"><?php echo date("M Y", strtotime($row['start_date'])); ?> - <?php echo $row['end_date'] ? date("M Y", strtotime($row['end_date'])) : 'Present'; ?></p>
                        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No work experience listed yet. Please add entries in the admin panel.</p>
            <?php endif; ?>
        </div>
    </div>
</section>