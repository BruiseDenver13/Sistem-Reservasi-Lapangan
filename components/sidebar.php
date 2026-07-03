<?php

$role_aktif    = $_SESSION['role'] ?? '';
$halaman_aktif = basename($_SERVER['PHP_SELF']); // untuk highlight menu aktif


function menu_aktif(string $nama_file, string $halaman_aktif): string
{
    return $nama_file === $halaman_aktif ? 'active' : '';
}
?>
<nav id="sidebar" class="bg-dark text-white p-3" style="width: 240px; min-height: 100vh;">
    <h5 class="mb-4">⚽ Futsal Admin</h5>

    <ul class="nav nav-pills flex-column gap-1">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-white <?= menu_aktif('dashboard.php', $halaman_aktif) ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>

        <?php if (in_array($role_aktif, ['Superadmin', 'Admin'], true)): ?>
            <li class="nav-item">
                <a href="lapangan.php" class="nav-link text-white <?= menu_aktif('lapangan.php', $halaman_aktif) ?>">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Data Lapangan
                </a>
            </li>
            <li class="nav-item">
                <a href="kategori_lapangan.php" class="nav-link text-white <?= menu_aktif('kategori_lapangan.php', $halaman_aktif) ?>">
                    <i class="bi bi-tags me-2"></i>Kategori Lapangan
                </a>
            </li>
            <li class="nav-item">
                <a href="jadwal.php" class="nav-link text-white <?= menu_aktif('jadwal.php', $halaman_aktif) ?>">
                    <i class="bi bi-calendar-week me-2"></i>Kelola Jadwal
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a href="reservasi.php" class="nav-link text-white <?= menu_aktif('reservasi.php', $halaman_aktif) ?>">
                <i class="bi bi-journal-check me-2"></i>Reservasi
            </a>
        </li>
        <li class="nav-item">
            <a href="pembayaran.php" class="nav-link text-white <?= menu_aktif('pembayaran.php', $halaman_aktif) ?>">
                <i class="bi bi-credit-card me-2"></i>Verifikasi Pembayaran
            </a>
        </li>

        <?php if (in_array($role_aktif, ['Superadmin', 'Admin'], true)): ?>
            <li class="nav-item">
                <a href="laporan.php" class="nav-link text-white <?= menu_aktif('laporan.php', $halaman_aktif) ?>">
                    <i class="bi bi-bar-chart-line me-2"></i>Laporan
                </a>
            </li>
        <?php endif; ?>

        <?php if ($role_aktif === 'Superadmin'): ?>
            <li class="nav-item">
                <a href="kelola_user.php" class="nav-link text-white <?= menu_aktif('kelola_user.php', $halaman_aktif) ?>">
                    <i class="bi bi-people me-2"></i>Kelola User
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>