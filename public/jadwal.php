<?php

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Lapangan.php';
require_once __DIR__ . '/../models/Jadwal.php';

$lapanganModel = new Lapangan($koneksi);
$jadwalModel   = new Jadwal($koneksi);

$daftar_lapangan = $lapanganModel->ambilYangAktif();

$id_lapangan_dipilih = (int) ($_GET['lapangan'] ?? 0);
$tanggal_dipilih      = $_GET['tanggal'] ?? date('Y-m-d');

$daftar_slot = [];
if ($id_lapangan_dipilih > 0) {
    $semua_jadwal = $jadwalModel->ambilByLapanganTanggal($id_lapangan_dipilih, $tanggal_dipilih);
    $daftar_slot  = array_filter($semua_jadwal, fn($j) => $j['status'] === 'tersedia');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Jadwal - Reservasi Futsal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">⚽ Futsal Reservasi</span>
    <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-outline-light btn-sm">Beranda</a>
        <a href="reservasi.php" class="btn btn-outline-light btn-sm">Booking</a>
        <a href="konfirmasi.php" class="btn btn-outline-light btn-sm">Cek Status</a>
    </div>
</nav>

<div class="container py-4">
    <h4 class="mb-3">Cek Ketersediaan Jadwal</h4>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <select name="lapangan" class="form-select" required>
                <option value="">-- Pilih Lapangan --</option>
                <?php foreach ($daftar_lapangan as $l): ?>
                    <option value="<?= (int)$l['id'] ?>" <?= $id_lapangan_dipilih === (int)$l['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($l['nama']) ?> (<?= htmlspecialchars($l['nama_kategori']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal_dipilih) ?>" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Cek Jadwal</button>
        </div>
    </form>

    <?php if ($id_lapangan_dipilih > 0): ?>
        <div class="row g-2">
            <?php foreach ($daftar_slot as $slot): ?>
                <div class="col-md-3">
                    <div class="card text-center p-2 border-success">
                        <div class="fw-bold"><?= substr($slot['jam_mulai'],0,5) ?> - <?= substr($slot['jam_selesai'],0,5) ?></div>
                        <span class="badge bg-success">Tersedia</span>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($daftar_slot)): ?>
                <p class="text-muted">Tidak ada slot tersedia untuk tanggal ini.</p>
            <?php endif; ?>
        </div>
        <a href="reservasi.php?lapangan=<?= $id_lapangan_dipilih ?>&tanggal=<?= htmlspecialchars($tanggal_dipilih) ?>" class="btn btn-primary mt-3">
            Booking Slot Ini
        </a>
    <?php endif; ?>
</div>

</body>
</html>