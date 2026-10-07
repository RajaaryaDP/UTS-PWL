<?php 
$title = 'Ganti Password';
require_once __DIR__ . '/../layout/header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <!-- Breadcrumb / Back button -->
        <div class="mb-3">
            <a href="<?= BASE_URL ?>/accounts" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-3 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div><?= htmlspecialchars($success) ?></div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Ganti Password</h5>
                    <small class="text-muted">Perbarui kata sandi akun Anda untuk keamanan.</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                    <i class="bi bi-person-fill me-1"></i> <?= htmlspecialchars($user['name'] ?? '') ?>
                </span>
            </div>

            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>/accounts/change-password" method="POST">
                    <!-- Password Saat Ini -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Password Saat Ini</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" name="current_password" id="current_password"
                                   class="form-control border-start-0 <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>"
                                   placeholder="Masukkan password saat ini" required autofocus>
                            <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('current_password', this)" title="Lihat/Sembunyikan">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['current_password'])): ?>
                            <div class="invalid-feedback d-block small"><?= htmlspecialchars($errors['current_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Password Baru -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" name="new_password" id="new_password"
                                   class="form-control border-start-0 <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>"
                                   placeholder="Minimal 6 karakter" required>
                            <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('new_password', this)" title="Lihat/Sembunyikan">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['new_password'])): ?>
                            <div class="invalid-feedback d-block small"><?= htmlspecialchars($errors['new_password']) ?></div>
                        <?php endif; ?>
                        <div class="form-text small text-muted">Password minimal 6 karakter dan tidak boleh sama dengan password lama.</div>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-check2-square"></i></span>
                            <input type="password" name="confirm_password" id="confirm_password"
                                   class="form-control border-start-0 <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                   placeholder="Ulangi password baru" required>
                            <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePassword('confirm_password', this)" title="Lihat/Sembunyikan">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="invalid-feedback d-block small"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <a href="<?= BASE_URL ?>/accounts" class="btn btn-outline-secondary px-3">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
