<?php
// app/Views/dashboard.php
// Used by BOTH Admin and User — role-based content via session
$this->extend('layouts/main');
$this->section('content');
$role = session()->get('role');
?>

<!-- ── Stat Cards ──────────────────────────────────────────── -->
<div class="row g-4 mb-4">

    <?php if ($role === 'admin'): ?>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;">
                        <i class="bi bi-people-fill" style="color:#1d4ed8;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-500">Normal Users</div>
                        <div class="fs-3 fw-700 lh-1"><?= $total_users ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#ede9fe;">
                        <i class="bi bi-shield-fill-check" style="color:#6d28d9;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-500">Admin Users</div>
                        <div class="fs-3 fw-700 lh-1"><?= $total_admins ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#d1fae5;">
                        <i class="bi bi-receipt" style="color:#065f46;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-500">Total Bills</div>
                        <div class="fs-3 fw-700 lh-1"><?= $total_bills ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7;">
                        <i class="bi bi-journal-text" style="color:#92400e;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-500">Audit Logs</div>
                        <div class="fs-3 fw-700 lh-1"><?= $total_audits ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>

        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;">
                        <i class="bi bi-receipt" style="color:#1d4ed8;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-500">Bills Computed</div>
                        <div class="fs-3 fw-700 lh-1"><?= $total_bills ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#d1fae5;">
                        <i class="bi bi-currency-exchange" style="color:#065f46;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-500">Total Billed Amount</div>
                        <div class="fs-3 fw-700 lh-1">₱<?= number_format($total_amount ?? 0, 2) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7;">
                        <i class="bi bi-exclamation-triangle-fill" style="color:#92400e;"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-500">Unpaid Bills</div>
                        <div class="fs-3 fw-700 lh-1"><?= $unpaid_count ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- ── Recent Activity Table ───────────────────────────────── -->
<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3"
         style="border-radius:14px 14px 0 0;">

        <?php if ($role === 'admin'): ?>
            <h6 class="mb-0 fw-700">
                <i class="bi bi-clock-history text-primary me-2"></i>Recent Activity
            </h6>
            <a href="<?= base_url('admin/audit') ?>" class="btn btn-sm btn-outline-primary">View All</a>
        <?php else: ?>
            <h6 class="mb-0 fw-700">
                <i class="bi bi-clock-history text-primary me-2"></i>Recent Bills
            </h6>
            <a href="<?= base_url('user/history') ?>" class="btn btn-sm btn-outline-primary">View All</a>
        <?php endif; ?>

    </div>
    <div class="card-body p-0">
        <div class="table-responsive">

            <?php if ($role === 'admin'): ?>
                <!-- Admin: Audit trail table -->
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_audits)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>No activity yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_audits as $a): ?>
                            <tr>
                                <td><span class="fw-600"><?= esc($a['full_name']) ?></span></td>
                                <td>
                                    <span class="badge rounded-pill <?= $a['role'] === 'admin' ? 'badge-role-admin' : 'badge-role-user' ?>">
                                        <?= ucfirst($a['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <?= esc($a['action']) ?>
                                    </span>
                                </td>
                                <td><?= esc($a['module']) ?></td>
                                <td class="text-muted small"><?= esc($a['description']) ?></td>
                                <td class="text-muted small">
                                    <?= date('M d, Y h:i A', strtotime($a['created_at'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <!-- User: Recent billing table -->
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Billing Month</th>
                            <th>Consumption</th>
                            <th>Amount Due</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_bills)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>No bills computed yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_bills as $b): ?>
                            <?php
                                $badges = ['unpaid' => 'warning', 'paid' => 'success', 'overdue' => 'danger'];
                                $badge  = $badges[$b['status']] ?? 'secondary';
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-600"><?= esc($b['client_name']) ?></div>
                                    <div class="small text-muted"><?= esc($b['client_no']) ?></div>
                                </td>
                                <td><?= esc($b['billing_month']) ?></td>
                                <td><?= number_format($b['consumption_kw'], 2) ?> KW</td>
                                <td class="fw-700 text-primary">₱<?= number_format($b['amount_due'], 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $badge ?> bg-opacity-15 text-<?= $badge ?>">
                                        <?= ucfirst($b['status']) ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?= date('M d, Y', strtotime($b['created_at'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php $this->endSection(); ?>