# orderflow
Çok katlı kafeler için sipariş sitesi.

# Database Bağlantısı
- core dosyasının içine 'config.local.php' dosyası oluşturun.
- Alttaki kodu içine kopyalayın ve değişkenlerin karşılıklarını kendi bilgilerinizle doldurun:

```php
<?php
// Bu dosya, hassas/yerel veritabanı bilgilerini tutar
$local_db_host = 'hostname';
$local_db_name = 'database_adı';
$local_db_user = 'username';
$local_db_pass = 'password';
?>
