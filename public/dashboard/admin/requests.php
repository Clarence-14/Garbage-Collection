<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
require_once __DIR__ . '/../../../src/Models/ServiceRequest.php';
use Src\Auth\Auth;
use Src\Models\ServiceRequest;

Auth::requireRole('admin');

$requestModel = new ServiceRequest();
$requests = $requestModel->getAllRequests();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;
    $notes = $_POST['admin_notes'] ?? '';

    if ($id && $status) {
        $requestModel->updateStatus($id, $status, $notes);
        redirect('/dashboard/admin/requests.php?msg=updated');
    }
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <h1>Service Requests</h1>
            <a href="/dashboard/admin/index.php" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Dashboard</a>
        </div>
    </div>

    <div class="grid grid-cols-1">
        <?php foreach ($requests as $r): ?>
            <div class="card" style="border-left: 5px solid <?= $r['status'] === 'Open' ? 'var(--warning)' : ($r['status'] === 'Resolved' ? 'var(--secondary)' : 'var(--text-muted)') ?>;">
                <div style="display:flex; justify-content:space-between; align-items:start;">
                    <div>
                        <h4 style="margin: 0; color: var(--primary);"><?= htmlspecialchars($r['request_type']) ?></h4>
                        <p style="margin: 0.25rem 0; color: var(--text-muted);">
                            By: <?= htmlspecialchars($r['full_name']) ?> (<?= htmlspecialchars($r['username']) ?>) <br>
                            <?= htmlspecialchars($r['created_at']) ?>
                        </p>
                        <p style="margin-top: 1rem;"><?= nl2br(htmlspecialchars($r['description'])) ?></p>
                        
                        <?php if($r['admin_notes']): ?>
                            <div style="background: rgba(0,0,0,0.2); padding: 0.5rem; margin-top: 0.5rem; border-radius: 0.25rem;">
                                <strong>Admin Note:</strong> <?= htmlspecialchars($r['admin_notes']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="text-align: right;">
                        <span style="font-weight: bold; padding: 0.25rem 0.5rem; border-radius: 0.25rem; background: #334155; display:inline-block; margin-bottom: 1rem;">
                            <?= $r['status'] ?>
                        </span>
                        
                        <?php if($r['status'] !== 'Resolved' && $r['status'] !== 'Rejected'): ?>
                            <form method="POST" style="display: flex; flex-direction: column; gap: 0.5rem; width: 200px;">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <textarea name="admin_notes" placeholder="Add note..." class="form-control" rows="1" style="font-size: 0.8rem;"></textarea>
                                <div style="display:flex; gap: 0.5rem;">
                                    <button type="submit" name="status" value="Resolved" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; background: var(--secondary); width: 100%;">Resolve</button>
                                    <button type="submit" name="status" value="Rejected" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; background: var(--danger); color: white; width: 100%;">Reject</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
