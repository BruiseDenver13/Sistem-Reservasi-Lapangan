<?php

define('SESSION_TIMEOUT', 1800);
 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
if (isset($_SESSION['waktu_aktif_terakhir'])) {
    $selisih_waktu = time() - $_SESSION['waktu_aktif_terakhir'];
 
    if ($selisih_waktu > SESSION_TIMEOUT) {
       
        $_SESSION = [];
        session_unset();
        session_destroy();
 
        header('Location: /auth/login.php?pesan=session_habis');
        exit;
    }
}
 
$_SESSION['waktu_aktif_terakhir'] = time();
 
if (!isset($_SESSION['dibuat_pada'])) {
    $_SESSION['dibuat_pada'] = time();
} elseif (time() - $_SESSION['dibuat_pada'] > 300) {
    
    session_regenerate_id(true);
    $_SESSION['dibuat_pada'] = time();
}