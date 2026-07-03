<?php
require_once __DIR__ . '/../config/koneksi.php';
 
// Jika session belum jalan (session.php akan redirect kalau timeout, jadi start manual dulu)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['id_admin'])) {
    header('Location: ../pages/dashboard.php');
    exit;
}
 
$pesan_error = '';
 
// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
 
    if ($username === '' || $password === '') {
        $pesan_error = 'Username dan password wajib diisi.';
    } else {
        // Prepared statement - anti SQL Injection
        $stmt = mysqli_prepare($koneksi, "SELECT id, username, password, nama, role FROM admin WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $data_admin = mysqli_fetch_assoc($hasil);
        mysqli_stmt_close($stmt);
 
        if ($data_admin && password_verify($password, $data_admin['password'])) {
            // Login berhasil - simpan data ke session
            session_regenerate_id(true); // cegah session fixation
            $_SESSION['id_admin'] = $data_admin['id'];
            $_SESSION['username']  = $data_admin['username'];
            $_SESSION['nama']      = $data_admin['nama'];
            $_SESSION['role']      = $data_admin['role'];
            $_SESSION['waktu_aktif_terakhir'] = time();
            $_SESSION['dibuat_pada'] = time();
 
            header('Location: ../pages/dashboard.php');
            exit;
        } else {
            $pesan_error = 'Username atau password salah.';
        }
    }
}
 
// Tampilkan pesan tambahan dari redirect (misal session habis)
if (isset($_GET['pesan']) && $_GET['pesan'] === 'session_habis') {
    $pesan_error = 'Sesi Anda telah habis, silakan login kembali.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Reservasi Futsal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
            <div class="card-body p-4">
                <h4 class="text-center mb-4">⚽ Login Admin</h4>
 
                <?php if ($pesan_error !== ''): ?>
                    <div class="alert alert-danger py-2">
                        <?= htmlspecialchars($pesan_error) ?>
                    </div>
                <?php endif; ?>
 
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
 
                <div class="text-center mt-3">
                    <a href="../public/index.php" class="small text-decoration-none">← Kembali ke Website</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>