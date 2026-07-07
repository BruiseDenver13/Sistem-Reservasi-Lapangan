<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Jadwal.php';
require_once __DIR__ . '/../models/Lapangan.php';

$judul_halaman = 'Kelola Jadwal';
$jadwalModel   = new Jadwal($koneksi);
$lapanganModel = new Lapangan($koneksi);

$pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
$pesan_error  = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_sukses'], $_SESSION['pesan_error']);

$daftar_jadwal   = $jadwalModel->ambilSemua();
$daftar_lapangan = $lapanganModel->ambilUntukDropdown();

require_once __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../components/navbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Kelola Jadwal</h4>
    <?php if (empty($daftar_lapangan)): ?>
        <span class="text-muted small">Tambahkan lapangan aktif dulu sebelum membuat jadwal.</span>
    <?php else: ?>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Jadwal
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
                    <th>#</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($daftar_jadwal as $baris): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($baris['nama_lapangan']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($baris['tanggal']))) ?></td>
                        <td><?= substr($baris['jam_mulai'], 0, 5) ?> - <?= substr($baris['jam_selesai'], 0, 5) ?></td>
                        <td>
                            <?php
                                $warna = match($baris['status']) {
                                    'tersedia' => 'bg-success',
                                    'dipesan'  => 'bg-warning text-dark',
                                    default    => 'bg-secondary',
                                };
                            ?>
                            <span class="badge <?= $warna ?>"><?= htmlspecialchars($baris['status']) ?></span>
                        </td>
                        <td class="text-end">
                            <button type="button"
                                    class="btn btn-sm btn-outline-warning btn-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit"
                                    data-id="<?= (int)$baris['id'] ?>"
                                    data-lapangan="<?= (int)$baris['id_lapangan'] ?>"
                                    data-tanggal="<?= htmlspecialchars($baris['tanggal']) ?>"
                                    data-jam-mulai="<?= substr($baris['jam_mulai'], 0, 5) ?>"
                                    data-jam-selesai="<?= substr($baris['jam_selesai'], 0, 5) ?>"
                                    data-status="<?= htmlspecialchars($baris['status']) ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="../process/jadwal_process.php" class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id" value="<?= (int)$baris['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($daftar_jadwal)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data jadwal.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="../process/jadwal_process.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="mb-3">
                    <label class="form-label">Lapangan</label>
                    <select name="id_lapangan" class="form-select" required>
                        <?php foreach ($daftar_lapangan as $id => $nama): ?>
                            <option value="<?= (int)$id ?>"><?= htmlspecialchars($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="tersedia">Tersedia</option>
                        <option value="tutup">Tutup</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="../process/jadwal_process.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jadwal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3">
                    <label class="form-label">Lapangan</label>
                    <select name="id_lapangan" id="edit_lapangan" class="form-select" required>
                        <?php foreach ($daftar_lapangan as $id => $nama): ?>
                            <option value="<?= (int)$id ?>"><?= htmlspecialchars($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="edit_jam_selesai" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="edit_status" class="form-select" required>
                        <option value="tersedia">Tersedia</option>
                        <option value="dipesan">Dipesan</option>
                        <option value="tutup">Tutup</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_lapangan').value = btn.dataset.lapangan;
        document.getElementById('edit_tanggal').value = btn.dataset.tanggal;
        document.getElementById('edit_jam_mulai').value = btn.dataset.jamMulai;
        document.getElementById('edit_jam_selesai').value = btn.dataset.jamSelesai;
        document.getElementById('edit_status').value = btn.dataset.status;
    });
});
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>