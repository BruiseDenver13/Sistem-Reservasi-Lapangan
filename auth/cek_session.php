<?php
require_once __DIR__ . '/../config/session.php'; // sudah handle session_start() + timeout
 
// 1. Cek apakah user sudah login
if (!isset($_SESSION['id_admin'])) {
    header('Location: ' . dirname($_SERVER['PHP_SELF'], 2) . '/auth/login.php');
    exit;
}
 
/**
 * @param array $role_diizinkan Contoh: ['Superadmin', 'Admin']
 */
function cek_role(array $role_diizinkan): void
{
    if (!in_array($_SESSION['role'], $role_diizinkan, true)) {
        // Role tidak diizinkan akses halaman ini
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
              <title>Akses Ditolak</title>
              <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
              </head><body class="d-flex align-items-center justify-content-center vh-100 bg-light">
              <div class="text-center">
                  <h1 class="display-6">403</h1>
                  <p class="text-muted">Anda tidak memiliki akses ke halaman ini.</p>
                  <a href="dashboard.php" class="btn btn-primary btn-sm">Kembali ke Dashboard</a>
              </div></body></html>';
        exit;
    }
}