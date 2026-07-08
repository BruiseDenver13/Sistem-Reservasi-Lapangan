<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin', 'Kasir']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Pembayaran.php';
require_once __DIR__ . '/../models/Reservasi.php';

$judul_halaman    = 'Verifikasi Pembayaran';
$pembayaranModel  = new Pembayaran($koneksi);
$reservasiModel   = new Reservasi($koneksi);

$pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
$pesan_error  = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_sukses'], $_SESSION['pesan_error']);

$daftar_pembayaran = $pembayaranModel->ambilSemua();
$daftar_reservasi   = $reservasiModel->ambilSemua();

require_once __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../components/navbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Verifikasi Pembayaran</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Catat Pembayaran
    </button>
</div>

<?php if ($pesan_sukses !== ''): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($pesan_sukses) ?></div>
<?php endif; ?>
<?php if ($pesan_error !== ''): ?>
    <div class="alert alert-danger py-2"><?= htmlspecialchars($pesan_error) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode Reservasi</th>
                    <th>Pemesan</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar_pembayaran as $baris): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($baris['kode']) ?></code></td>
                        <td><?= htmlspecialchars($baris['nama_pemesan']) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($baris['metode']) ?></td>
                        <td>Rp <?= number_format((float)$baris['jumlah_bayar'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars(date('d M Y H:i', strtotime($baris['tanggal_bayar']))) ?></td>
                        <td>
                            <?php
                                $warna = match($baris['status_verifikasi']) {
                                    'diverifikasi' => 'bg-success',
                                    'ditolak'      => 'bg-danger',
                                    default        => 'bg-secondary',
                                };
                            ?>
                            <span class="badge <?= $warna ?>"><?= htmlspecialchars($baris['status_verifikasi']) ?></span>
                        </td>
                        <td class="text-end">
                            <?php if ($baris['status_verifikasi'] === 'menunggu'): ?>
                                <form method="POST" action="../process/pembayaran_process.php" class="d-inline">
                                    <input type="hidden" name="aksi" value="verifikasi">
                                    <input type="hidden" name="id" value="<?= (int)$baris['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form method="POST" action="../process/pembayaran_process.php" class="d-inline">
                                    <input type="hidden" name="aksi" value="tolak">
                                    <input type="hidden" name="id" value="<?= (int)$baris['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($daftar_pembayaran)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Belum ada data pembayaran.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="../process/pembayaran_process.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Catat Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="mb-3">
                    <label class="form-label">Reservasi</label>
                    <select name="id_reservasi" class="form-select" required>
                        <option value="">-- Pilih reservasi --</option>
                        <?php foreach ($daftar_reservasi as $r): ?>
                            <option value="<?= (int)$r['id'] ?>">
                                <?= htmlspecialchars($r['kode']) ?> — <?= htmlspecialchars($r['nama_pemesan']) ?>
                                (Rp <?= number_format((float)$r['total_bayar'],0,',','.') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Metode</label>
                    <select name="metode" class="form-select" required>
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Bayar (Rp)</label>
                    <input type="number" name="jumlah_bayar" class="form-control" min="0" step="1000" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../components/footer.php'; ?>