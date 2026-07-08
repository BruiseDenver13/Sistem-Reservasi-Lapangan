<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin', 'Kasir']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Reservasi.php';

$judul_halaman   = 'Reservasi';
$reservasiModel  = new Reservasi($koneksi);

$pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
$pesan_error  = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_sukses'], $_SESSION['pesan_error']);

$daftar_reservasi     = $reservasiModel->ambilSemua();
$daftar_jadwal_kosong = $reservasiModel->ambilJadwalTersedia();

require_once __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../components/navbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Reservasi</h4>
    <?php if (empty($daftar_jadwal_kosong)): ?>
        <span class="text-muted small">Belum ada jadwal tersedia.</span>
    <?php else: ?>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Reservasi Baru
        </button>
    <?php endif; ?>
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
                    <th>Kode</th>
                    <th>Pemesan</th>
                    <th>Lapangan</th>
                    <th>Jadwal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar_reservasi as $baris): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($baris['kode']) ?></code></td>
                        <td>
                            <?= htmlspecialchars($baris['nama_pemesan']) ?><br>
                            <span class="small text-muted"><?= htmlspecialchars($baris['no_hp']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($baris['nama_lapangan']) ?></td>
                        <td>
                            <?= htmlspecialchars(date('d M Y', strtotime($baris['tanggal']))) ?><br>
                            <span class="small text-muted"><?= substr($baris['jam_mulai'],0,5) ?> - <?= substr($baris['jam_selesai'],0,5) ?></span>
                        </td>
                        <td>Rp <?= number_format((float)$baris['total_bayar'], 0, ',', '.') ?></td>
                        <td>
                            <?php
                                $warna = match($baris['status']) {
                                    'menunggu'     => 'bg-secondary',
                                    'dikonfirmasi' => 'bg-primary',
                                    'selesai'      => 'bg-success',
                                    'dibatalkan'   => 'bg-danger',
                                    default        => 'bg-secondary',
                                };
                            ?>
                            <span class="badge <?= $warna ?>"><?= htmlspecialchars($baris['status']) ?></span>
                        </td>
                        <td class="text-end">
                            <?php if ($baris['status'] === 'dikonfirmasi'): ?>
                                <form method="POST" action="../process/reservasi_process.php" class="d-inline">
                                    <input type="hidden" name="aksi" value="selesai">
                                    <input type="hidden" name="id" value="<?= (int)$baris['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Tandai selesai">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form method="POST" action="../process/reservasi_process.php" class="d-inline"
                                      onsubmit="return confirm('Batalkan reservasi ini? Jadwal akan kembali tersedia.');">
                                    <input type="hidden" name="aksi" value="batalkan">
                                    <input type="hidden" name="id" value="<?= (int)$baris['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($daftar_reservasi)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Belum ada data reservasi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="../process/reservasi_process.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reservasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="mb-3">
                    <label class="form-label">Jadwal</label>
                    <select name="id_jadwal" id="pilih_jadwal" class="form-select" required>
                        <option value="">-- Pilih jadwal --</option>
                        <?php foreach ($daftar_jadwal_kosong as $j): ?>
                            <?php
                                $durasi = (strtotime($j['jam_selesai']) - strtotime($j['jam_mulai'])) / 3600;
                                $estimasi = $durasi * (float) $j['harga_per_jam'];
                            ?>
                            <option value="<?= (int)$j['id'] ?>" data-estimasi="<?= $estimasi ?>">
                                <?= htmlspecialchars($j['nama_lapangan']) ?> —
                                <?= htmlspecialchars(date('d M Y', strtotime($j['tanggal']))) ?>
                                (<?= substr($j['jam_mulai'],0,5) ?>-<?= substr($j['jam_selesai'],0,5) ?>)
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
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2"></textarea>
                </div>
                <div class="alert alert-info py-2 mb-0" id="estimasi_box" style="display:none;">
                    Estimasi total: Rp <span id="estimasi_angka">0</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('pilih_jadwal').addEventListener('change', function () {
    const opsi = this.options[this.selectedIndex];
    const estimasi = opsi.dataset.estimasi;
    const box = document.getElementById('estimasi_box');
    if (estimasi) {
        document.getElementById('estimasi_angka').textContent =
            Number(estimasi).toLocaleString('id-ID');
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
});
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>