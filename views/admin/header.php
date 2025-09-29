<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Admin Panel'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Basic Reset & Typography */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
            color: #374151;
        }

        h1, h2, h3, h4, h5, h6 {
            color: #1C2B4A;
            font-weight: 600;
        }

        a {
            text-decoration: none;
            color: #1C2B4A;
        }

        /* Layout */
        .admin-wrapper {
            display: flex;
        }

        .sidebar {
            width: 260px;
            background-color: #1C2B4A;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }

        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 30px;
        }

        /* Sidebar */
        .sidebar-header {
            text-align: center;
            padding: 0 20px 20px 20px;
            border-bottom: 1px solid #3a4a6b;
        }
        .sidebar-header h2 {
            color: #fff;
            margin: 0;
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .sidebar-nav a {
            display: block;
            color: #e5e7eb;
            padding: 15px 25px;
            transition: background-color 0.3s, color 0.3s;
            font-weight: 500;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background-color: #E2B714;
            color: #1C2B4A;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
        }
        .sidebar-footer a {
            color: #9ca3af;
        }
        .sidebar-footer a:hover {
            color: #E2B714;
        }

        /* Main Content */
        .content-header h1 {
            margin: 0 0 25px 0;
            font-size: 2em;
        }
        .content-body {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }

        /* Forms & Buttons */
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; }
        input[type='text'], input[type='password'], input[type='email'], textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        .btn {
            background-color: #1C2B4A;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #E2B714;
            color: #1C2B4A;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            color: #fff;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        td .actions a {
            margin-right: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
            .admin-wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>