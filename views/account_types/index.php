<?php 
$title = 'Manajemen Tipe Akun';
require_once __DIR__ . '/../layout/header.php'; 
?>

<!-- Title & Action Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Tipe Akun</h4>
        <p class="text-muted small mb-0">Kelola kategori tipe akun dan peran pengguna dalam sistem.</p>
    </div>
    <a href="<?= BASE_URL ?>/account-types/create" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah Tipe Akun</span>
    </a>
</div>

<!-- Search Bar Box -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>/account-types" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" class="form-control border-start-0 bg-light" 
                           placeholder="Cari nama atau deskripsi..." 
                           value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    Cari
                </button>
                <?php if (!empty($search)): ?>
                    <a href="<?= BASE_URL ?>/account-types" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Data Table Card -->
<?php if (empty($types)): ?>
    <div class="card border-0 shadow-sm text-center p-5">
        <div class="py-4">
            <i class="bi bi-person-badge text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">Tidak ada data tipe akun yang ditemukan</h5>
            <p class="text-muted small">Coba kata kunci pencarian lain atau buat tipe akun baru.</p>
            <a href="<?= BASE_URL ?>/account-types/create" class="btn btn-primary btn-sm mt-2">
                <i class="bi bi-plus-lg"></i> Tambah Tipe Akun Baru
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small">
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Deskripsi</th>
                        <th>Dibuat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $row): ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($row['name']) ?></span>
                        </td>
                        <td>
                            <span class="text-muted small"><?= htmlspecialchars($row['description'] ?? '-') ?></span>
                        </td>
                        <td>
                            <span class="text-muted small">
                                <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?= BASE_URL ?>/account-types/<?= $row['id'] ?>/edit" 
                               class="btn btn-outline-warning btn-sm me-1" title="Ubah">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="<?= BASE_URL ?>/account-types/<?= $row['id'] ?>/delete" method="POST" 
                                  class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tipe akun ini? (Soft Delete)');">
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top text-muted small py-3 ps-4">
            Total Tipe Akun: <strong><?= count($types) ?></strong>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
