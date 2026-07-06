<?php

function upload_foto(array $file, string $folder_tujuan): array
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['sukses' => true, 'nama_file' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['sukses' => false, 'pesan' => 'Terjadi kesalahan saat upload foto.'];
    }

    $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
    $ukuran_maks        = 2 * 1024 * 1024;

    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensi_diizinkan, true)) {
        return ['sukses' => false, 'pesan' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.'];
    }
    if ($file['size'] > $ukuran_maks) {
        return ['sukses' => false, 'pesan' => 'Ukuran foto maksimal 2MB.'];
    }

    if (@getimagesize($file['tmp_name']) === false) {
        return ['sukses' => false, 'pesan' => 'File yang diupload bukan gambar yang valid.'];
    }

    if (!is_dir($folder_tujuan)) {
        mkdir($folder_tujuan, 0755, true);
    }

    $nama_file = 'lapangan_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ekstensi;
    $path_tujuan = rtrim($folder_tujuan, '/') . '/' . $nama_file;

    if (!move_uploaded_file($file['tmp_name'], $path_tujuan)) {
        return ['sukses' => false, 'pesan' => 'Gagal menyimpan file foto ke server.'];
    }

    return ['sukses' => true, 'nama_file' => $nama_file];
}