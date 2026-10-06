<?php 
$title = ($isEdit ? 'Ubah' : 'Tambah') . ' Tipe Akun';
require_once __DIR__ . '/../layout/header.php'; 
?>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
        <!-- Breadcrumb / Back button -->
        <div class="mb-3">
            <a href="<?= BASE_URL ?>/account-types" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Tipe Akun
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark"><?= $isEdit ? 'Ubah Tipe Akun' : 'Tambah Tipe Akun' ?></h5>
            </div>

            <div class="card-body p-4">
                <form action="<?= $isEdit ? BASE_URL . '/account-types/' . $id . '/update' : BASE_URL . '/account-types/store' ?>" method="POST">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Nama Tipe Akun</label>
                        <input type="text" name="name" 
                               class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               placeholder="Contoh: Dosen, Mahasiswa, atau Staff" 
                               value="<?= htmlspecialchars($values['name'] ?? '') ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback small"><?= $errors['name'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">Deskripsi</label>
                        <textarea name="description" rows="4" 
                                  class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                  placeholder="Tuliskan keterangan hak akses atau peran tipe akun ini..."><?= htmlspecialchars($values['description'] ?? '') ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback small"><?= $errors['description'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>
                        <a href="<?= BASE_URL ?>/account-types" class="btn btn-secondary px-4">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
