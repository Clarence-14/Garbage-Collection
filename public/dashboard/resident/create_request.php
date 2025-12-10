<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
require_once __DIR__ . '/../../../src/Models/ServiceRequest.php';
use Src\Auth\Auth;
use Src\Models\ServiceRequest;

Auth::requireRole('resident');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        <h1>Submit Service Request</h1>
        <a href="/dashboard/resident/index.php" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Dashboard</a>
    </div>
    
    <div class="card" style="max-width: 600px; margin-top: 2rem;">
        <?php if ($success): ?>
            <div style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <?= $success ?>
            </div>
            <a href="/dashboard/resident/index.php" class="btn btn-primary">Back to Dashboard</a>
        <?php else: ?>
            
            <?php if($error): ?>
                <div style="color: var(--danger); margin-bottom: 1rem;"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Issue Type</label>
                    <select name="type" class="form-control">
                        <option value="Missed Pickup">Missed Pickup</option>
                        <option value="Bin Damage">Bin Damage</option>
                        <option value="Bulk Pickup">Request Bulk Pickup</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Please provide details..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
