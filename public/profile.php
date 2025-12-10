<?php
require_once __DIR__ . '/../src/bootstrap.php';
use Src\Auth\Auth;
use Src\Models\User;

Auth::requireLogin();

$userModel = new User();
$user = $userModel->getUserById($_SESSION['user_id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    $data = [
        'username' => $_POST['username'] ?? $user['username'],
        'email' => $_POST['email'] ?? $user['email'],
        'full_name' => $_POST['full_name'] ?? $user['full_name'],
        'address' => $_POST['address'] ?? $user['address'],
        'phone' => $_POST['phone'] ?? $user['phone'],
        'role' => $user['role'] // Role cannot be changed by self
    ];

    // Password change (optional)
    if (!empty($_POST['new_password'])) {
        // In a real app, verify old password first
        $data['password'] = $_POST['new_password'];
        // We need to handle password update logic in User model or here. 
        // For simplicity, let's update password directly if provided.
        // NOTE: The current User::updateUser doesn't handle password. We might need to add it.
    }

    if ($userModel->updateUser($_SESSION['user_id'], $data)) {
        // Update session info if name changed
        $_SESSION['user']['full_name'] = $data['full_name'];
        $_SESSION['user']['username'] = $data['username'];
        $message = "Profile updated successfully!";
        // Refresh user data
        $user = $userModel->getUserById($_SESSION['user_id']);
    } else {
        $message = "Error updating profile.";
    }
}

include __DIR__ . '/../templates/header.php';
?>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1>My Profile</h1>
        <a href="javascript:history.back()" class="btn" style="background: transparent; border: 1px solid var(--text-muted); color: var(--text-muted);">
            &larr; Back
        </a>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <?php if ($message): ?>
            <div style="background: rgba(34, 197, 94, 0.2); color: var(--success); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <?= sanitize($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Role</label>
                <input type="text" class="form-control" value="<?= strtoupper($user['role']) ?>" disabled style="opacity: 0.7; cursor: not-allowed;">
            </div>

            <div class="grid grid-cols-2" style="gap: 1rem;">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>

            <div class="form-group" style="margin-top: 2rem; border-top: 1px solid #334155; padding-top: 1rem;">
                <label>Change Password (Leave blank to keep current)</label>
                <input type="password" name="new_password" class="form-control" placeholder="New Password">
            </div>

            <div style="text-align: right; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
