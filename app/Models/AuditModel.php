<?php
// app/Models/AuditModel.php
namespace App\Models;

use CodeIgniter\Model;

class AuditModel extends Model
{
    protected $table      = 'audit_trails';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id', 'action', 'module', 'description', 'ip_address'
    ];

    protected $useTimestamps = false; // we use created_at manually

    // ── Log an action ────────────────────────────────────
    public function log(int $userId, string $action, string $module, string $description): void
    {
        $request = \Config\Services::request();

        $this->insert([
            'user_id'    => $userId,
            'action'     => $action,
            'module'     => $module,
            'description'=> $description,
            'ip_address' => $request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // ── All trails (admin) ───────────────────────────────
    public function getAll()
    {
        return $this->db->table('audit_trails a')
            ->select('a.*, u.full_name, u.username, u.role')
            ->join('users u', 'u.id = a.user_id')
            ->orderBy('a.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // ── Trails by user ───────────────────────────────────
    public function getByUser(int $userId)
    {
        return $this->db->table('audit_trails a')
            ->select('a.*, u.full_name, u.username')
            ->join('users u', 'u.id = a.user_id')
            ->where('a.user_id', $userId)
            ->orderBy('a.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}