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

// Handle Form Submit (Create or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $userModel->createUser($_POST);
            redirect('/dashboard/admin/users.php?msg=created');
        } elseif ($_POST['action'] === 'update') {
            // "edit_id" comes from the hidden input
            $id = $_POST['edit_id'];
            // Prepare data: if password is empty, logic in User model handles it (skips update)
            $data = [
                'username' => $_POST['username'],
                'full_name' => $_POST['full_name'],
                'email' => $_POST['email'],
                'role' => $_POST['role'],
                'address' => $_POST['address'],
                'phone' => $_POST['phone'],
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
            <h1>User Management</h1>
            <a href="/dashboard/admin/index.php" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Dashboard</a>
        </div>
        <button onclick="openCreateModal()" class="btn btn-primary">Add New User</button>
    </div>

    <div class="card" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid #334155;">
                    <th style="padding: 1rem;">Role</th>
                    <th style="padding: 1rem;">Username</th>
                    <th style="padding: 1rem;">Full Name</th>
                    <th style="padding: 1rem;">Email</th>
                    <th style="padding: 1rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr style="border-bottom: 1px solid #1e293b;">
                        <td style="padding: 1rem;">
                            <span style="padding: 0.25rem 0.5rem; border-radius: 0.25rem; background: <?= $u['role'] === 'admin' ? 'var(--primary)' : ($u['role'] === 'driver' ? 'var(--warning)' : 'var(--secondary)') ?>; font-size: 0.8rem;">
                                <?= strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($u['username']) ?></td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($u['full_name']) ?></td>
                        <td style="padding: 1rem;"><?= htmlspecialchars($u['email']) ?></td>
                        <td style="padding: 1rem;">
                            <!-- Edit Button triggers modal with data -->
                            <button 
                                onclick='openEditModal(<?= json_encode($u) ?>)'
                                class="btn" 
                                style="padding: 0.25rem 0.5rem; font-size: 0.8rem; background: #334155; margin-right: 0.5rem;">
                                Edit
                            </button>

                            <?php if($u['id'] !== $_SESSION['user_id']): ?>
                                <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Are you sure?')" style="color: var(--danger);">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal (Shared for Create/Edit) -->
<div id="userModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index: 1000; align-items:center; justify-content:center;">
    <div class="card" style="width: 100%; max-width: 500px; max-height: 90vh; overflow-y:auto;">
        <h2 class="mb-4" id="modalTitle">Create New User</h2>
        <form method="POST" id="userForm">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="edit_id" id="editId" value="">

            <div class="form-group">
                <label>Role</label>
                <select name="role" id="roleInput" class="form-control">
                    <option value="resident">Resident</option>
                    <option value="driver">Driver</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="usernameInput" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" id="fullnameInput" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="emailInput" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password <span id="passwordHint" style="font-size: 0.8rem; color: var(--text-muted); display:none;">(Leave blank to keep current)</span></label>
                <input type="password" name="password" id="passwordInput" class="form-control">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" id="phoneInput" class="form-control">
            </div>
            <div class="form-group">
                <label>Address (Optional)</label>
                <input type="text" name="address" id="addressInput" class="form-control">
            </div>
            <div style="display:flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                <button type="button" class="btn" onclick="closeModal()" style="background: transparent; border: 1px solid #334155; color: white;">Cancel</button>
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
