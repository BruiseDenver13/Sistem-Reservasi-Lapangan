<?php

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Reservasi.php';

$reservasiModel = new Reservasi($koneksi);

$kode = trim($_GET['kode'] ?? '');
$data = $kode !== '' ? $reservasiModel->cariByKode($kode) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Status - Reservasi Futsal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">⚽ Futsal Reservasi</span>
    <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-outline-light btn-sm">Beranda</a>
        <a href="jadwal.php" class="btn btn-outline-light btn-sm">Cek Jadwal</a>
        <a href="reservasi.php" class="btn btn-outline-light btn-sm">Booking</a>
    </div>
</nav>

<div class="container py-4" style="max-width:500px;">
    <h4 class="mb-3">Cek Status Reservasi</h4>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-9">
            <input type="text" name="kode" class="form-control" placeholder="Masukkan kode booking (contoh: RSV2607...)" value="<?= htmlspecialchars($kode) ?>" required>
        </div>
        <div class="col-3">
            <button type="submit" class="btn btn-primary w-100">Cek</button>
        </div>
    </form>

    <?php if ($kode !== '' && !$data): ?>
        <div class="alert alert-danger">Kode booking tidak ditemukan.</div>
    <?php elseif ($data): ?>
        <?php
            $warna = match($data['status']) {
                'menunggu'     => 'secondary',
                'dikonfirmasi' => 'primary',
                'selesai'      => 'success',
                'dibatalkan'   => 'danger',
                default        => 'secondary',
            };
        ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5><?= htmlspecialchars($data['kode']) ?> <span class="badge bg-<?= $warna ?>"><?= htmlspecialchars($data['status']) ?></span></h5>
                <p class="mb-1"><strong>Nama:</strong> <?= htmlspecialchars($data['nama_pemesan']) ?></p>
                <p class="mb-1"><strong>Lapangan:</strong> <?= htmlspecialchars($data['nama_lapangan']) ?></p>
                <p class="mb-1"><strong>Jadwal:</strong> <?= htmlspecialchars(date('d M Y', strtotime($data['tanggal']))) ?>, <?= substr($data['jam_mulai'],0,5) ?> - <?= substr($data['jam_selesai'],0,5) ?></p>
                <p class="mb-0"><strong>Total:</strong> Rp <?= number_format((float)$data['total_bayar'], 0, ',', '.') ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>