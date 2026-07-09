<?php
require_once __DIR__ . '/../auth/cek_session.php';
require_once __DIR__ . '/../config/koneksi.php';
 
$judul_halaman = 'Dashboard';
 
$total_lapangan = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM lapangan"))['jumlah'];
$total_kategori = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM kategori_lapangan"))['jumlah'];
 
$reservasi_hari_ini = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS jumlah
    FROM reservasi r
    JOIN jadwal j ON r.id_jadwal = j.id
    WHERE j.tanggal = CURDATE()
"))['jumlah'];
 
$pendapatan_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COALESCE(SUM(jumlah_bayar), 0) AS total
    FROM pembayaran
    WHERE DATE(tanggal_bayar) = CURDATE() AND status_verifikasi = 'diverifikasi'
"))['total'];
 
$hasil_grafik = mysqli_query($koneksi, "
    SELECT j.tanggal, COUNT(*) AS jumlah
    FROM reservasi r
    JOIN jadwal j ON r.id_jadwal = j.id
    WHERE j.tanggal >= CURDATE() - INTERVAL 6 DAY
    GROUP BY j.tanggal
    ORDER BY j.tanggal ASC
");
 
$data_per_tanggal = [];
for ($i = 6; $i >= 0; $i--) {
    $tanggal = date('Y-m-d', strtotime("-$i day"));
    $data_per_tanggal[$tanggal] = 0;
}
while ($baris = mysqli_fetch_assoc($hasil_grafik)) {
    $data_per_tanggal[$baris['tanggal']] = (int) $baris['jumlah'];
}
 
$label_grafik = array_map(fn($tgl) => date('d M', strtotime($tgl)), array_keys($data_per_tanggal));
$angka_grafik = array_values($data_per_tanggal);
 
require_once __DIR__ . '/../components/header.php';
require_once __DIR__ . '/../components/sidebar.php';
require_once __DIR__ . '/../components/navbar.php';
?>
 
<h4 class="mb-4">Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?> </h4>
 
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Total Lapangan</div>
                <div class="fs-3 fw-bold"><?= $total_lapangan ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Total Kategori</div>
                <div class="fs-3 fw-bold"><?= $total_kategori ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Reservasi Hari Ini</div>
                <div class="fs-3 fw-bold"><?= $reservasi_hari_ini ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-muted small">Pendapatan Hari Ini</div>
                <div class="fs-5 fw-bold">Rp <?= number_format((float)$pendapatan_hari_ini, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
</div>
 
<div class="card shadow-sm border-0">
    <div class="card-body">
        <h6 class="mb-3">Reservasi 7 Hari Terakhir</h6>
        <canvas id="grafikReservasi" height="90"></canvas>
    </div>
</div>
 
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('grafikReservasi');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($label_grafik) ?>,
            datasets: [{
                label: 'Jumlah Reservasi',
                data: <?= json_encode($angka_grafik) ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.15)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
</script>
 
<?php require_once __DIR__ . '/../components/footer.php'; ?>
 