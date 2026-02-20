<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
require_once __DIR__ . '/../../../src/Models/Schedule.php';
use Src\Auth\Auth;
use Src\Models\Schedule;

Auth::requireRole('admin');

$scheduleModel = new Schedule();
$schedules = $scheduleModel->getAllSchedules();

if (isset($_GET['delete'])) {
    $scheduleModel->deleteSchedule($_GET['delete']);
    redirect('/dashboard/admin/schedules.php?msg=deleted');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    if (!\Src\Security\CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        die("CSRF Token Validation Failed.");
    }
    $data = [
        'zone_name' => sanitize($_POST['zone_name'] ?? ''),
        'collection_day' => sanitize($_POST['collection_day'] ?? ''),
        'waste_type' => sanitize($_POST['waste_type'] ?? ''),
        'description' => sanitize($_POST['description'] ?? '')
    ];
    $scheduleModel->createSchedule($data);
    sendMockEmail('residents@gc.local', 'New Collection Schedule', 'A new schedule for ' . $data['zone_name'] . ' has been added.');
    redirect('/dashboard/admin/schedules.php?msg=created');
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <h1 style="color: white; margin: 0;">Collection Schedules</h1>
            <a href="/dashboard/admin/index.php" class="btn mt-2" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--glass-border); padding: 0.5rem 1rem;">&larr; Back to Dashboard</a>
        </div>
        <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary" style="background: linear-gradient(135deg, var(--secondary), #ca8a04); box-shadow: 0 4px 15px rgba(234, 179, 8, 0.3);">Add Schedule</button>
    </div>

    <div class="grid grid-cols-2 mt-4">
        <?php foreach ($schedules as $s): ?>
            <div class="card glass card-interactive">
                <div style="display:flex; justify-content:space-between; align-items:start;">
                    <div>
                        <h3 style="margin: 0; color: white;"><?= htmlspecialchars($s['zone_name']) ?></h3>
                        <p style="margin: 0.5rem 0; font-size: 1.25rem; font-weight: 700; color: var(--secondary);"><?= htmlspecialchars($s['collection_day']) ?></p>
                        <span style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 500; backdrop-filter: blur(4px); display: inline-block; margin-top: 0.5rem;">
                            <?= htmlspecialchars($s['waste_type']) ?>
                        </span>
                    </div>
                     <a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete this schedule?')" style="color: #fca5a5; font-size: 1.25rem; opacity: 0.7; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">✕</a>
                </div>
                <?php if($s['description']): ?>
                    <div style="margin-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem;">
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.95rem; line-height: 1.5;"><?= nl2br(htmlspecialchars($s['description'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); align-items:center; justify-content:center; z-index: 1000;">
    <div class="card glass" style="width: 100%; max-width: 500px; max-height: 90vh; overflow-y:auto; border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <h2 class="mb-4" style="color: white; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Add Collection Schedule</h2>
        <form method="POST">
            <?= \Src\Security\CSRF::getField() ?>
            <input type="hidden" name="action" value="create">
            <div class="form-group mt-4">
                <label style="color: white;">Zone Name</label>
                <input type="text" name="zone_name" class="form-control" placeholder="e.g. Downtown" required>
            </div>
            <div class="form-group">
                <label style="color: white;">Collection Day</label>
                <select name="collection_day" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22white%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                    <option value="Monday" style="color: black;">Monday</option>
                    <option value="Tuesday" style="color: black;">Tuesday</option>
                    <option value="Wednesday" style="color: black;">Wednesday</option>
                    <option value="Thursday" style="color: black;">Thursday</option>
                    <option value="Friday" style="color: black;">Friday</option>
                    <option value="Saturday" style="color: black;">Saturday</option>
                    <option value="Sunday" style="color: black;">Sunday</option>
                </select>
            </div>
            <div class="form-group">
                <label style="color: white;">Waste Type</label>
                <select name="waste_type" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22white%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                    <option value="General" style="color: black;">General Waste</option>
                    <option value="Recycling" style="color: black;">Recycling</option>
                    <option value="Green" style="color: black;">Green Waste</option>
                    <option value="Bulk" style="color: black;">Bulk Pickup</option>
                </select>
            </div>
            <div class="form-group">
                <label style="color: white;">Description (Optional)</label>
                <textarea name="description" class="form-control" style="resize: vertical;"></textarea>
            </div>
            <div style="display:flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn" onclick="document.getElementById('createModal').style.display='none'" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, var(--secondary), #ca8a04); box-shadow: 0 4px 15px rgba(234, 179, 8, 0.3);">Create Schedule</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
