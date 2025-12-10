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
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Basic Validation
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
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
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-box card" style="max-width: 500px;">
        <h2 class="text-center mb-4" style="color: var(--secondary);">Resident Registration</h2>
        
        <?php if ($error): ?>
            <div style="background: rgba(239, 68, 68, 0.2); color: #ef4444; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <?= sanitize($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
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

            <button type="submit" class="btn btn-primary" style="width: 100%; background-color: var(--secondary);">Create Account</button>
        </form>

        <div class="text-center mt-4">
            <span style="color: var(--text-muted);">Already have an account?</span>
            <a href="/index.php">Login here</a>
        </div>
    </div>
</div>

</body>
</html>
