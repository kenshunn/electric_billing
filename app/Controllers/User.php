<?php
// app/Controllers/User.php
namespace App\Controllers;

use App\Models\BillingModel;
use App\Models\ClientModel;
use App\Models\AuditModel;

class User extends BaseController
{
    protected BillingModel $billingModel;
    protected ClientModel  $clientModel;
    protected AuditModel   $auditModel;

    public function __construct()
    {
        $this->billingModel = new BillingModel();
        $this->clientModel  = new ClientModel();
        $this->auditModel   = new AuditModel();
    }

    // ── Dashboard ────────────────────────────────────────
    public function dashboard()
    {
    $uid   = session()->get('user_id');
    $bills = $this->billingModel->getByUser($uid);

    $data = [
        'title'        => 'My Dashboard',
        'total_bills'  => count($bills),
        'total_amount' => array_sum(array_column($bills, 'amount_due')),
        'unpaid_count' => count(array_filter($bills, fn($b) => $b['status'] === 'unpaid')),
        'recent_bills' => array_slice($bills, 0, 5),
    ];

    return view('dashboard', $data);   // ← changed path
    }

    // ── Billing Computation ──────────────────────────────
    public function billing()
    {
        return view('user/billing', ['title' => 'Compute Bill']);
    }

    public function getClients()
    {
        $clients = $this->clientModel->findAll();
        return $this->response->setJSON($clients);
    }

    public function computeBill()
    {
        $rules = [
            'client_id'     => 'required|is_natural_no_zero',
            'billing_month' => 'required',
            'prev_reading'  => 'required|decimal',
            'curr_reading'  => 'required|decimal',
            'due_date'      => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $prev = (float) $this->request->getPost('prev_reading');
        $curr = (float) $this->request->getPost('curr_reading');

        if ($curr < $prev) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => ['curr_reading' => 'Current reading must be greater than or equal to previous reading.'],
            ]);
        }

        $consumption = $curr - $prev;
        $amount      = $this->billingModel->computeAmount($consumption);

        $data = [
            'client_id'     => (int)  $this->request->getPost('client_id'),
            'computed_by'   => session()->get('user_id'),
            'billing_month' => $this->request->getPost('billing_month'),
            'prev_reading'  => $prev,
            'curr_reading'  => $curr,
            'consumption_kw'=> $consumption,
            'amount_due'    => $amount,
            'due_date'      => $this->request->getPost('due_date'),
            'status'        => 'unpaid',
            'notes'         => $this->request->getPost('notes') ?? '',
        ];

        $this->billingModel->insert($data);

        // Get client name for audit
        $client = $this->clientModel->find($data['client_id']);

        $this->auditModel->log(
            session()->get('user_id'),
            'COMPUTE BILL',
            'Billing',
            "Computed bill for client '{$client['full_name']}' ({$client['client_no']}) — " .
            "{$consumption} KW — ₱" . number_format($amount, 2) . " for {$data['billing_month']}."
        );

        return $this->response->setJSON([
            'success'     => true,
            'message'     => 'Bill computed and saved successfully.',
            'consumption' => $consumption,
            'amount'      => $amount,
        ]);
    }

    // ── Billing History ──────────────────────────────────
    public function history()
    {
        return view('user/history', ['title' => 'My Billing History']);
    }

    public function historyData()
    {
        $bills = $this->billingModel->getByUser(session()->get('user_id'));
        return $this->response->setJSON(['data' => $bills]);
    }

    // ── My Action Trails ─────────────────────────────────
    public function trails()
    {
        return view('user/trails', ['title' => 'My Action Trails']);
    }

    public function trailsData()
    {
        $trails = $this->auditModel->getByUser(session()->get('user_id'));
        return $this->response->setJSON(['data' => $trails]);
    }
}