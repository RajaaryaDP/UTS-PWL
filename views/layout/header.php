<?php
global $page;
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = strcasecmp(trim($currentUser['account_type_name'] ?? ''), 'Admin') === 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Manajemen Akun' ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

    <div class="app-shell" id="appShell">

        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <a href="<?= BASE_URL ?>/accounts" class="sidebar-brand">
                <span class="brand-mark"><i class="bi bi-shield-lock-fill"></i></span>
                <span>Manajemen Akun</span>
            </a>

            <ul class="sidebar-nav">
                <li>
                    <a href="<?= BASE_URL ?>/accounts" class="nav-link <?= ($page === 'accounts') ? 'active' : '' ?>">
                        <i class="bi bi-people-fill"></i>
                        <span>Akun</span>
                    </a>
                </li>
                <?php if ($isAdmin): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/account-types" class="nav-link <?= ($page === 'account-types') ? 'active' : '' ?>">
                            <i class="bi bi-person-badge-fill"></i>
                            <span>Tipe Akun</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/actions" class="nav-link <?= ($page === 'actions') ? 'active' : '' ?>">
                            <i class="bi bi-key-fill"></i>
                            <span>Jenis Aksi</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </aside>

        <!-- Main Wrapper -->
        <div class="main">
            <!-- Topbar Header -->
            <header class="topbar">
                <div></div>

                <div class="d-flex align-items-center gap-3">
                    <?php if ($currentUser): ?>
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-bold small text-dark"><?= htmlspecialchars($currentUser['name']) ?></div>
                            <div class="text-muted" style="font-size: 0.78rem;">
                                <?= htmlspecialchars($currentUser['email']) ?>
                                <?php if (!empty($currentUser['account_type_name'])): ?>
                                    &bull; <span class="badge bg-light text-secondary border"><?= htmlspecialchars($currentUser['account_type_name']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-outline-danger btn-sm" title="Keluar">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Page Content -->
            <main class="content">
