<?php
require_once __DIR__ . '/../../../src/bootstrap.php';
require_once __DIR__ . '/../../../src/Models/User.php';
use Src\Auth\Auth;
use Src\Models\User;

Auth::requireRole('admin');

$userModel = new User();
$users = $userModel->getAllUsers();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== $_SESSION['user_id']) { // Prevent self-delete
        $userModel->deleteUser($id);
        redirect('/dashboard/admin/users.php?msg=deleted');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!\Src\Security\CSRF::verifyToken($_POST['csrf_token'] ?? '')) {
        die("CSRF Token Validation Failed.");
    }
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $data = [
                'username' => sanitize($_POST['username']),
                'full_name' => sanitize($_POST['full_name']),
                'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'role' => sanitize($_POST['role']),
                'address' => sanitize($_POST['address']),
                'phone' => sanitize($_POST['phone']),
                'password' => $_POST['password'] 
            ];
            $userModel->createUser($data);
            redirect('/dashboard/admin/users.php?msg=created');
        } elseif ($_POST['action'] === 'update') {
            // "edit_id" comes from the hidden input
            $id = (int)$_POST['edit_id'];
            // Prepare data: if password is empty, logic in User model handles it (skips update)
            $data = [
                'username' => sanitize($_POST['username']),
                'full_name' => sanitize($_POST['full_name']),
                'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'role' => sanitize($_POST['role']),
                'address' => sanitize($_POST['address']),
                'phone' => sanitize($_POST['phone']),
                'password' => $_POST['password'] // can be empty
            ];
            $userModel->updateUser($id, $data);
            redirect('/dashboard/admin/users.php?msg=updated');
        }
    }
}

include __DIR__ . '/../../../templates/header.php';
?>

<div class="container mt-4">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <div>
            <h1 style="color: white; margin: 0;">User Management</h1>
            <a href="/dashboard/admin/index.php" class="btn mt-2" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--glass-border); padding: 0.5rem 1rem;">&larr; Back to Dashboard</a>
        </div>
        <button onclick="openCreateModal()" class="btn btn-primary" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);">Add New User</button>
    </div>

    <div class="card glass" style="overflow-x: auto; padding: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; color: white;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Role</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Username</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Full Name</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Email</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 500;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1rem;">
                            <span style="padding: 0.35rem 0.65rem; border-radius: 9999px; background: rgba(255,255,255,0.1); border: 1px solid <?= $u['role'] === 'admin' ? 'rgba(34, 197, 94, 0.4)' : ($u['role'] === 'driver' ? 'rgba(234, 179, 8, 0.4)' : 'rgba(168, 85, 247, 0.4)') ?>; color: <?= $u['role'] === 'admin' ? '#4ade80' : ($u['role'] === 'driver' ? '#fde047' : '#c084fc') ?>; font-size: 0.8rem; font-weight: 500; backdrop-filter: blur(4px);">
                                <?= strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($u['username']) ?></td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($u['full_name']) ?></td>
                        <td style="padding: 1rem; color: var(--text-muted);"><?= htmlspecialchars($u['email']) ?></td>
                        <td style="padding: 1rem;">
                            <!-- Edit Button triggers modal with data -->
                            <button 
                                onclick='openEditModal(<?= json_encode($u) ?>)'
                                class="btn" 
                                style="padding: 0.35rem 0.75rem; font-size: 0.85rem; background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); margin-right: 0.5rem; transition: all 0.2s;"
                                onmouseover="this.style.background='rgba(59, 130, 246, 0.3)'" onmouseout="this.style.background='rgba(59, 130, 246, 0.2)'">
                                Edit
                            </button>

                            <?php if($u['id'] !== $_SESSION['user_id']): ?>
                                <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Are you sure?')" class="btn" style="padding: 0.35rem 0.75rem; font-size: 0.85rem; background: rgba(239, 68, 68, 0.1); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3); transition: all 0.2s;" onmouseover="this.style.background='rgba(239, 68, 68, 0.2)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal (Shared for Create/Edit) -->
<div id="userModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px); z-index: 1000; align-items:center; justify-content:center;">
    <div class="card glass" style="width: 100%; max-width: 500px; max-height: 90vh; overflow-y:auto; border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <h2 class="mb-4" id="modalTitle" style="color: white; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Create New User</h2>
        <form method="POST" id="userForm">
            <?= \Src\Security\CSRF::getField() ?>
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="edit_id" id="editId" value="">

            <div class="form-group mt-4">
                <label style="color: white;">Role</label>
                <select name="role" id="roleInput" class="form-control" style="appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22white%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                    <option value="resident" style="color: black;">Resident</option>
                    <option value="driver" style="color: black;">Driver</option>
                    <option value="admin" style="color: black;">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label style="color: white;">Username</label>
                <input type="text" name="username" id="usernameInput" class="form-control" required>
            </div>
            <div class="form-group">
                <label style="color: white;">Full Name</label>
                <input type="text" name="full_name" id="fullnameInput" class="form-control" required>
            </div>
            <div class="form-group">
                <label style="color: white;">Email</label>
                <input type="email" name="email" id="emailInput" class="form-control" required>
            </div>
            <div class="form-group">
                <label style="color: white;">Password <span id="passwordHint" style="font-size: 0.8rem; color: var(--warning); display:none; margin-left: 0.5rem; font-weight: normal;">(Leave blank to keep current)</span></label>
                <input type="password" name="password" id="passwordInput" class="form-control">
            </div>
            <div class="form-group">
                <label style="color: white;">Phone</label>
                <input type="text" name="phone" id="phoneInput" class="form-control">
            </div>
            <div class="form-group">
                <label style="color: white;">Address (Optional)</label>
                <input type="text" name="address" id="addressInput" class="form-control">
            </div>
            <div style="display:flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn" onclick="closeModal()" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Create User</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('userModal').style.display = 'flex';
    document.getElementById('modalTitle').innerText = 'Create New User';
    document.getElementById('formAction').value = 'create';
    document.getElementById('submitBtn').innerText = 'Create User';
    document.getElementById('passwordInput').required = true;
    document.getElementById('passwordHint').style.display = 'none';
    
    // Clear form
    document.getElementById('userForm').reset();
    document.getElementById('formAction').value = 'create'; // Reset again just in case
}

function openEditModal(user) {
    document.getElementById('userModal').style.display = 'flex';
    document.getElementById('modalTitle').innerText = 'Edit User';
    document.getElementById('formAction').value = 'update';
    document.getElementById('editId').value = user.id;
    document.getElementById('submitBtn').innerText = 'Update User';
    document.getElementById('passwordInput').required = false;
    document.getElementById('passwordHint').style.display = 'inline';

    // Populate fields
    document.getElementById('roleInput').value = user.role;
    document.getElementById('usernameInput').value = user.username;
    document.getElementById('fullnameInput').value = user.full_name;
    document.getElementById('emailInput').value = user.email;
    document.getElementById('phoneInput').value = user.phone || '';
    document.getElementById('addressInput').value = user.address || '';
    document.getElementById('passwordInput').value = '';
}

function closeModal() {
    document.getElementById('userModal').style.display = 'none';
}
</script>

<?php include __DIR__ . '/../../../templates/footer.php'; ?>
