<!-- Hero/About Section -->
<section id="about" class="hero">
    <div class="container">
        <?php if (!empty($settings['profile_picture'])): ?>
            <img src="<?= BASE_URL ?>public/uploads/<?= e($settings['profile_picture']) ?>" alt="Profile Picture" class="profile-picture">
        <?php endif; ?>

        <h1><?= e($settings['your_name'] ?? 'Welcome') ?></h1>
        <p class="tagline"><?= e($settings['your_title'] ?? 'A passionate developer') ?></p>

        <?php if (!empty($settings['bio'])): ?>
            <div class="bio">
                <p><?= nl2br(e($settings['bio'])) ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Portfolio Section -->
<section id="portfolio">
    <div class="container">
        <h2>My Portfolio</h2>
        <?php if (empty($items)): ?>
            <p style="text-align: center;">No portfolio items have been added yet.</p>
        <?php else: ?>
            <div class="portfolio-grid">
                <?php foreach ($items as $item): ?>
                    <div class="portfolio-item">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= BASE_URL ?>public/uploads/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>">
                        <?php endif; ?>
                        <div class="info">
                            <h3><?= e($item['title']) ?></h3>
                            <p><?= nl2br(e($item['description'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Documents Section -->
<section id="documents">
    <div class="container">
        <h2>Documents</h2>
        <?php if (empty($documents)): ?>
             <p style="text-align: center;">No documents have been uploaded yet.</p>
        <?php else: ?>
            <ul class="document-list">
                <?php foreach ($documents as $doc): ?>
                    <li>
                        <span><?= e($doc['original_filename']) ?></span>
                        <a href="<?= BASE_URL ?>portfolio/download/<?= e($doc['id']) ?>" class="btn-download">
                            <?= !empty($doc['password']) ? 'Unlock & Download' : 'Download' ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>