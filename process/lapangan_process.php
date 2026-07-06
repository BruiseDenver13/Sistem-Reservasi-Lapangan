<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Lapangan.php';
require_once __DIR__ . '/upload_process.php';

$lapanganModel   = new Lapangan($koneksi);
$aksi            = $_POST['aksi'] ?? '';
$folder_uploads  = __DIR__ . '/../assets/uploads';
$status_valid    = ['aktif', 'nonaktif'];

switch ($aksi) {

    case 'tambah':
        $id_kategori   = (int) ($_POST['id_kategori'] ?? 0);
        $nama          = trim($_POST['nama'] ?? '');
        $harga_per_jam = (float) ($_POST['harga_per_jam'] ?? 0);
        $keterangan    = trim($_POST['keterangan'] ?? '');
        $status        = $_POST['status'] ?? 'aktif';

        if ($id_kategori <= 0 || $nama === '' || $harga_per_jam <= 0 || !in_array($status, $status_valid, true)) {
            $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
            break;
        }

        $hasil_upload = upload_foto($_FILES['foto'] ?? [], $folder_uploads);
        if (!$hasil_upload['sukses']) {
            $_SESSION['pesan_error'] = $hasil_upload['pesan'];
            break;
        }

        $berhasil = $lapanganModel->tambah($id_kategori, $nama, $harga_per_jam, $hasil_upload['nama_file'], $keterangan, $status);
        $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
            $berhasil ? 'Lapangan berhasil ditambahkan.' : 'Gagal menambahkan lapangan.';
        break;

    case 'edit':
        $id            = (int) ($_POST['id'] ?? 0);
        $id_kategori   = (int) ($_POST['id_kategori'] ?? 0);
        $nama          = trim($_POST['nama'] ?? '');
        $harga_per_jam = (float) ($_POST['harga_per_jam'] ?? 0);
        $keterangan    = trim($_POST['keterangan'] ?? '');
        $status        = $_POST['status'] ?? 'aktif';

        if ($id <= 0 || $id_kategori <= 0 || $nama === '' || $harga_per_jam <= 0 || !in_array($status, $status_valid, true)) {
            $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
            break;
        }

        $hasil_upload = upload_foto($_FILES['foto'] ?? [], $folder_uploads);
        if (!$hasil_upload['sukses']) {
            $_SESSION['pesan_error'] = $hasil_upload['pesan'];
            break;
        }

        if ($hasil_upload['nama_file'] !== null) {
            $data_lama = $lapanganModel->cariById($id);
            if ($data_lama && !empty($data_lama['foto']) && file_exists($folder_uploads . '/' . $data_lama['foto'])) {
                unlink($folder_uploads . '/' . $data_lama['foto']);
            }
        }

        $berhasil = $lapanganModel->update($id, $id_kategori, $nama, $harga_per_jam, $hasil_upload['nama_file'], $keterangan, $status);
        $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
            $berhasil ? 'Lapangan berhasil diperbarui.' : 'Gagal memperbarui lapangan.';
        break;

    case 'hapus':
        $id = (int) ($_POST['id'] ?? 0);

        if ($lapanganModel->sedangDipakai($id)) {
            $_SESSION['pesan_error'] = 'Lapangan tidak bisa dihapus karena masih memiliki data jadwal/reservasi.';
            break;
        }

        $data = $lapanganModel->cariById($id);
        $berhasil = $lapanganModel->hapus($id);

        if ($berhasil) {
            if ($data && !empty($data['foto']) && file_exists($folder_uploads . '/' . $data['foto'])) {
                unlink($folder_uploads . '/' . $data['foto']);
            }
            $_SESSION['pesan_sukses'] = 'Lapangan berhasil dihapus.';
        } else {
            $_SESSION['pesan_error'] = 'Gagal menghapus lapangan.';
        }
        break;

    default:
        $_SESSION['pesan_error'] = 'Aksi tidak dikenali.';
        break;
}

header('Location: ../pages/lapangan.php');
exit;