<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Jadwal.php';

$jadwalModel  = new Jadwal($koneksi);
$aksi         = $_POST['aksi'] ?? '';
$status_valid = ['tersedia', 'dipesan', 'tutup'];

switch ($aksi) {

    case 'tambah':
        $id_lapangan = (int) ($_POST['id_lapangan'] ?? 0);
        $tanggal     = $_POST['tanggal'] ?? '';
        $jam_mulai   = $_POST['jam_mulai'] ?? '';
        $jam_selesai = $_POST['jam_selesai'] ?? '';
        $status      = $_POST['status'] ?? 'tersedia';

        if ($id_lapangan <= 0 || $tanggal === '' || $jam_mulai === '' || $jam_selesai === '' || !in_array($status, $status_valid, true)) {
            $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
        } elseif ($jam_mulai >= $jam_selesai) {
            $_SESSION['pesan_error'] = 'Jam selesai harus lebih besar dari jam mulai.';
        } elseif ($jadwalModel->cekBentrok($id_lapangan, $tanggal, $jam_mulai, $jam_selesai)) {
            $_SESSION['pesan_error'] = 'Jadwal bentrok dengan slot yang sudah ada di lapangan dan tanggal yang sama.';
        } else {
            $berhasil = $jadwalModel->tambah($id_lapangan, $tanggal, $jam_mulai, $jam_selesai, $status);
            $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
                $berhasil ? 'Jadwal berhasil ditambahkan.' : 'Gagal menambahkan jadwal.';
        }
        break;

    case 'edit':
        $id          = (int) ($_POST['id'] ?? 0);
        $id_lapangan = (int) ($_POST['id_lapangan'] ?? 0);
        $tanggal     = $_POST['tanggal'] ?? '';
        $jam_mulai   = $_POST['jam_mulai'] ?? '';
        $jam_selesai = $_POST['jam_selesai'] ?? '';
        $status      = $_POST['status'] ?? 'tersedia';

        if ($id <= 0 || $id_lapangan <= 0 || $tanggal === '' || $jam_mulai === '' || $jam_selesai === '' || !in_array($status, $status_valid, true)) {
            $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
        } elseif ($jam_mulai >= $jam_selesai) {
            $_SESSION['pesan_error'] = 'Jam selesai harus lebih besar dari jam mulai.';
        } elseif ($jadwalModel->cekBentrok($id_lapangan, $tanggal, $jam_mulai, $jam_selesai, $id)) {
            $_SESSION['pesan_error'] = 'Jadwal bentrok dengan slot yang sudah ada di lapangan dan tanggal yang sama.';
        } else {
            $berhasil = $jadwalModel->update($id, $id_lapangan, $tanggal, $jam_mulai, $jam_selesai, $status);
            $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
                $berhasil ? 'Jadwal berhasil diperbarui.' : 'Gagal memperbarui jadwal.';
        }
        break;

    case 'hapus':
        $id = (int) ($_POST['id'] ?? 0);

        if ($jadwalModel->sedangDipakai($id)) {
            $_SESSION['pesan_error'] = 'Jadwal tidak bisa dihapus karena sudah memiliki data reservasi.';
        } else {
            $berhasil = $jadwalModel->hapus($id);
            $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
                $berhasil ? 'Jadwal berhasil dihapus.' : 'Gagal menghapus jadwal.';
        }
        break;

    default:
        $_SESSION['pesan_error'] = 'Aksi tidak dikenali.';
        break;
}

header('Location: ../pages/jadwal.php');
exit;