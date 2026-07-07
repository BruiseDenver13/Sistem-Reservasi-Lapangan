<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/KategoriLapangan.php';

$judul_halaman  = 'Kategori Lapangan';
$kategoriModel  = new KategoriLapangan($koneksi);

$pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
$pesan_error  = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_sukses'], $_SESSION['pesan_error']);

$daftar_kategori = $kategoriModel->ambilSemua();

require_once __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../components/navbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Kategori Lapangan</h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
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
                    <th>#</th>
                    <th>Nama Kategori</th>
                    <th>Keterangan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($daftar_kategori as $baris): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($baris['nama']) ?></td>
                        <td><?= htmlspecialchars($baris['keterangan'] ?? '-') ?></td>
                        <td class="text-end">
                            <button type="button"
                                    class="btn btn-sm btn-outline-warning btn-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit"
                                    data-id="<?= (int)$baris['id'] ?>"
                                    data-nama="<?= htmlspecialchars($baris['nama']) ?>"
                                    data-keterangan="<?= htmlspecialchars($baris['keterangan'] ?? '') ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="../process/kategori_process.php" class="d-inline"
                                  onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id" value="<?= (int)$baris['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($daftar_kategori)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data kategori.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="../process/kategori_process.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2"></textarea>
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
        <form method="POST" action="../process/kategori_process.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
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
        document.getElementById('edit_nama').value = btn.dataset.nama;
        document.getElementById('edit_keterangan').value = btn.dataset.keterangan;
    });
});
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>