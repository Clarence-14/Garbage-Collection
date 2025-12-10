<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
require_once __DIR__ . '/../../../src/Models/Schedule.php';
use Src\Auth\Auth;
use Src\Models\Schedule;

Auth::requireRole('resident');

$scheduleModel = new Schedule();
$schedules = $scheduleModel->getAllSchedules();
$user = $_SESSION['user'];

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <h1>Welcome, <?= htmlspecialchars($user['full_name']) ?></h1>
    
    <div class="grid grid-cols-3">
        <a href="/dashboard/resident/create_request.php" class="card" style="text-align:center; color: inherit;">
            <h3 style="color: var(--warning);">Report an Issue</h3>
            <p>Missed pickup, damaged bin, etc.</p>
        </a>
        <a href="/dashboard/resident/billing.php" class="card" style="text-align:center; color: inherit;">
            <h3 style="color: var(--secondary);">My Billing</h3>
            <p>View statement and balance.</p>
        </a>
    </div>

    <h2 class="mt-4">Collection Schedule</h2>
    <div class="card">
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Based on your area, here are the upcoming collections:</p>
        <div class="grid grid-cols-2">
             <?php foreach ($schedules as $s): ?>
                <div style="border: 1px solid #334155; padding: 1rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <strong style="font-size: 1.1rem;"><?= htmlspecialchars($s['collection_day']) ?>s</strong>
                        <div style="color: var(--text-muted);"><?= htmlspecialchars($s['zone_name']) ?></div>
                    </div>
                    <span style="background: var(--primary); padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.9rem;">
                        <?= htmlspecialchars($s['waste_type']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
