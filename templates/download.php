<section id="download">
    <div class="container">
        <h2>Downloads</h2>
        <div class="download-list">
            <?php if (mysqli_num_rows($documents) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($documents)): ?>
                    <div class="download-item">
                        <p><?php echo htmlspecialchars($row['filename']); ?></p>
                        <form action="download_handler.php" method="post" class="download-form">
                            <input type="hidden" name="doc_id" value="<?php echo $row['id']; ?>">
                            <input type="password" name="password" placeholder="Enter password" required>
                            <button type="submit" class="btn">Download</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No documents available for download yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>