<?php
// app/Controllers/Admin.php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuditModel;
use App\Models\BillingModel;

class Admin extends BaseController
{
    protected UserModel  $userModel;
    protected AuditModel $auditModel;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->auditModel = new AuditModel();
    }

    // ── Dashboard ────────────────────────────────────────
    public function dashboard()
{
    $billingModel = new BillingModel();

    $data = [
        'title'        => 'Admin Dashboard',
        'total_users'  => $this->userModel->where('role', 'user')->countAllResults(),
        'total_admins' => $this->userModel->where('role', 'admin')->countAllResults(),
        'total_bills'  => count($billingModel->getAll()),
        'total_audits' => $this->auditModel->countAll(),   // ← added
        'recent_audits'=> array_slice($this->auditModel->getAll(), 0, 5),
    ];

    return view('dashboard', $data);   // ← changed path
}

    // ── User Management ──────────────────────────────────
    public function users()
    {
        $data = [
            'title' => 'User Management',
            'users' => $this->userModel->getAllUsers(session()->get('user_id')),
        ];
        return view('admin/users', $data);
    }

    public function getUser(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }
        unset($user['password']);
        return $this->response->setJSON(['success' => true, 'user' => $user]);
    }

    public function storeUser()
    {
        $rules = [
            'username'  => 'required|is_unique[users.username]|min_length[3]',
            'full_name' => 'required|min_length[3]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'password'  => 'required|min_length[8]',
            'role'      => 'required|in_list[admin,user]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'username'  => $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $this->request->getPost('email'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'      => $this->request->getPost('role'),
            'is_active' => 1,
        ];

        $this->userModel->insert($data);

        $this->auditModel->log(
            session()->get('user_id'),
            'CREATE USER',
            'User Management',
            "Created user '{$data['username']}' with role '{$data['role']}'."
        );

        return $this->response->setJSON(['success' => true, 'message' => 'User created successfully.']);
    }

    public function updateUser()
    {
        $id = $this->request->getPost('id');

        $rules = [
            'username'  => "required|is_unique[users.username,id,{$id}]|min_length[3]",
            'full_name' => 'required|min_length[3]',
            'email'     => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'      => 'required|in_list[admin,user]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'username'  => $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name'),
            'email'     => $this->request->getPost('email'),
            'role'      => $this->request->getPost('role'),
            'is_active' => (int) $this->request->getPost('is_active'),
        ];

        // Update password only if provided
        $newPass = $this->request->getPost('password');
        if (!empty($newPass)) {
            $data['password'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $data);

        $this->auditModel->log(
            session()->get('user_id'),
            'UPDATE USER',
            'User Management',
            "Updated user ID {$id} ('{$data['username']}')."
        );

        return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully.']);
    }

    public function deleteUser()
    {
        $id   = $this->request->getPost('id');
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        // Prevent self-deletion
        if ($id == session()->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete your own account.']);
        }

        $this->userModel->delete($id);

        $this->auditModel->log(
            session()->get('user_id'),
            'DELETE USER',
            'User Management',
            "Deleted user '{$user['username']}' (ID: {$id})."
        );

        return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully.']);
    }

    // ── Audit Trails ─────────────────────────────────────
    public function audit()
    {
        return view('admin/audit', ['title' => 'Audit Trails']);
    }

    public function auditData()
    {
        $trails = $this->auditModel->getAll();
        return $this->response->setJSON(['data' => $trails]);
    }
}