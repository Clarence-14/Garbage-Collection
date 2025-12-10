<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
use Src\Auth\Auth;

Auth::requireRole('admin');

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <h1>Admin Dashboard</h1>
    <div class="grid grid-cols-2"> <!-- Changed from 3 to 2 for layout or add a 4th -->
        
        <!-- Manage Users -->
        <a href="/dashboard/admin/users.php" class="card" style="text-decoration:none; color:inherit;">
            <h3 style="color: var(--primary);">Manage Users</h3>
            <p style="color: var(--text-muted);">Add, edit, or remove residents, drivers, and other admins.</p>
        </a>

        <!-- Manage Schedules -->
        <a href="/dashboard/admin/schedules.php" class="card" style="text-decoration:none; color:inherit;">
            <h3 style="color: var(--secondary);">Collection Schedules</h3>
            <p style="color: var(--text-muted);">Set up zones, recurring collection days, and waste types.</p>
        </a>
        
        <!-- Route Assignments -->
        <a href="/dashboard/admin/routes.php" class="card" style="text-decoration:none; color:inherit;">
            <h3 style="color: #8b5cf6;">Route Assignments</h3>
            <p style="color: var(--text-muted);">Assign drivers to daily collection routes.</p>
        </a>

        <!-- Service Requests -->
        <a href="/dashboard/admin/requests.php" class="card" style="text-decoration:none; color:inherit;">
            <h3 style="color: var(--warning);">Service Requests</h3>
            <p style="color: var(--text-muted);">View and resolve missed pickups, bin replacements, etc.</p>
        </a>
    </div>

    <!-- Quick Stats Placeholder -->
    <div class="mt-4">
        <h2>System Overview</h2>
        <div class="card">
            <p>Welcome to the main control panel. Use the links above to manage the system.</p>
        </div>
    </div>
</div>

<style>
    .card:hover {
        transform: translateY(-5px);
        border: 1px solid var(--primary);
    }
    .grid {
        display: grid;
        gap: 1.5rem;
    }
    @media (min-width: 768px) {
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
