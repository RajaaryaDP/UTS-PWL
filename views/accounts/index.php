<?php
$title = $isAdmin ? 'Manajemen Akun' : 'Akun ' . $dashboardRole;
require_once __DIR__ . '/../layout/header.php';

$currentUserId = $_SESSION['user']['id'] ?? '';
?>

<!-- Judul & tombol aksi -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><?= $isAdmin ? 'Manajemen Akun' : 'Akun ' . htmlspecialchars($dashboardRole) ?></h4>
        <p class="text-muted small mb-0">
            <?= $isAdmin ? 'Kelola seluruh akun pengguna.' : 'Lihat daftar akun ' . htmlspecialchars(strtolower($dashboardRole)) . ' lainnya.' ?>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/accounts/change-password" class="btn btn-primary">
            <i class="bi bi-key"></i> Ganti Password
        </a>
        <?php if ($isAdmin): ?>
            <a href="<?= BASE_URL ?>/accounts/create" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Tambah Akun
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Pencarian -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= BASE_URL ?>/accounts" class="row g-2 align-items-center">
            <div class="col-md-10">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" class="form-control border-start-0 bg-light"
                           placeholder="Cari nama, email, atau nomor identitas..."
                           value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Cari</button>
                <?php if (!empty($search)): ?>
                    <a href="<?= BASE_URL ?>/accounts" class="btn btn-outline-secondary" title="Reset">
                        <i class="bi bi-x-lg"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Tabel -->
<?php if (empty($accounts)): ?>
    <div class="card border-0 shadow-sm text-center p-5">
        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
        <h5 class="mt-3 text-muted">Tidak ada akun yang ditemukan</h5>
        <p class="text-muted small mb-0">Coba kata kunci pencarian lain.</p>
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="small">
                    <tr>
                        <th class="ps-4">Nama</th>
                        <th>Email</th>
                        <?php if ($isAdmin): ?><th>Tipe</th><?php endif; ?>
                        <th>Identitas</th>
                        <th>Status</th>
                        <?php if ($canEditRole): ?>
                            <th class="text-end pe-4">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                        <?php
                        $isSelf    = $account['id'] === $currentUserId;
                        $bolehEdit = $canEditRole && (
                            $isAdmin || strcasecmp(trim($account['account_type_name'] ?? ''), 'Staff') === 0
                        );
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">
                                    <?= htmlspecialchars($account['name']) ?>
                                    <?php if ($isSelf): ?>
                                        <span class="badge bg-secondary ms-1">Anda</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted small font-monospace">
                                    ID: <?= htmlspecialchars(substr($account['id'], 0, 8)) ?>...
                                </div>
                            </td>
                            <td><?= htmlspecialchars($account['email']) ?></td>
                            <?php if ($isAdmin): ?>
                                <td><?= htmlspecialchars($account['account_type_name'] ?? '-') ?></td>
                            <?php endif; ?>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">
                                    <?= htmlspecialchars($account['identification_type']) ?>:
                                    <?= htmlspecialchars($account['identification_number']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($account['status'] === 'Aktif'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill"></i> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        <i class="bi bi-dash-circle-fill"></i> Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>

                            <?php if ($canEditRole): ?>
                                <td class="text-end pe-4">
                                    <?php if ($bolehEdit): ?>
                                        <a href="<?= BASE_URL ?>/accounts/<?= htmlspecialchars($account['id']) ?>/edit"
                                           class="btn btn-outline-warning btn-sm me-1" title="Ubah">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($isAdmin && !$isSelf): ?>
                                        <form action="<?= BASE_URL ?>/accounts/<?= htmlspecialchars($account['id']) ?>/delete"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus akun ini? (Soft Delete)');">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top text-muted small py-3 ps-4">
            Total Akun: <strong><?= count($accounts) ?></strong>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>