<?php 
$title = !empty($isAdmin) ? 'Manajemen Akun' : 'Dashboard ' . htmlspecialchars($dashboardRole ?? 'Pengguna');
require_once __DIR__ . '/../layout/header.php'; 
?>

<!-- Title & Action Button -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><?= !empty($isAdmin) ? 'Manajemen Akun' : 'Akun ' . htmlspecialchars($dashboardRole ?? '') ?></h4>
        <p class="text-muted small mb-0">
            <?php if (!empty($isAdmin)): ?>
                Kelola seluruh data akun pengguna dan hak akses sistem.
            <?php else: ?>
                Lihat daftar akun <?= htmlspecialchars(strtolower($dashboardRole ?? 'pengguna')) ?> lainnya.
            <?php endif; ?>
        </p>
    </div>
    <?php if (!empty($isAdmin)): ?>
        <a href="<?= BASE_URL ?>/accounts/create" class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2 shadow-sm">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Akun</span>
        </a>
    <?php endif; ?>
</div>

<!-- Search Bar Box -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>/accounts" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" class="form-control border-start-0 bg-light" 
                           placeholder="<?= !empty($isAdmin) ? 'Cari nama, email, NIM/NIP, atau tipe akun...' : 'Cari nama, email, atau nomor identitas...' ?>"
                           value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    Cari
                </button>
                <?php if (!empty($search)): ?>
                    <a href="<?= BASE_URL ?>/accounts" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Data Table Card -->
<?php if (empty($accounts)): ?>
    <div class="card border-0 shadow-sm text-center p-5">
        <div class="py-4">
            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">Tidak ada data akun yang ditemukan</h5>
            <p class="text-muted small">
                <?php if (!empty($isAdmin)): ?>
                    Coba kata kunci pencarian lain atau tambahkan data akun baru.
                <?php else: ?>
                    Coba kata kunci pencarian lain.
                <?php endif; ?>
            </p>
            <?php if (!empty($isAdmin)): ?>
                <a href="<?= BASE_URL ?>/accounts/create" class="btn btn-primary btn-sm mt-2">
                    <i class="bi bi-plus-lg"></i> Tambah Akun Baru
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small">
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Email</th>
                        <th>Identitas</th>
                        <?php if (!empty($isAdmin)): ?>
                            <th>Tipe Akun</th>
                        <?php elseif (strcasecmp($dashboardRole ?? '', 'Dosen') === 0): ?>
                            <th>Tipe Akun</th>
                        <?php endif; ?>
                        <th>Status</th>
                        <?php if (!empty($isAdmin)): ?>
                            <th class="text-end pe-4">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $row): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                            <small class="text-muted font-monospace" style="font-size: 0.72rem;">ID: <?= substr($row['id'], 0, 8) ?>...</small>
                        </td>
                        <td>
                            <span class="text-muted"><?= htmlspecialchars($row['email']) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace fw-normal">
                                <?= htmlspecialchars($row['identification_type']) ?>: <?= htmlspecialchars($row['identification_number']) ?>
                            </span>
                        </td>
                        <?php if (!empty($isAdmin) || strcasecmp($dashboardRole ?? '', 'Dosen') === 0): ?>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <?= htmlspecialchars($row['account_type_name'] ?? '-') ?>
                                </span>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?php if ($row['status'] === 'Aktif'): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                    <i class="bi bi-dash-circle-fill me-1"></i> Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <?php if (!empty($isAdmin)): ?>
                            <td class="text-end pe-4">
                                <a href="<?= BASE_URL ?>/accounts/<?= $row['id'] ?>/edit"
                                   class="btn btn-outline-warning btn-sm me-1" title="Ubah Akun">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?= BASE_URL ?>/accounts/<?= $row['id'] ?>/delete" method="POST"
                                      class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini? (Soft Delete)');">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus Akun">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top text-muted small py-3 ps-4">
            Total Akun: <strong><?= count($accounts) ?></strong> akun <?= !empty($isAdmin) ? 'terdaftar' : 'lainnya' ?>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
