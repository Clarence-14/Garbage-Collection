<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
require_once __DIR__ . '/../../../src/Models/Route.php';
require_once __DIR__ . '/../../../src/Models/User.php';
require_once __DIR__ . '/../../../src/Models/Schedule.php';

use Src\Auth\Auth;
use Src\Models\Route;
use Src\Models\User;
use Src\Models\Schedule;

Auth::requireRole('admin');

$routeModel = new Route();
$userModel = new User();
$scheduleModel = new Schedule();

$routes = $routeModel->getAllRoutes();
$drivers = array_filter($userModel->getAllUsers(), fn($u) => $u['role'] === 'driver');
$schedules = $scheduleModel->getAllSchedules();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $driver_id = $_POST['driver_id'];
    $schedule_id = $_POST['schedule_id'];
    $date = $_POST['date'];
    
    $routeModel->createRoute($driver_id, $schedule_id, $date);
    redirect('/dashboard/admin/routes.php?msg=created');
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <h1>Route Assignments</h1>
            <a href="/dashboard/admin/index.php" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Dashboard</a>
        </div>
        <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary">Assign Route</button>
    </div>

    <div class="card" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid #334155;">
                    <th style="padding: 1rem;">Date</th>
                    <th style="padding: 1rem;">Driver</th>
                    <th style="padding: 1rem;">Zone/Schedule</th>
                    <th style="padding: 1rem;">Waste Type</th>
                    <th style="padding: 1rem;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $r): ?>
                    <tr style="border-bottom: 1px solid #1e293b;">
                        <td style="padding: 1rem;"><?= htmlspecialchars($r['collection_date']) ?></td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($r['driver_name']) ?></td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($r['zone_name']) ?></td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($r['waste_type']) ?></td>
                        <td style="padding: 1rem;">
                            <span style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; 
                                background: <?= $r['status'] === 'Completed' ? 'var(--secondary)' : ($r['status'] === 'In Progress' ? 'var(--warning)' : '#334155') ?>;">
                                <?= $r['status'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Assign Modal -->
<div id="createModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); align-items:center; justify-content:center;">
    <div class="card" style="width: 100%; max-width: 500px;">
        <h2 class="mb-4">Assign Route to Driver</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label>Driver</label>
                <select name="driver_id" class="form-control" required>
                    <?php foreach ($drivers as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Schedule/Zone</label>
                <select name="schedule_id" class="form-control" required>
                    <?php foreach ($schedules as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['zone_name']) ?> - <?= $s['collection_day'] ?> (<?= $s['waste_type'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control" required value="<?= date('Y-m-d') ?>">
            </div>

            <div style="display:flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" class="btn" onclick="document.getElementById('createModal').style.display='none'" style="background: transparent; border: 1px solid #334155; color: white;">Cancel</button>
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
