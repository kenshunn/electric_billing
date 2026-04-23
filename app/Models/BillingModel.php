<?php
// app/Models/BillingModel.php
namespace App\Models;

use CodeIgniter\Model;

class BillingModel extends Model
{
    protected $table      = 'billings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'client_id', 'computed_by', 'billing_month',
        'prev_reading', 'curr_reading', 'consumption_kw',
        'amount_due', 'due_date', 'status', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ── Tiered rate computation ──────────────────────────
    public function computeAmount(float $consumption): float
    {
        $amount = 0.0;

        if ($consumption <= 0) {
            return 0.0;
        } elseif ($consumption <= 200) {
            $amount = $consumption * 10.00;
        } elseif ($consumption <= 500) {
            $amount  = 200 * 10.00;                         // first 200 KW
            $amount += ($consumption - 200) * 13.00;        // next portion
        } else {
            $amount  = 200 * 10.00;                         // first 200 KW
            $amount += 300 * 13.00;                         // next 300 KW
            $amount += ($consumption - 500) * 15.00;        // beyond 500 KW
        }

        return round($amount, 2);
    }

    // ── Billings by a specific user (with client info) ───
    public function getByUser(int $userId)
    {
        return $this->db->table('billings b')
            ->select('b.*, c.full_name AS client_name, c.client_no, c.meter_no, u.full_name AS computed_by_name')
            ->join('clients c', 'c.id = b.client_id')
            ->join('users u',   'u.id = b.computed_by')
            ->where('b.computed_by', $userId)
            ->orderBy('b.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    // ── All billings (admin view) ────────────────────────
    public function getAll()
    {
        return $this->db->table('billings b')
            ->select('b.*, c.full_name AS client_name, c.client_no, c.meter_no, u.full_name AS computed_by_name')
            ->join('clients c', 'c.id = b.client_id')
            ->join('users u',   'u.id = b.computed_by')
            ->orderBy('b.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}