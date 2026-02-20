<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
require_once __DIR__ . '/../../../src/Models/ServiceRequest.php';
use Src\Auth\Auth;
use Src\Models\ServiceRequest;

Auth::requireRole('resident');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Src\Security\CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        die("CSRF Token Validation Failed.");
    }
    $type = $_POST['type'] ?? '';
    $description = $_POST['description'] ?? '';
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
    
    if ($type && $description && $latitude && $longitude) {
        $reqModel = new ServiceRequest();
        $reqModel->createRequest($_SESSION['user_id'], $type, $description, $latitude, $longitude);
        $success = "Request submitted successfully.";
    } else {
        $error = "Please fill all fields and select your location on the map.";
    }
}

include __DIR__ . '/../../../templates/header.php';
?>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1 style="color: white; margin: 0;">Submit Service Request</h1>
        <a href="/dashboard/resident/index.php" class="btn" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--glass-border); padding: 0.5rem 1rem;">&larr; Back to Dashboard</a>
    </div>
    
    <div class="card glass" style="max-width: 600px; margin-top: 2rem; margin-left: auto; margin-right: auto;">
        <?php if ($success): ?>
            <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #4ade80; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; text-align: center;">
                <?= $success ?>
            </div>
            <div style="text-align: center;">
                <a href="/dashboard/resident/index.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        <?php else: ?>
            
            <?php if($error): ?>
                <div style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 0.75rem; border-radius: 0.75rem; margin-bottom: 1.5rem; text-align: center; border: 1px solid rgba(239, 68, 68, 0.3);">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <?= \Src\Security\CSRF::getField() ?>
                <div class="form-group">
                    <label style="color: white;">Issue Type</label>
                    <select name="type" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22white%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                        <option value="Missed Pickup" style="color: black;">Missed Pickup</option>
                        <option value="Bin Damage" style="color: black;">Bin Damage</option>
                        <option value="Bulk Pickup" style="color: black;">Request Bulk Pickup</option>
                        <option value="Other" style="color: black;">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label style="color: white; margin-bottom: 0.5rem; display: block;">Exact Location <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal;">(Click map to drop pin)</span></label>
                    <div id="requestMap" style="height: 300px; width: 100%; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 1rem; z-index: 1;"></div>
                    <input type="hidden" name="latitude" id="latInput" required>
                    <input type="hidden" name="longitude" id="lngInput" required>
                    <div id="locationStatus" style="color: #fca5a5; font-size: 0.85rem; margin-top: 0.5rem;">⚠️ No location selected yet. Drop a pin on the map.</div>
                </div>

                <div class="form-group">
                    <label style="color: white;">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Please provide details..." style="resize: vertical;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Submit Request</button>
            </form>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Set boundaries strictly to Kahawa Sukari area
                    var kahawaBounds = [
                        [-1.215, 36.910], // Southwest
                        [-1.175, 36.950]  // Northeast
                    ];

                    var map = L.map('requestMap', {
                        maxBounds: kahawaBounds,
                        maxBoundsViscosity: 1.0,
                        minZoom: 13
                    }).setView([-1.1910, 36.9287], 15);
                    
                    var marker;

                    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                        maxZoom: 20
                    }).addTo(map);

                    // If browser supports Geolocation, center the map there initially (only if within bounds)
                    if ("geolocation" in navigator) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            var userLat = position.coords.latitude;
                            var userLng = position.coords.longitude;
                            
                            // Basic check if they are in Kahawa
                            if(userLat > -1.215 && userLat < -1.175 && userLng > 36.910 && userLng < 36.950) {
                                map.setView([userLat, userLng], 16);
                            }
                        });
                    }

                    // Click event to place pin
                    map.on('click', function(e) {
                        var lat = e.latlng.lat;
                        var lng = e.latlng.lng;

                        if (marker) {
                            map.removeLayer(marker);
                        }
                        
                        marker = L.marker([lat, lng]).addTo(map);
                        
                        document.getElementById('latInput').value = lat;
                        document.getElementById('lngInput').value = lng;
                        
                        var statusEl = document.getElementById('locationStatus');
                        statusEl.innerText = '✅ Location grabbed successfully.';
                        statusEl.style.color = '#4ade80';
                    });
                });
            </script>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
