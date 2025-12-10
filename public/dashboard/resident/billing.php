<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
use Src\Auth\Auth;

Auth::requireRole('resident');

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1>Billing & Payments</h1>
        <a href="/dashboard/resident/index.php" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Dashboard</a>
    </div>
    
    <div class="card" style="max-width: 600px; margin-top: 2rem;">
        <h2 style="color: var(--text-muted); margin-top: 0;">Current Period</h2>
        <div style="font-size: 3rem; font-weight: bold; color: var(--text-main);">$45.00</div>
        <p style="color: var(--warning);">Due Date: <?= date('F j, Y', strtotime('+15 days')) ?></p>
        
        <hr style="border-color: #334155; margin: 2rem 0;">
        
        <h3>Recent Transactions</h3>
        <table style="width: 100%; text-align: left; color: var(--text-muted);">
            <tr>
                <td style="padding: 0.5rem 0;">Last Month Bill</td>
                <td style="text-align: right;">$45.00</td>
            </tr>
            <tr>
                <td style="padding: 0.5rem 0;">Payment - <?= date('M j') ?></td>
                <td style="text-align: right; color: var(--secondary);">-$45.00</td>
            </tr>
        </table>
        
        <div style="margin-top: 2rem;">
            <button class="btn btn-primary" style="width: 100%; opacity: 0.7; cursor: not-allowed;">Pay Online (Coming Soon)</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
