<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin']);

require_once __DIR__ . '/../config/koneksi.php';

$judul_halaman = 'Laporan';

$dari   = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

$stmt = mysqli_prepare($koneksi, "
    SELECT r.kode, r.nama_pemesan, r.total_bayar, r.status,
           j.tanggal, l.nama AS nama_lapangan
    FROM reservasi r
    JOIN jadwal j ON r.id_jadwal = j.id
    JOIN lapangan l ON j.id_lapangan = l.id
    WHERE j.tanggal BETWEEN ? AND ?
    ORDER BY j.tanggal ASC
");
mysqli_stmt_bind_param($stmt, 'ss', $dari, $sampai);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

$daftar_laporan = [];
$total_pendapatan = 0;
while ($baris = mysqli_fetch_assoc($hasil)) {
    $daftar_laporan[] = $baris;
    if ($baris['status'] === 'selesai') {
        $total_pendapatan += (float) $baris['total_bayar'];
    }
}
mysqli_stmt_close($stmt);

require_once __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../components/navbar.php';
?>

<h4 class="mb-3">Laporan Reservasi</h4>

<form method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>" class="form-control">
    </div>
    <div class="col-auto">
        <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>" class="form-control">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>

    <div class="col-auto">
    <a href="../process/export_pdf.php?dari=<?= urlencode($dari) ?>&sampai=<?= urlencode($sampai) ?>"
       class="btn btn-outline-secondary">
        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
    </a>
</div>

</form>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="text-muted small">Total Pendapatan (status selesai)</div>
        <div class="fs-4 fw-bold">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pemesan</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($daftar_laporan as $baris): ?>
                    <tr>
                        <td><code><?= htmlspecialchars($baris['kode']) ?></code></td>
                        <td><?= htmlspecialchars($baris['nama_pemesan']) ?></td>
                        <td><?= htmlspecialchars($baris['nama_lapangan']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($baris['tanggal']))) ?></td>
                        <td>Rp <?= number_format((float)$baris['total_bayar'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($baris['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($daftar_laporan)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data di rentang tanggal ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../components/footer.php'; ?>