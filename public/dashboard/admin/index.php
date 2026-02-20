<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
use Src\Auth\Auth;

Auth::requireRole('admin');

// Analytics Data Fetching
$db = \Src\Config\Database::getInstance()->getConnection();

// User Stats
$userStats = $db->query("SELECT role, COUNT(*) as count FROM users GROUP BY role")->fetchAll();
$userLabels = [];
$userData = [];
foreach($userStats as $stat) {
    if(empty($stat['role'])) continue;
    $userLabels[] = ucfirst($stat['role']);
    $userData[] = $stat['count'];
}

// Request Stats
$reqStats = $db->query("SELECT status, COUNT(*) as count FROM service_requests GROUP BY status")->fetchAll();
$reqLabels = [];
$reqData = [];
foreach($reqStats as $stat) {
    $reqLabels[] = ucfirst($stat['status']);
    $reqData[] = $stat['count'];
}

// Route Stats
$routeStats = $db->query("SELECT status, COUNT(*) as count FROM collection_routes GROUP BY status")->fetchAll();
$routeLabels = [];
$routeData = [];
foreach($routeStats as $stat) {
    $routeLabels[] = ucfirst($stat['status']);
    $routeData[] = $stat['count'];
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <h1 style="color: white; margin-bottom: 2rem;">Admin Dashboard</h1>
    <div class="grid grid-cols-2">
        
        <!-- Manage Users -->
        <a href="/dashboard/admin/users.php" class="card card-interactive glass" style="text-decoration:none; color:inherit; display:flex; flex-direction: column; align-items:flex-start;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">👥</div>
            <h3 style="color: var(--primary); margin-bottom: 0.5rem;">Manage Users</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Add, edit, or remove residents, drivers, and other admins.</p>
        </a>

        <!-- Manage Schedules -->
        <a href="/dashboard/admin/schedules.php" class="card card-interactive glass" style="text-decoration:none; color:inherit; display:flex; flex-direction: column; align-items:flex-start;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">🗓️</div>
            <h3 style="color: var(--secondary); margin-bottom: 0.5rem;">Collection Schedules</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Set up zones, recurring collection days, and waste types.</p>
        </a>
        
        <!-- Route Assignments -->
        <a href="/dashboard/admin/routes.php" class="card card-interactive glass" style="text-decoration:none; color:inherit; display:flex; flex-direction: column; align-items:flex-start;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">🗺️</div>
            <h3 style="color: #a78bfa; margin-bottom: 0.5rem;">Route Assignments</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Assign drivers to daily collection routes.</p>
        </a>

        <!-- Service Requests -->
        <a href="/dashboard/admin/requests.php" class="card card-interactive glass" style="text-decoration:none; color:inherit; display:flex; flex-direction: column; align-items:flex-start;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">📥</div>
            <h3 style="color: var(--warning); margin-bottom: 0.5rem;">Service Requests</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">View and resolve missed pickups, bin replacements, etc.</p>
        </a>
    </div>

    <!-- Analytics Dashboard -->
    <div class="mt-4" style="margin-top: 3rem;">
        <h2 style="color: white; margin-bottom: 1.5rem;">System Analytics</h2>
        <div class="grid grid-cols-2">
            <div class="card glass">
                <h3 style="color: white; margin-bottom: 1rem;">Users by Role</h3>
                <canvas id="userChart" width="400" height="250"></canvas>
            </div>
            <div class="card glass">
                <h3 style="color: white; margin-bottom: 1rem;">Service Requests</h3>
                <canvas id="reqChart" width="400" height="250"></canvas>
            </div>
            <div class="card glass" style="grid-column: span 2;">
                <h3 style="color: white; margin-bottom: 1rem;">Collection Routes Status</h3>
                <canvas id="routeChart" width="400" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
    .grid {
        display: grid;
        gap: 1.5rem;
    }
    @media (min-width: 768px) {
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Common Chart Options for Glassmorphism Dark Theme
    Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';
    
    // User Chart (Doughnut)
    new Chart(document.getElementById('userChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($userLabels) ?>,
            datasets: [{
                data: <?= json_encode($userData) ?>,
                backgroundColor: ['#22c55e', '#eab308', '#a855f7'],
                borderWidth: 0
            }]
        }
    });

    // Requests Chart (Pie)
    new Chart(document.getElementById('reqChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($reqLabels) ?>,
            datasets: [{
                data: <?= json_encode($reqData) ?>,
                backgroundColor: ['#fde047', '#4ade80', '#fca5a5'],
                borderWidth: 0
            }]
        }
    });

    // Routes Chart (Bar)
    new Chart(document.getElementById('routeChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($routeLabels) ?>,
            datasets: [{
                label: 'Routes',
                data: <?= json_encode($routeData) ?>,
                backgroundColor: ['#4ade80', '#fde047', '#fca5a5'],
                borderRadius: 4
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
