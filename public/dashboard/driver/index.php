<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
require_once __DIR__ . '/../../../src/Models/Route.php';
use Src\Auth\Auth;
use Src\Models\Route;

Auth::requireRole('driver');
$user = $_SESSION['user'];

$routeModel = new Route();
$routes = $routeModel->getRoutesByDriver($user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Src\Security\CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        die("CSRF Token Validation Failed.");
    }
    $routeId = $_POST['route_id'];
    $status = $_POST['status'];
    $routeModel->updateStatus($routeId, $status);
    redirect('/dashboard/driver/index.php?msg=updated');
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <h1 style="color: white; margin-bottom: 0.5rem;">Hello, <?= htmlspecialchars($user['full_name']) ?></h1>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">Here are your assigned routes.</p>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Route Map -->
    <div class="card glass" style="margin-bottom: 2rem; padding: 1.5rem;">
        <h3 style="color: white; margin-bottom: 1rem; font-weight: 600;">Collection Map Overview</h3>
        <div id="routeMap" style="height: 350px; width: 100%; border-radius: 0.5rem; z-index: 1;"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Center map (fallback coordinates)
            var map = L.map('routeMap').setView([-1.2921, 36.8219], 12);

            // Dark theme tiles for glassmorphism layout
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            <?php foreach($routes as $r): ?>
                <?php if ($r['status'] !== 'Completed'): ?>
                    // Simulated coordinates around center
                    var lat = -1.2921 + (Math.random() - 0.5) * 0.08;
                    var lng = 36.8219 + (Math.random() - 0.5) * 0.08;
                    var marker = L.marker([lat, lng]).addTo(map);
                    marker.bindPopup("<strong style='color:black;'><?= addslashes((string)$r['zone_name']) ?></strong><br>Status: <?= $r['status'] ?>");
                <?php endif; ?>
            <?php endforeach; ?>
        });
    </script>

    <h3 style="color: white; margin-bottom: 1.5rem;">Route Details</h3>
    <div class="grid grid-cols-1">
        <?php foreach ($routes as $r): ?>
            <div class="card glass" style="border-left: 4px solid <?= $r['status'] === 'Completed' ? 'rgba(34, 197, 94, 0.5)' : ($r['status'] === 'In Progress' ? 'rgba(234, 179, 8, 0.5)' : 'rgba(255,255,255,0.2)') ?>; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <h2 style="margin: 0; color: white;"><?= htmlspecialchars($r['zone_name']) ?></h2>
                        <span style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 0.25rem 0.6rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 500; margin-top:0.75rem; display:inline-block; backdrop-filter: blur(4px);">
                            <?= htmlspecialchars($r['waste_type']) ?>
                        </span>
                        <p style="margin: 1rem 0 0.5rem 0; font-weight: 600; font-size: 1.1rem; color: var(--secondary);"><?= date('l, M j', strtotime($r['collection_date'])) ?></p>
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">Status: <span style="font-weight: 500; color: <?= $r['status'] === 'Completed' ? '#4ade80' : ($r['status'] === 'In Progress' ? '#fde047' : 'white') ?>;"><?= $r['status'] ?></span></p>
                    </div>
                </div>

                <?php if ($r['status'] !== 'Completed'): ?>
                    <form method="POST" style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                        <?= \Src\Security\CSRF::getField() ?>
                        <input type="hidden" name="route_id" value="<?= $r['id'] ?>">
                        <?php if ($r['status'] === 'Pending'): ?>
                            <button name="status" value="In Progress" class="btn btn-primary" style="width: 100%; background: linear-gradient(135deg, #eab308, #ca8a04); box-shadow: 0 4px 15px rgba(234, 179, 8, 0.3); font-weight: 600;">Start Route</button>
                        <?php else: ?>
                            <button name="status" value="Completed" class="btn btn-primary" style="width: 100%; background: linear-gradient(135deg, #22c55e, #16a34a); box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3); font-weight: 600;">Mark Completed</button>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1); color: #4ade80; font-weight: bold; text-align: center; font-size: 1.1rem; letter-spacing: 0.5px;">✓ Completed</div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <?php if(empty($routes)): ?>
            <div class="card glass" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                <div style="font-size: 3rem; opacity: 0.5; margin-bottom: 1rem;">🛣️</div>
                <p style="font-size: 1.1rem; margin: 0;">No active routes assigned.</p>
                <p style="font-size: 0.9rem; margin-top: 0.5rem; opacity: 0.7;">Check back later or contact dispatch.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
