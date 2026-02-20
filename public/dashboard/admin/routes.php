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
    if (!\Src\Security\CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        die("CSRF Token Validation Failed.");
    }
    $driver_id = (int)$_POST['driver_id'];
    $schedule_id = (int)$_POST['schedule_id'];
    $date = sanitize($_POST['date']);
    
    $routeModel->createRoute($driver_id, $schedule_id, $date);
    redirect('/dashboard/admin/routes.php?msg=created');
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <h1 style="color: white; margin: 0;">Route Assignments</h1>
            <a href="/dashboard/admin/index.php" class="btn mt-2" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--glass-border); padding: 0.5rem 1rem;">&larr; Back to Dashboard</a>
        </div>
        <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);">Assign Route</button>
    </div>

    <div class="card glass" style="overflow-x: auto; padding: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Date</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Driver</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Zone/Schedule</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Waste Type</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $r): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1rem; font-weight: 500;"><?= htmlspecialchars($r['collection_date']) ?></td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($r['driver_name']) ?></td>
                        <td style="padding: 1rem; color: var(--secondary);"><?= htmlspecialchars($r['zone_name']) ?></td>
                        <td style="padding: 1rem; color: var(--text-muted);"><?= htmlspecialchars($r['waste_type']) ?></td>
                        <td style="padding: 1rem;">
                            <span style="padding: 0.35rem 0.65rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 500; backdrop-filter: blur(4px); display: inline-block;
                                background: <?= $r['status'] === 'Completed' ? 'rgba(34, 197, 94, 0.15)' : ($r['status'] === 'In Progress' ? 'rgba(234, 179, 8, 0.15)' : 'rgba(255,255,255,0.1)') ?>;
                                color: <?= $r['status'] === 'Completed' ? '#4ade80' : ($r['status'] === 'In Progress' ? '#fde047' : 'var(--text-muted)') ?>;
                                border: 1px solid <?= $r['status'] === 'Completed' ? 'rgba(34, 197, 94, 0.3)' : ($r['status'] === 'In Progress' ? 'rgba(234, 179, 8, 0.3)' : 'rgba(255,255,255,0.2)') ?>;">
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
<div id="createModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); align-items:center; justify-content:center; z-index: 1000;">
    <div class="card glass" style="width: 100%; max-width: 500px; max-height: 90vh; overflow-y:auto; border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <h2 class="mb-4" style="color: white; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Assign Route to Driver</h2>
        <form method="POST">
            <?= \Src\Security\CSRF::getField() ?>
            <input type="hidden" name="action" value="create">
            
            <div class="form-group mt-4">
                <label style="color: white;">Driver</label>
                <select name="driver_id" class="form-control" required style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22white%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                    <?php foreach ($drivers as $d): ?>
                        <option value="<?= $d['id'] ?>" style="color: black;"><?= htmlspecialchars($d['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label style="color: white;">Schedule/Zone</label>
                <select name="schedule_id" class="form-control" required style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22white%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                    <?php foreach ($schedules as $s): ?>
                        <option value="<?= $s['id'] ?>" style="color: black;"><?= htmlspecialchars($s['zone_name']) ?> - <?= $s['collection_day'] ?> (<?= $s['waste_type'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label style="color: white;">Date</label>
                <input type="date" name="date" class="form-control" required value="<?= date('Y-m-d') ?>" style="color-scheme: dark;">
            </div>

            <div style="display:flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn" onclick="document.getElementById('createModal').style.display='none'" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);">Assign</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
