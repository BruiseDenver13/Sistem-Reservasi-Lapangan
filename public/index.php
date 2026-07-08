<?php

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Lapangan.php';

$lapanganModel = new Lapangan($koneksi);
$daftar_lapangan = $lapanganModel->ambilYangAktif();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Lapangan Futsal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">⚽ Futsal Reservasi</span>
    <div class="d-flex gap-2">
        <a href="jadwal.php" class="btn btn-outline-light btn-sm">Cek Jadwal</a>
        <a href="reservasi.php" class="btn btn-outline-light btn-sm">Booking</a>
        <a href="konfirmasi.php" class="btn btn-outline-light btn-sm">Cek Status</a>
        <a href="../auth/login.php" class="btn btn-light btn-sm">Login Admin</a>
    </div>
</nav>

<div class="container py-5 text-center">
    <h1 class="fw-bold">Booking Lapangan Futsal Jadi Gampang</h1>
    <p class="text-muted">Cek jadwal kosong, pilih jam, booking langsung tanpa ribet.</p>
    <a href="reservasi.php" class="btn btn-primary btn-lg mt-2">Booking Sekarang</a>
</div>

<div class="container pb-5">
    <h4 class="mb-3">Lapangan Tersedia</h4>
    <div class="row g-3">
        <?php foreach ($daftar_lapangan as $l): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($l['foto']) && file_exists(__DIR__ . '/../assets/uploads/' . $l['foto'])): ?>
                        <img src="../assets/uploads/<?= htmlspecialchars($l['foto']) ?>" class="card-img-top" style="height:160px; object-fit:cover;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height:160px;">
                            <i class="bi bi-image text-muted" style="font-size:2rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h6 class="card-title"><?= htmlspecialchars($l['nama']) ?></h6>
                        <div class="text-muted small mb-2"><?= htmlspecialchars($l['nama_kategori']) ?></div>
                        <div class="fw-bold">Rp <?= number_format((float)$l['harga_per_jam'], 0, ',', '.') ?> / jam</div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($daftar_lapangan)): ?>
            <p class="text-muted text-center">Belum ada lapangan yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>