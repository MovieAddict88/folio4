<section class="download-prompt">
    <div class="container">
        <h2>Password Required</h2>
        <p>The document "<strong><?= e($doc['original_filename']) ?></strong>" is protected.</p>
        <p>Please enter the password to download the file.</p>

        <form action="<?= BASE_URL ?>portfolio/download/<?= e($doc['id']) ?>" method="POST">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autofocus>
            </div>

            <?php if (!empty($error)): ?>
                <p class="error"><?= e($error) ?></p>
            <?php endif; ?>

            <button type="submit" class="btn">Download File</button>
        </form>
    </div>
</section>