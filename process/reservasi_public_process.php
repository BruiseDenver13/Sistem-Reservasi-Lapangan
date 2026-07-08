<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Reservasi.php';
require_once __DIR__ . '/../models/Jadwal.php';

$reservasiModel = new Reservasi($koneksi);
$jadwalModel    = new Jadwal($koneksi);

$id_jadwal    = (int) ($_POST['id_jadwal'] ?? 0);
$nama_pemesan = trim($_POST['nama_pemesan'] ?? '');
$no_hp        = trim($_POST['no_hp'] ?? '');
$keterangan   = trim($_POST['keterangan'] ?? '');

if ($id_jadwal <= 0 || $nama_pemesan === '' || $no_hp === '') {
    $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
    header('Location: ../public/reservasi.php');
    exit;
}

$jadwal = $reservasiModel->ambilJadwalUntukReservasi($id_jadwal);

if (!$jadwal || $jadwal['status'] !== 'tersedia') {
    $_SESSION['pesan_error'] = 'Maaf, jam ini baru saja dipesan orang lain. Silakan pilih jam lain.';
    header('Location: ../public/reservasi.php');
    exit;
}

$durasi_jam  = (strtotime($jadwal['jam_selesai']) - strtotime($jadwal['jam_mulai'])) / 3600;
$total_bayar = $durasi_jam * (float) $jadwal['harga_per_jam'];

$kode = $reservasiModel->tambahPublik($id_jadwal, $nama_pemesan, $no_hp, $total_bayar, $keterangan);

if ($kode) {
    $jadwalModel->update(
        $id_jadwal, $jadwal['id_lapangan'], $jadwal['tanggal'],
        $jadwal['jam_mulai'], $jadwal['jam_selesai'], 'dipesan'
    );
    header('Location: ../public/konfirmasi.php?kode=' . urlencode($kode));
    exit;
}

$_SESSION['pesan_error'] = 'Gagal membuat reservasi, silakan coba lagi.';
header('Location: ../public/reservasi.php');
exit;