<?php
// app/Views/user/history.php
$this->extend('layouts/main');
$this->section('content');
?>

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-header bg-white border-bottom py-3" style="border-radius:14px 14px 0 0;">
        <h6 class="mb-0 fw-700"><i class="bi bi-clock-history text-primary me-2"></i>My Billing History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="historyTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th><th>Client</th><th>Client No.</th><th>Meter No.</th>
                        <th>Billing Month</th><th>Prev (KW)</th><th>Curr (KW)</th>
                        <th>Consumption</th><th>Amount Due</th><th>Due Date</th><th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>
<?php $this->section('scripts'); ?>
<script>
$(function () {
    const BASE = '<?= base_url() ?>';

    $('#historyTable').DataTable({
        ajax: { url: BASE + 'user/history/data', dataSrc: 'data' },
        order: [[0, 'desc']],
        columns: [
            { data: null, render: (d, t, r, meta) => meta.row + 1 },
            { data: 'client_name', render: d => `<span class="fw-600">${d}</span>` },
            { data: 'client_no' },
            { data: 'meter_no', render: d => `<code>${d}</code>` },
            { data: 'billing_month' },
            { data: 'prev_reading',   render: d => parseFloat(d).toFixed(2) },
            { data: 'curr_reading',   render: d => parseFloat(d).toFixed(2) },
            { data: 'consumption_kw', render: d => `<span class="fw-600">${parseFloat(d).toFixed(2)} KW</span>` },
            { data: 'amount_due',     render: d => `<span class="fw-700 text-primary">₱${parseFloat(d).toLocaleString('en-PH',{minimumFractionDigits:2})}</span>` },
            { data: 'due_date',       render: d => new Date(d).toLocaleDateString('en-PH',{year:'numeric',month:'short',day:'numeric'}) },
            { data: 'status', render: d => {
                const map = {unpaid:'warning',paid:'success',overdue:'danger'};
                const c = map[d] || 'secondary';
                return `<span class="badge bg-${c} bg-opacity-15 text-${c}">${d.charAt(0).toUpperCase()+d.slice(1)}</span>`;
            }},
        ]
    });
});
</script>
<?php $this->endSection(); ?>