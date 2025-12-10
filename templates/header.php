<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTrack - Garbage Collection Management</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* Simple Navbar */
        nav {
            background: var(--bg-card);
            border-bottom: 1px solid #334155;
            padding: 1rem 0;
        }
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .brand {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--primary);
        }
    </style>
</head>
<body>
<?php if(isset($_SESSION['user_id'])): ?>
    <nav>
        <div class="container nav-content">
            <a href="/" class="brand">EcoTrack</a>
            <div>
                <span style="margin-right: 15px;">Hello, <?= htmlspecialchars($_SESSION['user']['full_name']) ?></span>
                <a href="/profile.php" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: var(--secondary); margin-right: 0.5rem;">Profile</a>
                <a href="/logout.php" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Logout</a>
            </div>
        </div>
    </nav>
<?php endif; ?>
<main>
