<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Akun</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 1.5rem;
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(22, 26, 48, 0.08);
            border: 1px solid #e5e9f2;
            padding: 2.5rem;
        }
        .brand-icon-box {
            width: 58px;
            height: 58px;
            background: #eef2ff;
            color: #4f46e5;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1.25rem;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.15);
        }
        .btn-login {
            background-color: #4f46e5;
            border-color: #4f46e5;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        .hint-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 0.85rem;
            font-size: 0.82rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Logo / Icon -->
        <div class="brand-icon-box shadow-sm">
            <i class="bi bi-shield-check"></i>
        </div>

        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1 text-dark">Manajemen Akun</h4>
            <p class="text-muted small mb-0">Masuk pakai email dan password kamu.</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger py-2 small d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?= htmlspecialchars($errors['general']) ?></div>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/auth/login" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Email</label>
                <input type="email" name="email" 
                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                       placeholder="nama@email.com"
                       value="<?= htmlspecialchars($old['email'] ?? 'admin@pnj.ac.id') ?>" required autofocus>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback small"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary">Password</label>
                <input type="password" name="password" 
                       class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                       placeholder="••••••••" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback small"><?= $errors['password'] ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-login w-100 text-white shadow-sm d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </button>
        </form>

        <div class="hint-box mt-4 text-secondary">
            <div class="fw-semibold text-dark mb-1"><i class="bi bi-info-circle me-1 text-primary"></i> Akun Pengujian (Seed Data):</div>
            <div>Email: <code class="text-primary">admin@pnj.ac.id</code></div>
            <div>Password: <code class="text-primary">admin123</code></div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
