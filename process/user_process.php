<?php

require_once __DIR__ . '/../auth/cek_session.php';
cek_role(['Superadmin']);

require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../models/Admin.php';

$adminModel = new Admin($koneksi);
$aksi       = $_POST['aksi'] ?? '';
$role_valid = ['Superadmin', 'Admin', 'Kasir'];

switch ($aksi) {

    case 'tambah':
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $nama     = trim($_POST['nama'] ?? '');
        $role     = $_POST['role'] ?? '';

        if ($username === '' || $password === '' || $nama === '' || !in_array($role, $role_valid, true)) {
            $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
        } elseif (strlen($password) < 6) {
            $_SESSION['pesan_error'] = 'Password minimal 6 karakter.';
        } elseif ($adminModel->usernameSudahAda($username)) {
            $_SESSION['pesan_error'] = 'Username sudah digunakan, silakan pilih username lain.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $berhasil = $adminModel->tambah($username, $password_hash, $nama, $role);
            $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
                $berhasil ? 'User baru berhasil ditambahkan.' : 'Gagal menambahkan user. Silakan coba lagi.';
        }
        break;

    case 'edit':
        $id       = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? ''; 
        $nama     = trim($_POST['nama'] ?? '');
        $role     = $_POST['role'] ?? '';

        if ($id <= 0 || $username === '' || $nama === '' || !in_array($role, $role_valid, true)) {
            $_SESSION['pesan_error'] = 'Semua field wajib diisi dengan benar.';
        } elseif ($adminModel->usernameSudahAda($username, $id)) {
            $_SESSION['pesan_error'] = 'Username sudah digunakan oleh user lain.';
        } elseif ($password !== '' && strlen($password) < 6) {
            $_SESSION['pesan_error'] = 'Password minimal 6 karakter.';
        } else {
            $password_hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;
            $berhasil = $adminModel->update($id, $username, $nama, $role, $password_hash);

            if ($berhasil) {
                $_SESSION['pesan_sukses'] = 'Data user berhasil diperbarui.';
                if ($id === (int)$_SESSION['id_admin']) {
                    $_SESSION['username'] = $username;
                    $_SESSION['nama']     = $nama;
                    $_SESSION['role']     = $role;
                }
            } else {
                $_SESSION['pesan_error'] = 'Gagal memperbarui data user.';
            }
        }
        break;

    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id === (int)$_SESSION['id_admin']) {
            $_SESSION['pesan_error'] = 'Anda tidak bisa menghapus akun Anda sendiri.';
        } elseif ($id > 0) {
            $berhasil = $adminModel->hapus($id);
            $_SESSION[$berhasil ? 'pesan_sukses' : 'pesan_error'] =
                $berhasil ? 'User berhasil dihapus.' : 'Gagal menghapus user. Pastikan user tidak terkait data lain.';
        }
        break;

    default:
        $_SESSION['pesan_error'] = 'Aksi tidak dikenali.';
        break;
}

header('Location: ../pages/kelola_user.php');
exit;