<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin', 'Kasir']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Reservasi.php';
require_once __DIR__ . '/../models/Jadwal.php';

$reservasiModel = new Reservasi($koneksi);
$jadwalModel    = new Jadwal($koneksi);
$aksi           = $_POST['aksi'] ?? '';
$id_admin       = (int) $_SESSION['id_admin'];

switch ($aksi) {

    case 'tambah':
        $id_jadwal    = (int) ($_POST['id_jadwal'] ?? 0);
        $nama_pemesan = trim($_POST['nama_pemesan'] ?? '');
        $no_hp        = trim($_POST['no_hp'] ?? '');
        $keterangan   = trim($_POST['keterangan'] ?? '');

        if ($id_jadwal <= 0 || $nama_pemesan === '' || $no_hp === '') {
            $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
            break;
        }

        $jadwal = $reservasiModel->ambilJadwalUntukReservasi($id_jadwal);

        if (!$jadwal || $jadwal['status'] !== 'tersedia') {
            $_SESSION['pesan_error'] = 'Jadwal yang dipilih sudah tidak tersedia.';
            break;
        }

        $durasi_jam  = (strtotime($jadwal['jam_selesai']) - strtotime($jadwal['jam_mulai'])) / 3600;
        $total_bayar = $durasi_jam * (float) $jadwal['harga_per_jam'];

        $berhasil = $reservasiModel->tambah($id_jadwal, $nama_pemesan, $no_hp, $total_bayar, $keterangan, $id_admin);

        if ($berhasil) {
            $jadwalModel->update(
                $id_jadwal, $jadwal['id_lapangan'], $jadwal['tanggal'],
                $jadwal['jam_mulai'], $jadwal['jam_selesai'], 'dipesan'
            );
            $_SESSION['pesan_sukses'] = 'Reservasi berhasil dibuat.';
        } else {
            $_SESSION['pesan_error'] = 'Gagal membuat reservasi.';
        }
        break;

    case 'konfirmasi':
    case 'selesai':
        $id     = (int) ($_POST['id'] ?? 0);
        $status = $aksi === 'konfirmasi' ? 'dikonfirmasi' : 'selesai';

        $berhasil = $reservasiModel->updateStatus($id, $status);
        $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
            $berhasil ? 'Status reservasi berhasil diperbarui.' : 'Gagal memperbarui status.';
        break;

    case 'batalkan':
        $id = (int) ($_POST['id'] ?? 0);
        $reservasi = $reservasiModel->cariById($id);

        if (!$reservasi) {
            $_SESSION['pesan_error'] = 'Data reservasi tidak ditemukan.';
            break;
        }

        $berhasil = $reservasiModel->updateStatus($id, 'dibatalkan');

        if ($berhasil) {
            $jadwal = $jadwalModel->cariById((int) $reservasi['id_jadwal']);
            if ($jadwal) {
                $jadwalModel->update(
                    $jadwal['id'], $jadwal['id_lapangan'], $jadwal['tanggal'],
                    $jadwal['jam_mulai'], $jadwal['jam_selesai'], 'tersedia'
                );
            }
            $_SESSION['pesan_sukses'] = 'Reservasi berhasil dibatalkan, jadwal kembali tersedia.';
        } else {
            $_SESSION['pesan_error'] = 'Gagal membatalkan reservasi.';
        }
        break;

    case 'hapus':
        $id = (int) ($_POST['id'] ?? 0);
        $berhasil = $reservasiModel->hapus($id);
        $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
            $berhasil ? 'Reservasi berhasil dihapus.' : 'Gagal menghapus reservasi.';
        break;

    default:
        $_SESSION['pesan_error'] = 'Aksi tidak dikenali.';
        break;
}

header('Location: ../pages/reservasi.php');
exit;