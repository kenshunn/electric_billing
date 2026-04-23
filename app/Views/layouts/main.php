<!DOCTYPE html>
<!-- app/Views/layouts/main.php -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pay4Electricity') ?></title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
        /* ── Brand Colors ── */
            --primary:     #1a56db;
            --primary-dk:  #1241a8;
            --sidebar-bg:  #0f1c36;
            --sidebar-w:   260px;
            --accent:      #f59e0b;
            --success:     #10b981;
            --danger:      #ef4444;

            /* ── Light Mode (default) ── */
            --bg-body:       #f0f4f8;
            --bg-card:       #ffffff;
            --bg-topbar:     #ffffff;
            --text-main:     #1e293b;
            --text-muted:    #64748b;
            --border-color:  #e2e8f0;
            --table-head-bg: #f8fafc;
            --input-bg:      #ffffff;
            --input-color:   #1e293b;
        }

        /* ── Dark Mode ── */
        [data-theme="dark"] {
            --bg-body:       #0f172a;
            --bg-card:       #1e293b;
            --bg-topbar:     #1e293b;
            --text-main:     #f1f5f9;
            --text-muted:    #94a3b8;
            --border-color:  #334155;
            --table-head-bg: #273549;
            --input-bg:      #273549;
            --input-color:   #f1f5f9;
}

        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background: var(--bg-body); margin: 0; color: var(--text-main); transition: background .3s, color .3s; }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            transition: transform .25s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.4rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-brand h5 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            font-size: .95rem;
            letter-spacing: .3px;
        }
        .sidebar-brand small { color: var(--accent); font-size: .72rem; font-weight: 600; }

        .sidebar-nav { padding: 1rem 0; flex: 1; }
        .sidebar-section-label {
            color: rgba(255,255,255,.35);
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: .6rem 1.5rem .2rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .62rem 1.5rem;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .15s;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            color: #fff;
            background: rgba(255,255,255,.07);
            border-left-color: var(--accent);
        }
        .sidebar-nav a i { font-size: 1rem; width: 1.2rem; text-align: center; }

        .sidebar-user {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-user .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--primary);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: .85rem;
        }
        .sidebar-user .name  { color: #fff;  font-size: .82rem; font-weight: 600; }
        .sidebar-user .role  { color: rgba(255,255,255,.4); font-size: .72rem; }

        /* ── Main ── */
        #main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #topbar {
            background: var(--bg-topbar);
            border-bottom: 1px solid var(--border-color);
            padding: .85rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0; z-index: 1030;
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
        }

        .page-wrapper { padding: 1.75rem; flex: 1; }

        /* ── Cards ── */
        .stat-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            transition: transform .15s, box-shadow .15s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,.08);
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }

        /* ── Tables ── */
        .table thead th {
            background: var(--table-head-bg);
            font-size: .775rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border-color);
        }

        .badge-role-admin { background: #ede9fe; color: #5b21b6; }
        .badge-role-user  { background: #dbeafe; color: #1e40af; }

        /* ── Toast container ── */
        #toast-container {
            position: fixed;
            top: 1.2rem;
            right: 1.2rem;
            z-index: 9999;
        }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }

        /* ── Dark Mode Overrides ── */
        [data-theme="dark"] .card,
        [data-theme="dark"] .stat-card {
            background: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .card-header {
            background: var(--bg-card) !important;
            border-color: var(--border-color) !important;
        }

        [data-theme="dark"] .table {
            color: var(--text-main);
        }

        [data-theme="dark"] .table thead th {
            color: var(--text-muted);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .table td,
        [data-theme="dark"] .table tr {
            border-color: var(--border-color);
        }

        [data-theme="dark"] .table-hover tbody tr:hover {
            background: rgba(255,255,255,.04);
        }

        [data-theme="dark"] .modal-content {
            background: var(--bg-card);
            color: var(--text-main);
        }

        [data-theme="dark"] .modal-header,
        [data-theme="dark"] .modal-footer {
            border-color: var(--border-color);
        }

        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background: var(--input-bg);
            color: var(--input-color);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus {
            background: var(--input-bg);
            color: var(--input-color);
        }

        [data-theme="dark"] .input-group-text {
            background: var(--input-bg);
            color: var(--text-muted);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .btn-light {
            background: #334155;
            border-color: #334155;
            color: #f1f5f9;
        }

        [data-theme="dark"] .badge.bg-light {
            background: #334155 !important;
            color: #f1f5f9 !important;
        }

        [data-theme="dark"] .text-muted {
            color: var(--text-muted) !important;
        }

        [data-theme="dark"] .alert {
            background: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        [data-theme="dark"] .page-wrapper {
            background: var(--bg-body);
        }

        /* Smooth transition for everything */
        *, *::before, *::after {
            transition: background-color .25s ease, border-color .25s ease, color .2s ease;
        }
    </style>
    <?= $this->renderSection('head_extra') ?>
</head>
<body>

<!-- ── Sidebar ──────────────────────────────────────────── -->
<div id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <img id="siteLogo"
            src="<?= base_url('img/nyancat.gif') ?>" 
            alt="Logo"
            style="width:47px; height:47px; border-radius:10px; object-fit:cover;">
            <div>
                <h5>Electric Bill <span style="color:var(--accent)">Services</span></h5>
                <small>Billing Management System</small>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php if (session()->get('role') === 'admin'): ?>
            <div class="sidebar-section-label">Overview</div>
            <a href="<?= base_url('admin/dashboard') ?>" class="<?= (uri_string() === 'admin/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
            <div class="sidebar-section-label">Management</div>
            <a href="<?= base_url('admin/users') ?>" class="<?= (uri_string() === 'admin/users') ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> User Accounts
            </a>
            <div class="sidebar-section-label">Monitoring</div>
            <a href="<?= base_url('admin/audit') ?>" class="<?= (uri_string() === 'admin/audit') ? 'active' : '' ?>">
                <i class="bi bi-journal-text"></i> Audit Trails
            </a>
        <?php else: ?>
            <div class="sidebar-section-label">Overview</div>
            <a href="<?= base_url('user/dashboard') ?>" class="<?= (uri_string() === 'user/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
            <div class="sidebar-section-label">Billing</div>
            <a href="<?= base_url('user/billing') ?>" class="<?= (uri_string() === 'user/billing') ? 'active' : '' ?>">
                <i class="bi bi-calculator-fill"></i> Compute Bill
            </a>
            <a href="<?= base_url('user/history') ?>" class="<?= (uri_string() === 'user/history') ? 'active' : '' ?>">
                <i class="bi bi-clock-history"></i> Billing History
            </a>
            <div class="sidebar-section-label">Account</div>
            <a href="<?= base_url('user/trails') ?>" class="<?= (uri_string() === 'user/trails') ? 'active' : '' ?>">
                <i class="bi bi-activity"></i> My Action Trails
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-user d-flex align-items-center gap-2">
        <div class="avatar"><?= strtoupper(substr(session()->get('full_name'), 0, 1)) ?></div>
        <div>
            <div class="name"><?= esc(session()->get('full_name')) ?></div>
            <div class="role"><?= ucfirst(session()->get('role')) ?></div>
        </div>
    </div>
</div>

<!-- ── Main Content ──────────────────────────────────────── -->
<div id="main-content">
    <div id="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" id="sidebar-toggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h6 class="mb-0 fw-700" style="color:#1e293b;"><?= esc($title ?? 'Dashboard') ?></h6>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-light" id="themeToggle" title="Toggle Dark/Light Mode">
            <i class="bi bi-moon-fill" id="themeIcon"></i>
            </button>
            <span class="badge bg-light text-dark border">
                <i class="bi bi-circle-fill text-success me-1" style="font-size:.5rem;"></i>
                <?= esc(session()->get('username')) ?>
            </span>
            <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </a>
        </div>
    </div>

    <div class="page-wrapper">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Sidebar toggle (mobile)
    $('#sidebar-toggle').on('click', function () {
        $('#sidebar').toggleClass('show');
    });

    // Show toast notification
    function showToast(message, type = 'success') {
        const icons = { success: 'check-circle-fill', danger: 'exclamation-triangle-fill', warning: 'exclamation-circle-fill' };
        const id = 'toast_' + Date.now();
        const html = `
            <div id="${id}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${icons[type] || 'info-circle-fill'} me-2"></i>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        $('#toast-container').append(html);
        const toastEl = document.getElementById(id);
        new bootstrap.Toast(toastEl, { delay: 4000 }).show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    // ── Dark / Light Mode ────────────────────────────────────
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon   = document.getElementById('themeIcon');

    // Load saved theme on page load
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateIcon(savedTheme);

    // Toggle on button click
    themeToggle.addEventListener('click', function () {
        const current = document.documentElement.getAttribute('data-theme');
        const next    = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        updateIcon(next);
    });

    // Update the button icon
    function updateIcon(theme) {
        if (theme === 'dark') {
            themeIcon.className = 'bi bi-sun-fill';
            themeToggle.title   = 'Switch to Light Mode';

            document.getElementById('siteLogo').src = '<?= base_url('img/nyancat.gif') ?>';
        } else {
            themeIcon.className = 'bi bi-moon-fill';
            themeToggle.title   = 'Switch to Dark Mode';

            document.getElementById('siteLogo').src = '<?= base_url('img/surfingnyan.gif') ?>';
        }
    }
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>