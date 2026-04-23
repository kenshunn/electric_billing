<?php
// app/Views/admin/audit.php
$this->extend('layouts/main');
$this->section('content');
?>

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-header bg-white border-bottom py-3" style="border-radius:14px 14px 0 0;">
        <h6 class="mb-0 fw-700"><i class="bi bi-journal-text text-primary me-2"></i>System Audit Trails</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="auditTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th><th>User</th><th>Role</th><th>Action</th>
                        <th>Module</th><th>Description</th><th>IP Address</th><th>Date & Time</th>
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

    $('#auditTable').DataTable({
        ajax: {
            url: BASE + 'admin/audit/data',
            dataSrc: 'data'
        },
        order: [[7, 'desc']],
        columns: [
            { data: null, render: (d, t, r, meta) => meta.row + 1 },
            { data: 'full_name', render: d => `<span class="fw-600">${d}</span>` },
            { data: 'role', render: d => `<span class="badge rounded-pill ${d==='admin'?'badge-role-admin':'badge-role-user'}">${d.charAt(0).toUpperCase()+d.slice(1)}</span>` },
            { data: 'action', render: d => `<span class="badge bg-primary bg-opacity-10 text-primary">${d}</span>` },
            { data: 'module' },
            { data: 'description', render: d => `<span class="text-muted small">${d}</span>` },
            { data: 'ip_address', render: d => `<code class="small">${d || 'N/A'}</code>` },
            { data: 'created_at', render: d => {
                const dt = new Date(d);
                return dt.toLocaleDateString('en-PH', {year:'numeric',month:'short',day:'numeric'}) + ' ' +
                       dt.toLocaleTimeString('en-PH', {hour:'2-digit',minute:'2-digit'});
            }},
        ]
    });
});
</script>
<?php $this->endSection(); ?>