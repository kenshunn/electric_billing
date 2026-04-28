<!DOCTYPE html>
<!-- app/Views/auth/login.php -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Pay4Electricity</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f1c36 0%, #1a3a6b 60%, #1a56db 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,.3);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem 2rem;
        }
        .brand-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #1a56db, #f59e0b);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            color: #fff;
            margin: 0 auto 1rem;
            box-shadow: 0 8px 20px rgba(26,86,219,.35);
        }
        .form-control:focus {
            border-color: #1a56db;
            box-shadow: 0 0 0 3px rgba(26,86,219,.12);
        }
        .btn-login {
            background: linear-gradient(135deg, #1a56db, #1241a8);
            border: 0;
            color: #fff;
            font-weight: 600;
            padding: .7rem;
            border-radius: 10px;
            transition: opacity .15s;
        }
        .btn-login:hover { opacity: .88; color: #fff; }
        .input-group-text {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
        }
        .form-control { border-color: #e2e8f0; }
        .demo-box {
            background: #f0f7ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: .75rem 1rem;
            font-size: .8rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon">
                <img src="<?= base_url('img/nyancat.gif') ?>" 
             alt="Logo"
             style="width:47px; height:47px; border-radius:10px; object-fit:cover;">
            </div>
            <h4 class="fw-800 mb-0" style="color:#0f1c36;">PAY4 <span style="color:#1a56db;">ELECTRICITY</span></h4>
            <p class="text-muted small mb-0">Electricity Billing</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-triangle-fill me-1"></i><?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success py-2 small">
                <i class="bi bi-check-circle-fill me-1"></i><?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty(session()->getFlashdata('errors'))): ?>
            <div class="alert alert-danger py-2 small">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                    <div><i class="bi bi-dot"></i><?= esc($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-600 small">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="username" class="form-control"
                           placeholder="Enter username"
                           value="<?= esc(old('username')) ?>" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-600 small">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" id="pwd" class="form-control"
                           placeholder="Enter password" required>
                    <button type="button" class="input-group-text" id="toggle-pwd" style="cursor:pointer;">
                        <i class="bi bi-eye-fill" id="eye-icon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggle-pwd').addEventListener('click', function () {
            const pwd  = document.getElementById('pwd');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'bi bi-eye-slash-fill';
            } else {
                pwd.type = 'password';
                icon.className = 'bi bi-eye-fill';
            }
        });
    </script>
</body>
</html>