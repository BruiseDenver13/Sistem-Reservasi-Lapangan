<?php
/**
 * File: components/header.php
 * Fungsi: Membuka layout HTML (head + pembuka body) untuk semua halaman admin
 *
 * Cara pakai di halaman admin, contoh pages/dashboard.php:
 *
 *   require_once __DIR__ . '/../auth/cek_session.php';
 *   $judul_halaman = 'Dashboard';
 *   require_once __DIR__ . '/../components/header.php';
 *   require_once __DIR__ . '/../components/sidebar.php';
 *   require_once __DIR__ . '/../components/navbar.php';
 *
 *   // ... konten halaman di sini ...
 *
 *   require_once __DIR__ . '/../components/footer.php';
 */
 
// Judul halaman default kalau tidak diset di halaman pemanggil
$judul_halaman = $judul_halaman ?? 'Reservasi Futsal';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($judul_halaman) ?> - Reservasi Futsal</title>
 
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (untuk ikon menu sidebar) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="d-flex" id="wrapper">