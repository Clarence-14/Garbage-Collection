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
    $scheduleModel->createSchedule($_POST);
    sendMockEmail('residents@gc.local', 'New Collection Schedule', 'A new schedule for ' . $_POST['zone_name'] . ' has been added.');
    redirect('/dashboard/admin/schedules.php?msg=created');
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <h1>Collection Schedules</h1>
            <a href="/dashboard/admin/index.php" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Dashboard</a>
        </div>
        <button onclick="document.getElementById('createModal').style.display='flex'" class="btn btn-primary">Add Schedule</button>
    </div>

    <div class="grid grid-cols-2">
        <?php foreach ($schedules as $s): ?>
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:start;">
                    <div>
                        <h3 style="margin: 0; color: var(--secondary);"><?= htmlspecialchars($s['zone_name']) ?></h3>
                        <p style="margin: 0.5rem 0; font-size: 1.1rem; font-weight: bold;"><?= htmlspecialchars($s['collection_day']) ?></p>
                        <span style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">
                            <?= htmlspecialchars($s['waste_type']) ?>
                        </span>
                    </div>
                     <a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete this schedule?')" style="color: var(--danger);">✕</a>
                </div>
                <?php if($s['description']): ?>
                    <p style="margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($s['description']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); align-items:center; justify-content:center;">
    <div class="card" style="width: 100%; max-width: 500px;">
        <h2 class="mb-4">Add Collection Schedule</h2>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label>Zone Name</label>
                <input type="text" name="zone_name" class="form-control" placeholder="e.g. Downtown" required>
            </div>
            <div class="form-group">
                <label>Collection Day</label>
                <select name="collection_day" class="form-control">
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>
            <div class="form-group">
                <label>Waste Type</label>
                <select name="waste_type" class="form-control">
                    <option value="General">General Waste</option>
                    <option value="Recycling">Recycling</option>
                    <option value="Green">Green Waste</option>
                    <option value="Bulk">Bulk Pickup</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description (Optional)</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div style="display:flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" class="btn" onclick="document.getElementById('createModal').style.display='none'" style="background: transparent; border: 1px solid #334155; color: white;">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Schedule</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
