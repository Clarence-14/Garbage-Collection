<?php
require_once __DIR__ . '/../src/bootstrap.php';
use Src\Auth\Auth;

$auth = new Auth();
$error = '';
$success = '';

if (isLoggedIn()) {
    redirect('/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Src\Security\CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        die("CSRF Token Validation Failed.");
    }

    $username = sanitize($_POST['username'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $full_name = sanitize($_POST['full_name'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');

    // Basic Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (empty($username) || empty($password) || empty($full_name)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            $result = $auth->register([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'full_name' => $full_name,
                'address' => $address,
                'phone' => $phone,
                'role' => 'resident' // Force role
            ]);

            if ($result) {
                // Auto login or redirect to login
                header("Location: /index.php?registered=1");
                exit();
            } else {
                $error = "Registration failed. Username or Email might be taken.";
            }
        } catch (Exception $e) {
             $error = "Registration failed. Username or Email might be taken.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EcoTrack</title>
    <!-- Use Google Fonts (Poppins & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-box card" style="max-width: 500px;">
        <h2 class="text-center mb-4" style="color: white; font-weight: 700; letter-spacing: -0.5px;">Resident <span style="color: var(--secondary);">Registration</span></h2>
        
        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 0.75rem; border-radius: 0.75rem; margin-bottom: 1.5rem; text-align: center; border: 1px solid rgba(239, 68, 68, 0.3);">
                <?= sanitize($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?= \Src\Security\CSRF::getField() ?>
            <div class="grid grid-cols-2" style="gap: 1rem;">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Address (For Collection)</label>
                <textarea name="address" class="form-control" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary mt-2" style="width: 100%; font-size: 1rem; padding: 0.85rem; background: linear-gradient(135deg, var(--secondary), #ca8a04); box-shadow: 0 4px 15px rgba(234, 179, 8, 0.3);">Create Account</button>
        </form>

        <div class="text-center mt-4" style="padding-top: 1rem; border-top: 1px solid var(--glass-border);">
            <span style="color: var(--text-muted); font-size: 0.95rem;">Already have an account?</span>
            <a href="/index.php" style="font-weight: 500; margin-left: 0.25rem; color: var(--secondary);">Login here</a>
        </div>
    </div>
</div>

</body>
</html>
