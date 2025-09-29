<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Admin Login') ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background-color: #f4f7f9; }
        .login-container { width: 100%; max-width: 360px; padding: 30px; background-color: #fff; border-radius: 6px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { display: block; width: 100%; background-color: #007BFF; color: #fff; padding: 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; text-align: center; font-size: 16px; }
        .btn:hover { background-color: #0056b3; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px; list-style: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <?= $content ?>
    </div>
</body>
</html>