<?php
// app/Models/UserModel.php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'username', 'password', 'full_name', 'email', 'role', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ── Find by username for login ──────────────────────
    public function findByUsername(string $username)
    {
        return $this->where('username', $username)
                    ->where('is_active', 1)
                    ->first();
    }

    // ── All users except the current session user ────────
    public function getAllUsers(int $excludeId = 0)
    {
        return $this->where('id !=', $excludeId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}