<?php
// app/Filters/AuthFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session(); //store the current session if logged in or not

        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to continue.'); 
        }

        // archived checker
        $userModel = new \App\Models\UserModel();
        $user      = $userModel->find(session()->get('user_id'));

        if (!$user || $user['is_active'] == 0) {
            session()->destroy();
            return redirect()->to('/login')
                            ->with('error', 'Your account has been archived. Contact your administrator.');
        }

        // Role check
        if (!empty($arguments)) { //if the user is logged in, checks for its role to redirect to the corresponding dashboard
            $requiredRole = $arguments[0];
            if ($session->get('role') !== $requiredRole) {
                // Redirect to their correct dashboard
                $role = $session->get('role');
                return redirect()->to("/{$role}/dashboard") //if the user attempts to enter an admin/dashboard, the access will be denied vice versa.
                                 ->with('error', 'Access denied.'); 
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing needed after
    }
}