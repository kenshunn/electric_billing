<?php
// app/Views/admin/users.php
$this->extend('layouts/main');
$this->section('content');
?>

<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3" style="border-radius:14px 14px 0 0;">
        <h6 class="mb-0 fw-700"><i class="bi bi-people-fill text-primary me-2"></i>User Accounts</h6>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModal" id="btnCreate">
            <i class="bi bi-plus-lg me-1"></i>Add User
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="usersTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th><th>Full Name</th><th>Username</th><th>Email</th>
                        <th>Role</th><th>Status</th><th>Created</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $i => $u): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td class="fw-600"><?= esc($u['full_name']) ?></td>
                        <td class="text-muted"><?= esc($u['username']) ?></td>
                        <td class="text-muted small"><?= esc($u['email']) ?></td>
                        <td>
                            <span class="badge rounded-pill <?= $u['role'] === 'admin' ? 'badge-role-admin' : 'badge-role-user' ?>">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <span class="badge bg-success bg-opacity-15 text-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-15 text-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1 btn-edit"
                                    data-id="<?= $u['id'] ?>">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete"
                                    data-id="<?= $u['id'] ?>"
                                    data-name="<?= esc($u['full_name']) ?>">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── User Modal ─────────────────────────────────────────── -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700" id="modalTitle">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="formErrors" class="alert alert-danger d-none small"></div>
                <form id="userForm">
                    <input type="hidden" id="userId" name="id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-600 small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="full_name" id="full_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 small">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" id="username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 small">Role <span class="text-danger">*</span></label>
                            <select class="form-select" name="role" id="role">
                                <option value="user">Normal User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600 small">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 small">Password <span class="text-danger" id="pwdRequired">*</span></label>
                            <input type="password" class="form-control" name="password" id="password">
                            <div class="form-text" id="pwdHint"></div>
                        </div>
                        <div class="col-md-6" id="statusField" style="display:none;">
                            <label class="form-label fw-600 small">Status</label>
                            <select class="form-select" name="is_active" id="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveUser">
                    <i class="bi bi-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Delete Confirm Modal ──────────────────────────────── -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-body text-center py-4">
                <div class="mb-3" style="font-size:2.5rem;">🗑️</div>
                <h6 class="fw-700">Delete User?</h6>
                <p class="text-muted small mb-0">Are you sure you want to delete <strong id="deleteName"></strong>? This cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
$(function () {
    const BASE = '<?= base_url() ?>';
    let deleteId = null;

    // Init DataTable
    $('#usersTable').DataTable({ order: [] });

    // ── Open Create Modal ─────────────────────────────────
    $('#btnCreate').on('click', function () {
        $('#modalTitle').text('Add New User');
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#pwdRequired').show();
        $('#pwdHint').text('');
        $('#statusField').hide();
        $('#formErrors').addClass('d-none').html('');
    });

    // ── Open Edit Modal ───────────────────────────────────
    $(document).on('click', '.btn-edit', function () {
        const id = $(this).data('id');
        $.get(BASE + 'admin/users/get/' + id, function (res) {
            if (!res.success) { showToast(res.message, 'danger'); return; }
            const u = res.user;
            $('#modalTitle').text('Edit User');
            $('#userId').val(u.id);
            $('#full_name').val(u.full_name);
            $('#username').val(u.username);
            $('#email').val(u.email);
            $('#role').val(u.role);
            $('#is_active').val(u.is_active);
            $('#password').val('');
            $('#pwdRequired').hide();
            $('#pwdHint').text('Leave blank to keep current password.');
            $('#statusField').show();
            $('#formErrors').addClass('d-none').html('');
            new bootstrap.Modal('#userModal').show();
        });
    });

    // ── Save User ─────────────────────────────────────────
    $('#saveUser').on('click', function () {
        const id  = $('#userId').val();
        const url = id ? BASE + 'admin/users/update' : BASE + 'admin/users/store';
        $.post(url, $('#userForm').serialize() + '&' + $('meta[name=csrf-token]').attr('content'), function (res) {
            if (res.success) {
                bootstrap.Modal.getInstance('#userModal').hide();
                showToast(res.message, 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                let html = '<ul class="mb-0">';
                $.each(res.errors, (k, v) => { html += `<li>${v}</li>`; });
                html += '</ul>';
                $('#formErrors').removeClass('d-none').html(html);
            }
        });
    });

    // ── Delete ────────────────────────────────────────────
    $(document).on('click', '.btn-delete', function () {
        deleteId = $(this).data('id');
        $('#deleteName').text($(this).data('name'));
        new bootstrap.Modal('#deleteModal').show();
    });

    $('#confirmDelete').on('click', function () {
        $.post(BASE + 'admin/users/delete', { id: deleteId }, function (res) {
            bootstrap.Modal.getInstance('#deleteModal').hide();
            if (res.success) {
                showToast(res.message, 'success');
                setTimeout(() => location.reload(), 900);
            } else {
                showToast(res.message, 'danger');
            }
        });
    });
});
</script>
<?php $this->endSection(); ?>