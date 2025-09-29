<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Panel') ?> - My Portfolio</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            background-color: #f4f7f9;
            color: #333;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 240px;
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            color: #fff;
            text-align: center;
            margin: 0 0 30px 0;
            font-size: 1.5em;
        }
        .sidebar nav a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .sidebar nav a:hover, .sidebar nav a.active {
            background-color: #34495e;
        }
        .sidebar .logout {
            margin-top: auto;
        }
        .main-content {
            flex-grow: 1;
            padding: 30px;
            overflow-y: auto;
        }
        .main-content header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .main-content h1 {
            margin: 0;
            font-size: 1.8em;
        }
        .user-info {
            font-weight: 500;
        }
        .card {
            background-color: #fff;
            padding: 25px;
            border-radius: 6px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <a href="<?= BASE_URL ?>admin/dashboard" class="<?= ($GLOBALS['admin_controller_name'] ?? '') === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="<?= BASE_URL ?>admin/portfolio" class="<?= ($GLOBALS['admin_controller_name'] ?? '') === 'portfolio' ? 'active' : '' ?>">Portfolio</a>
            <a href="<?= BASE_URL ?>admin/documents" class="<?= ($GLOBALS['admin_controller_name'] ?? '') === 'documents' ? 'active' : '' ?>">Documents</a>
            <a href="<?= BASE_URL ?>admin/settings" class="<?= ($GLOBALS['admin_controller_name'] ?? '') === 'settings' ? 'active' : '' ?>">Settings</a>
        </nav>
        <div class="logout">
             <a href="<?= BASE_URL ?>admin/auth/logout">Logout</a>
        </div>
    </aside>
    <main class="main-content">
        <header>
            <h1><?= e($title ?? 'Dashboard') ?></h1>
            <div class="user-info">
                Logged in as: <strong><?= e($GLOBALS['current_username'] ?? 'Admin') ?></strong>
            </div>
        </header>
        <div class="card">
            <?= $content ?>
        </div>
    </main>
</body>
</html>