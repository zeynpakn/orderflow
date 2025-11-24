<?php
// 1. GİZLİ YEREL YAPILANDIRMAYI DAHİL ET
// Eğer config.local.php dosyası varsa (yani siz yerel ortamınızda çalışıyorsanız),
// o dosyadaki değişkenleri kullan. Bu dosya .gitignore'da olduğu için paylaşılmaz.
if (file_exists(__DIR__ . '/config.local.php')) {
    include __DIR__ . '/config.local.php';

    // Gizli dosyadan gelen değişkenleri ayarla
    $host = $local_db_host;
    $db   = $local_db_name;
    $user = $local_db_user;
    $pass = $local_db_pass;

} 
else{
    throw new \Exception("HATA: Veritabanı bağlantısı için gerekli 'config.local.php' dosyası bulunamadı. 
    Lütfen bu dosyayı oluşturun ve yerel veritabanı bilgilerinizi girin.");
}

$charset = 'utf8mb4';

// Geri kalan bağlantı kodu, yukarıda ayarlanan $host, $db, $user ve $pass değişkenlerini kullanır.
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
}
catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
     // exit('Veritabanı bağlantı hatası...');
}
?>