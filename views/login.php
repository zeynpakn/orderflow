<?php
session_start();

// 1. GÜNCEL DOSYA YOLU İLE DB BAĞLANTISINI DAHİL ET
// views/login.php'den app/core/db.php'ye ulaşmak için yol: '../app/core/db.php'
include '../app/core/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 2. GEÇİCİ KONTROL YERİNE GERÇEK VERİTABANI SORGUSU
    
    // Kullanıcıyı e-posta adresine göre bul
    // $pdo değişkeni artık db.php'den geliyor
    $stmt = $pdo->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Şifre Kontrolü: Veritabanındaki HASH'lenmiş şifre ile girilen şifreyi karşılaştır
        if (password_verify($password, $user['password'])) {
            // Giriş Başarılı
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            
            // Anasayfaya yönlendir
            header("Location: index.php");
            exit;
        } else {
            // Şifre Hatalı
            $error = "Hatalı e-posta veya şifre.";
        }
    } else {
        // Kullanıcı Bulunamadı
        $error = "Hatalı e-posta veya şifre.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Lusso Coffee</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../public/css/style.css">

    <style>
        /* CSS Stilleri Buraya Taşınabilir VEYA style.css'te bırakılabilir */
        /* Login sayfasına özel override stilleri */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1497935586351-b67a49e012bf?ixlib=rb-1.2.1&auto=format&fit=crop&w=1351&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.1); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            color: white;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 12px;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            background: #fff;
            box-shadow: 0 0 0 3px var(--secondary-color);
        }

        h2.brand-title {
            font-family: 'Playfair Display', serif;
            color: var(--secondary-color); 
        }

        .btn-login {
            background-color: var(--primary-color);
            color: white;
            padding: 12px;
            border-radius: 30px;
            transition: all 0.3s;
            border: none;
            font-weight: 600;
        }

        .btn-login:hover {
            background-color: var(--secondary-color);
            color: #fff;
            transform: translateY(-2px);
        }

        .input-group-text {
            background: var(--secondary-color);
            color: white;
            border: none;
        }
        
        a { color: var(--secondary-color); text-decoration: none; }
        a:hover { color: white; }
    </style>
</head>
<body>

    <div class="login-card animate__animated animate__fadeInUp">
        <div class="text-center mb-4">
            <h2 class="brand-title mb-2"><i class="fas fa-coffee me-2"></i>Lusso</h2>
            <p class="text-white-50">Sipariş Otomasyonu</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger py-2 text-center small">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
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