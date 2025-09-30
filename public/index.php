<?php
require_once '../src/init.php';

// If config doesn't exist, redirect to installer
if (!file_exists(CONFIG_FILE)) {
    header('Location: ../install.php');
    exit;
}

// Fetch all public data
$profile = $pdo->query("SELECT * FROM profile WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
$portfolio_items = $pdo->query("SELECT * FROM portfolio_items ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$documents = $pdo->query("SELECT id, document_name FROM documents ORDER BY upload_date DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile['full_name'] ?? 'My Portfolio'); ?>'s Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><?php echo htmlspecialchars($profile['full_name'] ?? 'Portfolio'); ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#portfolio">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#documents">Documents</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header id="home" class="hero-section text-center text-white">
        <div class="container">
            <img src="<?php echo htmlspecialchars($profile['profile_picture'] ?? 'https://via.placeholder.com/150'); ?>" class="profile-picture rounded-circle" alt="Profile Picture">
            <h1 class="display-4 mt-3"><?php echo htmlspecialchars($profile['full_name'] ?? 'Welcome to My Portfolio'); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($profile['title'] ?? 'A showcase of my projects and skills.'); ?></p>
        </div>
    </header>

    <main class="container my-5">
        <section id="about" class="py-5">
            <div class="row">
                <div class="col-md-10 mx-auto text-center">
                    <h2>About Me</h2>
                    <p class="lead text-muted">
                        <?php echo nl2br(htmlspecialchars($profile['bio'] ?? 'Information about me. This can be updated in the admin panel.')); ?>
                    </p>
                </div>
            </div>
        </section>

        <hr class="my-5">

        <section id="portfolio" class="py-5">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>My Work</h2>
                    <p class="text-muted">Here are some of my recent projects.</p>
                </div>
            </div>

            <div class="row mt-4">
                <?php if (empty($portfolio_items)): ?>
                    <div class="col text-center">
                        <p>No portfolio items have been added yet. Check back soon!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($portfolio_items as $item): ?>
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <?php if (!empty($item['project_url'])): ?>
                                <a href="<?php echo htmlspecialchars($item['project_url']); ?>" class="btn btn-primary" target="_blank">View Project</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <hr class="my-5">

        <section id="documents" class="py-5">
             <div class="row">
                <div class="col-md-12 text-center">
                    <h2>Protected Documents</h2>
                    <p class="text-muted">These documents require a password to download.</p>
                </div>
            </div>
            <div class="row mt-4 justify-content-center">
                <div class="col-md-8">
                    <?php if (empty($documents)): ?>
                         <div class="col text-center">
                            <p>No documents are available for download at the moment.</p>
                        </div>
                    <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($documents as $doc): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-file-earmark-text me-2"></i><?php echo htmlspecialchars($doc['document_name']); ?></span>
                                <a href="download.php?id=<?php echo $doc['id']; ?>" class="btn btn-success">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </main>

    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($profile['full_name'] ?? 'My Portfolio'); ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>