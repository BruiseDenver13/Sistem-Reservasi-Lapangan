<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Lapangan.php';
require_once __DIR__ . '/../models/KategoriLapangan.php';

$judul_halaman = 'Data Lapangan';
$lapanganModel = new Lapangan($koneksi);
$kategoriModel = new KategoriLapangan($koneksi);

$pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
$pesan_error  = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_sukses'], $_SESSION['pesan_error']);

$daftar_lapangan  = $lapanganModel->ambilSemua();
$daftar_kategori  = $kategoriModel->ambilUntukDropdown();

require_once __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../components/navbar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Data Lapangan</h4>
    <?php if (empty($daftar_kategori)): ?>
        <span class="text-muted small">Tambahkan kategori dulu sebelum menambah lapangan.</span>
    <?php else: ?>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Lapangan
        </button>
    <?php endif; ?>
</div>

<?php if ($pesan_sukses !== ''): ?>
    <div class="alert alert-success py-2"><?= htmlspecialchars($pesan_sukses) ?></div>
<?php endif; ?>
<?php if ($pesan_error !== ''): ?>
    <div class="alert alert-danger py-2"><?= htmlspecialchars($pesan_error) ?></div>
<?php endif; ?>

<div class="row g-3">
    <?php foreach ($daftar_lapangan as $baris): ?>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <?php if (!empty($baris['foto']) && file_exists(__DIR__ . '/../assets/uploads/' . $baris['foto'])): ?>
                    <img src="../assets/uploads/<?= htmlspecialchars($baris['foto']) ?>" class="card-img-top" style="height:160px; object-fit:cover;" alt="Foto lapangan">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:160px;">
                        <i class="bi bi-image text-muted" style="font-size:2rem;"></i>
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="card-title mb-1"><?= htmlspecialchars($baris['nama']) ?></h6>
                        <span class="badge <?= $baris['status'] === 'aktif' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= htmlspecialchars($baris['status']) ?>
                        </span>
                    </div>
                    <div class="text-muted small mb-2"><?= htmlspecialchars($baris['nama_kategori']) ?></div>
                    <div class="fw-bold mb-2">Rp <?= number_format((float)$baris['harga_per_jam'], 0, ',', '.') ?> / jam</div>
                    <p class="small text-muted mb-3"><?= htmlspecialchars($baris['keterangan'] ?? '-') ?></p>

                    <div class="d-flex gap-2">
                        <button type="button"
                                class="btn btn-sm btn-outline-warning btn-edit"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEdit"
                                data-id="<?= (int)$baris['id'] ?>"
                                data-kategori="<?= (int)$baris['id_kategori'] ?? '' ?>"
                                data-nama="<?= htmlspecialchars($baris['nama']) ?>"
                                data-harga="<?= (float)$baris['harga_per_jam'] ?>"
                                data-keterangan="<?= htmlspecialchars($baris['keterangan'] ?? '') ?>"
                                data-status="<?= htmlspecialchars($baris['status']) ?>">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <form method="POST" action="../process/lapangan_process.php"
                              onsubmit="return confirm('Yakin ingin menghapus lapangan ini?');">
                            <input type="hidden" name="aksi" value="hapus">
                            <input type="hidden" name="id" value="<?= (int)$baris['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($daftar_lapangan)): ?>
        <div class="col-12 text-center text-muted py-4">Belum ada data lapangan.</div>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="../process/lapangan_process.php" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Lapangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="tambah">
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="id_kategori" class="form-select" required>
                        <?php foreach ($daftar_kategori as $id => $nama): ?>
                            <option value="<?= (int)$id ?>"><?= htmlspecialchars($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lapangan</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga per Jam (Rp)</label>
                    <input type="number" name="harga_per_jam" class="form-control" min="0" step="1000" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto</label>
                    <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <div class="form-text">Maks 2MB, format JPG/PNG/WEBP.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
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
        <form method="POST" action="../process/lapangan_process.php" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Lapangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="aksi" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="id_kategori" id="edit_kategori" class="form-select" required>
                        <?php foreach ($daftar_kategori as $id => $nama): ?>
                            <option value="<?= (int)$id ?>"><?= htmlspecialchars($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lapangan</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga per Jam (Rp)</label>
                    <input type="number" name="harga_per_jam" id="edit_harga" class="form-control" min="0" step="1000" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ganti Foto</label>
                    <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <div class="form-text">Kosongkan jika tidak ingin mengganti foto lama.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="edit_status" class="form-select" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
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
        document.getElementById('edit_kategori').value = btn.dataset.kategori;
        document.getElementById('edit_nama').value = btn.dataset.nama;
        document.getElementById('edit_harga').value = btn.dataset.harga;
        document.getElementById('edit_keterangan').value = btn.dataset.keterangan;
        document.getElementById('edit_status').value = btn.dataset.status;
    });
});
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>