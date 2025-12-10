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
    $routeId = $_POST['route_id'];
    $status = $_POST['status'];
    $routeModel->updateStatus($routeId, $status);
    redirect('/dashboard/driver/index.php?msg=updated');
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <h1>Hello, <?= htmlspecialchars($user['full_name']) ?></h1>
    <p style="color: var(--text-muted);">Here are your assigned routes.</p>

    <div class="grid grid-cols-1">
        <?php foreach ($routes as $r): ?>
            <div class="card" style="border-left: 5px solid <?= $r['status'] === 'Completed' ? 'var(--secondary)' : ($r['status'] === 'In Progress' ? 'var(--warning)' : '#334155') ?>;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <h2 style="margin: 0;"><?= htmlspecialchars($r['zone_name']) ?></h2>
                        <span style="background: var(--primary); padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.8rem; margin-top:0.5rem; display:inline-block;">
                            <?= htmlspecialchars($r['waste_type']) ?>
                        </span>
                        <p style="margin: 0.5rem 0; font-weight: bold;"><?= date('l, M j', strtotime($r['collection_date'])) ?></p>
                        <p style="margin: 0.5rem 0; color: var(--text-muted);">Status: <?= $r['status'] ?></p>
                    </div>
                </div>

                <?php if ($r['status'] !== 'Completed'): ?>
                    <form method="POST" style="margin-top: 1rem;">
                        <input type="hidden" name="route_id" value="<?= $r['id'] ?>">
                        <?php if ($r['status'] === 'Pending'): ?>
                            <button name="status" value="In Progress" class="btn btn-primary" style="width: 100%; background: var(--warning);">Start Route</button>
                        <?php else: ?>
                            <button name="status" value="Completed" class="btn btn-primary" style="width: 100%; background: var(--secondary);">Mark Completed</button>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <div style="margin-top: 1rem; color: var(--secondary); font-weight: bold; text-align: center;">✓ Completed</div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <?php if(empty($routes)): ?>
            <div class="card" style="text-align: center; color: var(--text-muted);">
                <p>No active routes assigned.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
