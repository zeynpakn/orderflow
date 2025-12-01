<?php
session_start();

// 1. Veritabanı Bağlantısı
if (file_exists('../app/core/db.php')) {
    require_once '../app/core/db.php';
} else {
    die("HATA: Veritabanı dosyası bulunamadı. Yol: ../app/core/db.php");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ... views/login.php içindeki yönlendirme kısmı ...

if ($user) {
    if ($password === $user['password']) {
        // Session Başlat
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'] . ' ' . $user['surname'];
        $_SESSION['user_role'] = $user['role']; // 'admin' veya 'user'
        $_SESSION['user_email'] = $user['email'];
        header("Location: profile.php"); 
        exit;
    } else {
        $error = "Hatalı şifre girdiniz.";
    }
}
// ...
        } else {
            $error = "Bu e-posta adresiyle kayıtlı (veya aktif) kullanıcı bulunamadı.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - OrderFlow</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../public/css/style_index.css">
</head>
<body class="bg-login-image">

    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="mb-2" style="font-family: 'Playfair Display', serif;">OrderFlow</h2>
            <p class="text-white-50">Hoş Geldiniz</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger py-2 text-center small">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="E-posta Adresi" required>
                </div>
            </div>

            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Şifre" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">
                Giriş Yap
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-white-50">Hesabın yok mu? <a href="register.php" class="fw-bold">Kayıt Ol</a></small>
        </div>
        <div class="text-center mt-2">
            <a href="index.php" class="small"><i class="fas fa-arrow-left me-1"></i> Anasayfaya Dön</a>
        </div>
    </div>

</body>
</html>