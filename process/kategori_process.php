<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin', 'Admin']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/KategoriLapangan.php';

$kategoriModel = new KategoriLapangan($koneksi);
$aksi          = $_POST['aksi'] ?? '';

switch ($aksi) {

    case 'tambah':
        $nama       = trim($_POST['nama'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');

        if ($nama === '') {
            $_SESSION['pesan_error'] = 'Nama kategori wajib diisi.';
        } else {
            $berhasil = $kategoriModel->tambah($nama, $keterangan);
            $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
                $berhasil ? 'Kategori berhasil ditambahkan.' : 'Gagal menambahkan kategori.';
        }
        break;

    case 'edit':
        $id         = (int) ($_POST['id'] ?? 0);
        $nama       = trim($_POST['nama'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');

        if ($id <= 0 || $nama === '') {
            $_SESSION['pesan_error'] = 'Data tidak valid.';
        } else {
            $berhasil = $kategoriModel->update($id, $nama, $keterangan);
            $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
                $berhasil ? 'Kategori berhasil diperbarui.' : 'Gagal memperbarui kategori.';
        }
        break;

    case 'hapus':
        $id = (int) ($_POST['id'] ?? 0);

        if ($kategoriModel->sedangDipakai($id)) {
            $_SESSION['pesan_error'] = 'Kategori tidak bisa dihapus karena masih dipakai oleh data lapangan.';
        } else {
            $berhasil = $kategoriModel->hapus($id);
            $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
                $berhasil ? 'Kategori berhasil dihapus.' : 'Gagal menghapus kategori.';
        }
        break;

    default:
        $_SESSION['pesan_error'] = 'Aksi tidak dikenali.';
        break;
}

header('Location: ../pages/kategori_lapangan.php');
exit;