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
    <h1 style="color: var(--primary);">Welcome, <span style="color: white;"><?= htmlspecialchars($user['full_name']) ?></span></h1>
    
    <div class="grid grid-cols-3 mt-4">
        <a href="/dashboard/resident/create_request.php" class="card card-interactive" style="text-align:center; color: inherit; display: block;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚠️</div>
            <h3 style="color: var(--warning); margin-bottom: 0.5rem;">Report an Issue</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Missed pickup, damaged bin, etc.</p>
        </a>
        <a href="/dashboard/resident/billing.php" class="card card-interactive" style="text-align:center; color: inherit; display: block;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💳</div>
            <h3 style="color: var(--secondary); margin-bottom: 0.5rem;">My Billing</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">View statement and balance.</p>
        </a>
    </div>

    <h2 class="mt-4" style="margin-top: 3rem;">Collection Schedule</h2>
    <div class="card glass mb-4">
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Based on your area, here are the upcoming collections:</p>
        <div class="grid grid-cols-2">
             <?php foreach ($schedules as $s): ?>
                <div style="background: rgba(255,255,255,0.03); padding: 1.25rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.05);">
                    <div>
                        <strong style="font-size: 1.15rem; color: white; display: block; margin-bottom: 0.25rem;"><?= htmlspecialchars($s['collection_day']) ?>s</strong>
                        <div style="color: rgba(148, 163, 184, 0.8); font-size: 0.9rem;">
                            <span style="display:inline-block; margin-right: 0.5rem;">📍</span><?= htmlspecialchars($s['zone_name']) ?>
                        </div>
                    </div>
                    <span style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 500; backdrop-filter: blur(4px);">
                        <?= htmlspecialchars($s['waste_type']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Notifications Panel -->
    <?php
        require_once __DIR__ . '/../../../src/Models/ServiceRequest.php';
        $requestModel = new \Src\Models\ServiceRequest();
        $myRequests = $requestModel->getRequestsByUserId($user['id']);
    ?>
    <h2 class="mt-4" style="margin-top: 3rem; color: white;">Recent Activity (Notifications)</h2>
    <div class="grid grid-cols-1">
        <?php if(empty($myRequests)): ?>
            <div class="card glass">
                <p style="color: var(--text-muted); margin: 0;">No recent activity.</p>
            </div>
        <?php else: ?>
            <?php foreach(array_slice($myRequests, 0, 3) as $req): ?>
                <?php 
                    $statusColor = $req['status'] === 'Open' ? '#fde047' : ($req['status'] === 'Resolved' ? '#4ade80' : 'var(--text-muted)');
                    $statusBg = $req['status'] === 'Open' ? 'rgba(234, 179, 8, 0.15)' : ($req['status'] === 'Resolved' ? 'rgba(34, 197, 94, 0.15)' : 'rgba(255,255,255,0.1)');
                ?>
                <div class="card glass" style="display:flex; justify-content:space-between; align-items:center; padding: 1.25rem;">
                    <div>
                        <strong style="color: white; font-size: 1.1rem;"><?= htmlspecialchars($req['request_type']) ?></strong>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">Submitted on: <?= htmlspecialchars($req['created_at']) ?></div>
                        <?php if($req['admin_notes']): ?>
                            <div style="margin-top: 0.75rem; background: rgba(59, 130, 246, 0.1); border-left: 3px solid rgba(59, 130, 246, 0.5); padding: 0.5rem 0.75rem; color: #60a5fa; font-size: 0.85rem; border-radius: 0 0.25rem 0.25rem 0;">
                                <strong style="color: white;">Admin Update:</strong> <?= htmlspecialchars($req['admin_notes']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <span style="background: <?= $statusBg ?>; color: <?= $statusColor ?>; padding: 0.4rem 0.85rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; border: 1px solid <?= $statusColor ?>; opacity: 0.8;">
                        <?= $req['status'] ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
