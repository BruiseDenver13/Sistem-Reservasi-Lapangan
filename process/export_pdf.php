<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../vendor/autoload.php';

use TCPDF as TCPDFBase;

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

$total_pendapatan = 0;
$baris_tabel = '';
while ($baris = mysqli_fetch_assoc($hasil)) {
    if ($baris['status'] === 'selesai') {
        $total_pendapatan += (float) $baris['total_bayar'];
    }
    $baris_tabel .= '<tr>
        <td>' . htmlspecialchars($baris['kode']) . '</td>
        <td>' . htmlspecialchars($baris['nama_pemesan']) . '</td>
        <td>' . htmlspecialchars($baris['nama_lapangan']) . '</td>
        <td>' . htmlspecialchars(date('d M Y', strtotime($baris['tanggal']))) . '</td>
        <td align="right">Rp ' . number_format((float)$baris['total_bayar'], 0, ',', '.') . '</td>
        <td>' . htmlspecialchars($baris['status']) . '</td>
    </tr>';
}

$pdf = new TCPDFBase('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Sistem Reservasi Futsal');
$pdf->SetTitle('Laporan Reservasi');
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

$periode = htmlspecialchars(date('d M Y', strtotime($dari))) . ' s/d ' . htmlspecialchars(date('d M Y', strtotime($sampai)));

$html = '
<h3>Laporan Reservasi Lapangan Futsal</h3>
<p>Periode: ' . $periode . '</p>
<p><strong>Total Pendapatan (status selesai): Rp ' . number_format($total_pendapatan, 0, ',', '.') . '</strong></p>
<table border="1" cellpadding="5">
    <tr style="background-color:#dddddd; font-weight:bold;">
        <th>Kode</th><th>Pemesan</th><th>Lapangan</th><th>Tanggal</th><th>Total</th><th>Status</th>
    </tr>
    ' . $baris_tabel . '
</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('laporan-reservasi-' . date('Ymd') . '.pdf', 'D');
exit;