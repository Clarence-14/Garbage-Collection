<?php
require_once __DIR__ . '/../src/bootstrap.php';
use Src\Auth\Auth;

$auth = new Auth();
$error = '';

if (isLoggedIn()) {
    $role = $_SESSION['user']['role'];
    if ($role === 'admin') redirect('/dashboard/admin/index.php');
    if ($role === 'resident') redirect('/dashboard/resident/index.php');
    if ($role === 'driver') redirect('/dashboard/driver/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Src\Security\CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        die("CSRF Token Validation Failed.");
    }

    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if ($auth->login($email, $password)) {
        $role = $_SESSION['user']['role'];
        if ($role === 'admin') redirect('/dashboard/admin/index.php');
        if ($role === 'resident') redirect('/dashboard/resident/index.php');
        if ($role === 'driver') redirect('/dashboard/driver/index.php');
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EcoTrack</title>
    <!-- Use Google Fonts (Poppins & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-box card">
        <h2 class="text-center mb-4" style="color: white; font-weight: 700; letter-spacing: -0.5px;">EcoTrack <span style="color: var(--primary);">Login</span></h2>
        
        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 0.75rem; border-radius: 0.75rem; margin-bottom: 1.5rem; text-align: center; border: 1px solid rgba(239, 68, 68, 0.3);">
                <?= sanitize($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?= \Src\Security\CSRF::getField() ?>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="user@example.com">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary mt-2" style="width: 100%; font-size: 1rem; padding: 0.85rem;">Sign In</button>
        </form>

        <div class="text-center mt-4" style="padding-top: 1rem; border-top: 1px solid var(--glass-border);">
            <span style="color: var(--text-muted); font-size: 0.95rem;">New Resident?</span>
            <a href="/register.php" style="font-weight: 500; margin-left: 0.25rem;">Create Account</a>
        </div>
    </div>
</div>

</body>
</html>
