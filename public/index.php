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
    $email = $_POST['email'] ?? '';
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
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-box card">
        <h2 class="text-center mb-4" style="color: var(--primary);">EcoTrack Login</h2>
        
        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.2); color: #ef4444; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem; text-align: center;">
                <?= sanitize($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="user@example.com">
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
        </form>

        <div class="text-center mt-4">
            <span style="color: var(--text-muted);">New Resident?</span>
            <a href="/register.php">Create Account</a>
        </div>
    </div>
</div>

</body>
</html>
