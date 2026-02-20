<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTrack - Garbage Collection Management</title>
    <!-- Use Google Fonts (Poppins & Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php
$toastMessage = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'created') $toastMessage = 'Successfully created!';
    elseif ($_GET['msg'] === 'updated') $toastMessage = 'Successfully updated!';
    elseif ($_GET['msg'] === 'deleted') $toastMessage = 'Successfully deleted!';
    elseif ($_GET['msg'] === 'profile_updated') $toastMessage = 'Profile updated successfully!';
    else $toastMessage = htmlspecialchars($_GET['msg']);
} elseif (isset($_SESSION['flash'])) {
    $toastMessage = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>
<?php if ($toastMessage): ?>
<div id="toast-notification" style="position: fixed; top: 20px; right: 20px; z-index: 9999; background: rgba(34, 197, 94, 0.2); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; padding: 1rem 1.5rem; border-radius: 0.5rem; backdrop-filter: blur(10px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3); display: flex; align-items: center; gap: 0.75rem; transform: translateX(120%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
    <span style="font-size: 1.25rem;">✅</span>
    <span style="font-weight: 500;"><?= $toastMessage ?></span>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const toast = document.getElementById('toast-notification');
        setTimeout(() => toast.style.transform = 'translateX(0)', 100);
        setTimeout(() => toast.style.transform = 'translateX(120%)', 4000);
    });
</script>
<?php endif; ?>

<?php if(isset($_SESSION['user_id'])): ?>
    <nav class="glass-nav">
        <div class="container nav-content">
            <a href="/" class="nav-brand">EcoTrack</a>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <!-- Subtle welcome message -->
                <span style="color: rgba(255,255,255,0.8); font-size: 0.9rem;">
                    Hello, <strong style="color: white;"><?= htmlspecialchars($_SESSION['user']['full_name']) ?></strong>
                </span>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="/profile.php" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 0.5rem 1rem; font-size: 0.85rem;">Profile</a>
                    <a href="/logout.php" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;">Logout</a>
                </div>
            </div>
        </div>
    </nav>
<?php endif; ?>
<main style="padding-top: 2rem; padding-bottom: 2rem; min-height: calc(100vh - 160px);">
