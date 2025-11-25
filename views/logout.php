<?php
// 1. Oturumu başlat (Mevcut oturumu bulmak için)
session_start();

// 2. Tüm session değişkenlerini sıfırla (Hafızayı boşalt)
$_SESSION = array();

// 3. Tarayıcıdaki session çerezini (cookie) sil (Tam güvenlik için)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Oturumu tamamen yok et
session_destroy();

// 5. Anasayfaya yönlendir
header("Location: index.php");
exit;
?>