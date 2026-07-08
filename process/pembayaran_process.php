<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin', 'Kasir']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Pembayaran.php';

$pembayaranModel = new Pembayaran($koneksi);
$aksi            = $_POST['aksi'] ?? '';
$id_admin        = (int) $_SESSION['id_admin'];

switch ($aksi) {

    case 'tambah':
        $id_reservasi = (int) ($_POST['id_reservasi'] ?? 0);
        $metode       = $_POST['metode'] ?? '';
        $jumlah_bayar = (float) ($_POST['jumlah_bayar'] ?? 0);
        $metode_valid = ['tunai', 'transfer', 'qris'];

        if ($id_reservasi <= 0 || !in_array($metode, $metode_valid, true) || $jumlah_bayar <= 0) {
            $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
        } else {
            $status_awal = $metode === 'tunai' ? 'diverifikasi' : 'menunggu';
            $berhasil = $pembayaranModel->tambah($id_reservasi, $metode, null, $jumlah_bayar, $status_awal, $id_admin);
            $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
                $berhasil ? 'Pembayaran berhasil dicatat.' : 'Gagal mencatat pembayaran.';
        }
        break;

    case 'verifikasi':
        $id = (int) ($_POST['id'] ?? 0);
        $berhasil = $pembayaranModel->verifikasi($id, $id_admin);
        $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
            $berhasil ? 'Pembayaran berhasil diverifikasi.' : 'Gagal memverifikasi pembayaran.';
        break;

    case 'tolak':
        $id = (int) ($_POST['id'] ?? 0);
        $berhasil = $pembayaranModel->tolak($id, $id_admin);
        $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
            $berhasil ? 'Pembayaran ditolak.' : 'Gagal menolak pembayaran.';
        break;

    default:
        $_SESSION['pesan_error'] = 'Aksi tidak dikenali.';
        break;
}

header('Location: ../pages/pembayaran.php');
exit;