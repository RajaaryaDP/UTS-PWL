<?php 
$title = ($isEdit ? 'Ubah' : 'Tambah') . ' Akun';
require_once __DIR__ . '/../layout/header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <!-- Breadcrumb / Back button -->
        <div class="mb-3">
            <a href="<?= BASE_URL ?>/accounts" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Akun
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><?= $isEdit ? 'Ubah Akun' : 'Tambah Akun' ?></h5>
            </div>

            <div class="card-body p-4">
                <form action="<?= $isEdit ? BASE_URL . '/accounts/' . $id . '/update' : BASE_URL . '/accounts/store' ?>" method="POST">

                    <!-- Baris 1: Nama & Email -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Nama Lengkap</label>
                            <input type="text" name="name" 
                                   class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                   placeholder="Contoh: Ikbal Maulana" 
                                   value="<?= htmlspecialchars($values['name'] ?? '') ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback small"><?= $errors['name'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Email</label>
                            <input type="email" name="email" 
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   placeholder="nama@email.com" 
                                   value="<?= htmlspecialchars($values['email'] ?? '') ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback small"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Baris 2: Password -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Password</label>
                        <input type="password" name="password" 
                               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                               placeholder="<?= $isEdit ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 6 karakter' ?>"
                               <?= $isEdit ? '' : 'required' ?>>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback small"><?= $errors['password'] ?></div>
                        <?php endif; ?>
                        <?php if ($isEdit): ?>
                            <div class="form-text small text-muted">Biarkan kosong jika Anda tidak ingin mengganti password akun ini.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Baris 3: Tipe Akun & Status -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Tipe Akun</label>
                            <select name="account_type_id" class="form-select <?= isset($errors['account_type_id']) ? 'is-invalid' : '' ?>" required>
                                <option value="" disabled <?= empty($values['account_type_id']) ? 'selected' : '' ?>>-- Pilih Tipe Akun --</option>
                                <?php foreach ($accountTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>" 
                                        <?= (($values['account_type_id'] ?? '') === $type['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['account_type_id'])): ?>
                                <div class="invalid-feedback small"><?= $errors['account_type_id'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Status Akun</label>
                            <select name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>">
                                <option value="Aktif" <?= (($values['status'] ?? 'Aktif') === 'Aktif') ? 'selected' : '' ?>>Aktif</option>
                                <option value="Nonaktif" <?= (($values['status'] ?? '') === 'Nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback small"><?= $errors['status'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Baris 4: Jenis Identitas & Nomor Identitas -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold text-secondary">Jenis Identitas</label>
                            <select name="identification_type" class="form-select <?= isset($errors['identification_type']) ? 'is-invalid' : '' ?>">
                                <option value="NIM" <?= (($values['identification_type'] ?? 'NIM') === 'NIM') ? 'selected' : '' ?>>NIM (Mahasiswa)</option>
                                <option value="NIP" <?= (($values['identification_type'] ?? '') === 'NIP') ? 'selected' : '' ?>>NIP (Dosen / Tendik)</option>
                            </select>
                            <?php if (isset($errors['identification_type'])): ?>
                                <div class="invalid-feedback small"><?= $errors['identification_type'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-secondary">Nomor Identitas (NIM / NIP)</label>
                            <input type="text" name="identification_number" 
                                   class="form-control <?= isset($errors['identification_number']) ? 'is-invalid' : '' ?>" 
                                   placeholder="Contoh: 2507411029 atau 520000000000000746" 
                                   value="<?= htmlspecialchars($values['identification_number'] ?? '') ?>" required>
                            <?php if (isset($errors['identification_number'])): ?>
                                <div class="invalid-feedback small"><?= $errors['identification_number'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <a href="<?= BASE_URL ?>/accounts" class="btn btn-secondary px-4">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
