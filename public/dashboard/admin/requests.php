<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
require_once __DIR__ . '/../../../src/Models/ServiceRequest.php';
use Src\Auth\Auth;
use Src\Models\ServiceRequest;

Auth::requireRole('admin');

$requestModel = new ServiceRequest();
$requests = $requestModel->getAllRequests();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Src\Security\CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        die("CSRF Token Validation Failed.");
    }
    $id = (int)($_POST['id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');
    $notes = sanitize($_POST['admin_notes'] ?? '');

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
            <h1 style="color: white; margin: 0;">Service Requests</h1>
            <a href="/dashboard/admin/index.php" class="btn mt-2" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--glass-border); padding: 0.5rem 1rem;">&larr; Back to Dashboard</a>
        </div>
    </div>

    <div class="grid grid-cols-1 mt-4">
        <?php foreach ($requests as $r): ?>
            <?php 
                $borderColor = $r['status'] === 'Open' ? 'rgba(234, 179, 8, 0.5)' : ($r['status'] === 'Resolved' ? 'rgba(34, 197, 94, 0.5)' : 'rgba(255,255,255,0.2)');
                $statusBg = $r['status'] === 'Open' ? 'rgba(234, 179, 8, 0.15)' : ($r['status'] === 'Resolved' ? 'rgba(34, 197, 94, 0.15)' : 'rgba(255,255,255,0.1)');
                $statusColor = $r['status'] === 'Open' ? '#fde047' : ($r['status'] === 'Resolved' ? '#4ade80' : 'var(--text-muted)');
            ?>
            <div class="card glass" style="border-left: 4px solid <?= $borderColor ?>; padding: 1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:start;">
                    <div style="flex: 1; padding-right: 2rem;">
                        <h3 style="margin: 0; color: white; margin-bottom: 0.5rem;"><?= htmlspecialchars($r['request_type']) ?></h3>
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">
                            By: <span style="color: var(--secondary);"><?= htmlspecialchars($r['full_name']) ?></span> (@<?= htmlspecialchars($r['username']) ?>) <br>
                            Submitted: <?= htmlspecialchars($r['created_at']) ?>
                        </p>
                        
                        <div style="margin-top: 1.5rem; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05); padding: 1rem; border-radius: 0.5rem;">
                            <p style="margin: 0; color: white; line-height: 1.5;"><?= nl2br(htmlspecialchars($r['description'])) ?></p>
                        </div>
                        
                        <?php if($r['admin_notes']): ?>
                            <div style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); padding: 0.75rem 1rem; margin-top: 1rem; border-radius: 0.5rem;">
                                <strong style="color: #60a5fa;">Admin Note:</strong> <span style="color: white;"><?= htmlspecialchars($r['admin_notes']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="text-align: right; min-width: 250px;">
                        <div style="margin-bottom: 1.5rem;">
                            <span style="font-weight: 500; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; border: 1px solid <?= $borderColor ?>; background: <?= $statusBg ?>; color: <?= $statusColor ?>; backdrop-filter: blur(4px); display:inline-block;">
                                <?= $r['status'] ?>
                            </span>
                        </div>
                        
                        <?php if($r['status'] !== 'Resolved' && $r['status'] !== 'Rejected'): ?>
                            <form method="POST" style="display: flex; flex-direction: column; gap: 0.75rem; background: rgba(255,255,255,0.03); padding: 1rem; border: 1px solid rgba(255,255,255,0.05); border-radius: 0.75rem;">
                                <?= \Src\Security\CSRF::getField() ?>
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <div>
                                    <label style="color: var(--text-muted); font-size: 0.8rem; display: block; text-align: left; margin-bottom: 0.25rem;">Resolve Note (Optional)</label>
                                    <textarea name="admin_notes" placeholder="Reason or outcome..." class="form-control" rows="2" style="font-size: 0.9rem; resize: vertical; margin-bottom: 0;"></textarea>
                                </div>
                                <div style="display:flex; gap: 0.5rem;">
                                    <button type="submit" name="status" value="Resolved" class="btn btn-primary" style="padding: 0.5rem; font-size: 0.85rem; background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); width: 100%; transition: all 0.2s;" onmouseover="this.style.background='rgba(34, 197, 94, 0.3)'" onmouseout="this.style.background='rgba(34, 197, 94, 0.2)'">Resolve</button>
                                    <button type="submit" name="status" value="Rejected" class="btn" style="padding: 0.5rem; font-size: 0.85rem; background: rgba(239, 68, 68, 0.15); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3); width: 100%; transition: all 0.2s;" onmouseover="this.style.background='rgba(239, 68, 68, 0.25)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.15)'">Reject</button>
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
