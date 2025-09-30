<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            color: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 10px 20px;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background: #495057;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .sidebar-header {
            padding: 0 20px 20px;
            text-align: center;
            border-bottom: 1px solid #495057;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
        }
        .card {
            border-radius: .5rem;
        }
        .breadcrumb {
            background-color: #e9ecef;
            padding: .75rem 1rem;
            border-radius: .25rem;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h5>Admin Panel</h5>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo ($page === 'dashboard') ? 'active' : ''; ?>" href="index.php?page=dashboard">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page === 'profile') ? 'active' : ''; ?>" href="index.php?page=profile">
                <i class="bi bi-person-fill"></i> Profile
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page === 'portfolio') ? 'active' : ''; ?>" href="index.php?page=portfolio">
                <i class="bi bi-briefcase-fill"></i> Portfolio
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page === 'documents') ? 'active' : ''; ?>" href="index.php?page=documents">
                <i class="bi bi-file-earmark-lock-fill"></i> Documents
            </a>
        </li>
        <li class="nav-item mt-auto">
            <a class="nav-link" href="logout.php">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo ucfirst($page); ?></li>
      </ol>
    </nav>
    <div class="container-fluid">