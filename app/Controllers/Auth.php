<?php
// app/Controllers/Auth.php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuditModel;

class Auth extends BaseController
{
    public function index() 
    {
        // Already logged in → redirect to dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/' . session()->get('role') . '/dashboard'); // if the user is logged it, retrieves the role to redirect to the corresponding dashboard
        }
        return view('auth/login'); 
    }

    public function login() //basic login
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Invalid username or password.');
        }

        // Set session
        session()->set([
            'logged_in' => true,
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'full_name' => $user['full_name'],
            'role'      => $user['role'],
        ]);

        // Audit log
        $audit = new AuditModel();
        $audit->log($user['id'], 'LOGIN', 'Auth', "User '{$user['username']}' logged in.");

        return redirect()->to('/' . $user['role'] . '/dashboard');
    }

    public function logout()
    {
        if (session()->get('logged_in')) {
            $audit = new AuditModel();
            $audit->log(
                session()->get('user_id'),
                'LOGOUT',
                'Auth',
                "User '" . session()->get('username') . "' logged out."
            );
        }

        session()->destroy();
        return redirect()->to('/login')->with('success', 'Logged out successfully.');
    }
}