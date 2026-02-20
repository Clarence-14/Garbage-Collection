<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
use Src\Auth\Auth;

Auth::requireRole('resident');

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1 style="color: white; margin: 0;">Billing & Payments</h1>
        <a href="/dashboard/resident/index.php" class="btn" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--glass-border); padding: 0.5rem 1rem;">&larr; Back to Dashboard</a>
    </div>
    
    <div class="card glass" style="max-width: 600px; margin-top: 2rem; margin-left: auto; margin-right: auto;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="color: var(--text-muted); font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Current Period Balance</h2>
            <div style="font-size: 3.5rem; font-weight: 700; color: white; line-height: 1;">$45.00</div>
            <p style="color: var(--warning); margin-top: 1rem; font-weight: 500;">Due Date: <?= date('F j, Y', strtotime('+15 days')) ?></p>
        </div>
        
        <hr style="border-top: 1px solid var(--glass-border); border-bottom: none; border-left: none; border-right: none; margin: 2rem 0;">
        
        <h3 style="color: white; font-size: 1.1rem; margin-bottom: 1rem;">Recent Transactions</h3>
        <table style="width: 100%; text-align: left; color: var(--text-muted); border-collapse: collapse;">
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 1rem 0;">
                    <div style="color: white; font-weight: 500;">Last Month Bill</div>
                    <div style="font-size: 0.85rem;">Generated on <?= date('M j, Y', strtotime('-1 month')) ?></div>
                </td>
                <td style="text-align: right; color: white; font-weight: 600;">$45.00</td>
            </tr>
            <tr>
                <td style="padding: 1rem 0;">
                    <div style="color: #4ade80; font-weight: 500;">Payment Received</div>
                    <div style="font-size: 0.85rem;">Processed on <?= date('M j, Y') ?></div>
                </td>
                <td style="text-align: right; color: #4ade80; font-weight: 600;">-$45.00</td>
            </tr>
        </table>
        
        <div style="margin-top: 2.5rem;">
            <button class="btn btn-primary" style="width: 100%; opacity: 0.6; cursor: not-allowed; background: rgba(255,255,255,0.1); box-shadow: none; border: 1px dashed rgba(255,255,255,0.2);">Pay Online (Coming Soon)</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
