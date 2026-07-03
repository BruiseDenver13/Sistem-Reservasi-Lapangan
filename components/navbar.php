<?php

$nama_admin = $_SESSION['nama'] ?? 'Admin';
$role_admin = $_SESSION['role'] ?? '-';
?>
<div class="flex-grow-1 d-flex flex-column">
    <nav class="navbar navbar-light bg-white border-bottom px-4 py-2">
        <span class="navbar-brand mb-0 h5"><?= htmlspecialchars($judul_halaman ?? '') ?></span>

        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars($nama_admin) ?>
                <span class="badge bg-secondary ms-1"><?= htmlspecialchars($role_admin) ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="../auth/logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a></li>
            </ul>
        </div>
    </nav>

    <main class="p-4">