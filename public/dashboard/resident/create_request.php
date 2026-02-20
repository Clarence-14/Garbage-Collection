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
    
    if ($type && $description) {
        $reqModel = new ServiceRequest();
        $reqModel->createRequest($_SESSION['user_id'], $type, $description);
        $success = "Request submitted successfully.";
    } else {
        $error = "Please fill all fields.";
    }
}

include __DIR__ . '/../../../templates/header.php';
?>

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
                    <label style="color: white;">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Please provide details..." style="resize: vertical;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Submit Request</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
