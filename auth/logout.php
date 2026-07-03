<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
// Hapus semua data session
$_SESSION = [];
session_unset();
session_destroy();
 
// Hapus cookie session di browser (opsional, tapi lebih bersih)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
 
header('Location: login.php');
exit;
 