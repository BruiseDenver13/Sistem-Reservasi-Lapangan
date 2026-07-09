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

$pesan_error = $_SESSION['pesan_error'] ?? '';
if (session_status() === PHP_SESSION_ACTIVE) {
    unset($_SESSION['pesan_error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking - Reservasi Futsal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">⚽ Futsal Reservasi</span>
    <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-outline-light btn-sm">Beranda</a>
        <a href="jadwal.php" class="btn btn-outline-light btn-sm">Cek Jadwal</a>
        <a href="konfirmasi.php" class="btn btn-outline-light btn-sm">Cek Status</a>
    </div>
</nav>

<div class="container py-4" style="max-width:600px;">
    <h4 class="mb-3">Booking Lapangan</h4>

    <?php if ($pesan_error !== ''): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($pesan_error) ?></div>
    <?php endif; ?>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-6">
            <select name="lapangan" class="form-select" required onchange="this.form.submit()">
                <option value="">-- Pilih Lapangan --</option>
                <?php foreach ($daftar_lapangan as $l): ?>
                    <option value="<?= (int)$l['id'] ?>" <?= $id_lapangan_dipilih === (int)$l['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($l['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($tanggal_dipilih) ?>" min="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
        </div>
    </form>

    <?php if ($id_lapangan_dipilih > 0 && !empty($daftar_slot)): ?>
        <form method="POST" action="../process/reservasi_public_process.php">
            <div class="mb-3">
                <label class="form-label">Pilih Jam</label>
                <select name="id_jadwal" class="form-select" required>
                    <?php foreach ($daftar_slot as $slot): ?>
                        <option value="<?= (int)$slot['id'] ?>">
                            <?= substr($slot['jam_mulai'],0,5) ?> - <?= substr($slot['jam_selesai'],0,5) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Pemesan</label>
                <input type="text" name="nama_pemesan" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">No. HP</label>
                <input type="text" name="no_hp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan (opsional)</label>
                <textarea name="keterangan" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Booking Sekarang</button>
        </form>
    <?php elseif ($id_lapangan_dipilih > 0): ?>
        <p class="text-muted">Tidak ada slot tersedia untuk tanggal ini, coba tanggal lain.</p>
    <?php endif; ?>
</div>

</body>
</html>