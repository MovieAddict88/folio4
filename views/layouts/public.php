<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'My Portfolio') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="main-header">
        <div class="container">
            <a href="<?= BASE_URL ?>" class="logo"><?= e($settings['your_name'] ?? 'Portfolio') ?></a>
            <nav class="main-nav">
                <a href="#about">About</a>
                <a href="#portfolio">Portfolio</a>
                <a href="#documents">Documents</a>
            </nav>
        </div>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= e($settings['your_name'] ?? '') ?>. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>