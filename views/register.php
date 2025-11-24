<?php
session_start();
// include 'db.php'; // Veritabanı bağlantısı ileride buraya gelecek

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basit boş alan kontrolü
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        // BURADA VERİTABANI KAYDI YAPILACAK
        // Şimdilik işlem başarılıymış gibi mesaj veriyoruz:
        $success = "Kaydınız başarıyla oluşturuldu! Giriş yapabilirsiniz.";
        
        // İpucu: İlerde buraya şu kodu ekleyeceğiz:
        /*
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_pass]);
        */
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Lusso Coffee</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="style.css">

    <style>
        /* Login sayfasıyla aynı tasarım kodları */
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
            <h2 class="brand-title mb-2"><i class="fas fa-user-plus me-2"></i>Lusso</h2>
            <p class="text-white-50">Aramıza Katılın</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger py-2 text-center small">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="alert alert-success py-2 text-center small">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" name="name" placeholder="Ad Soyad" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="E-posta Adresi" required>
                </div>
            </div>

            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Şifre Belirle" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">
                Kayıt Ol
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-white-50">Zaten hesabın var mı? <a href="login.php" class="fw-bold">Giriş Yap</a></small>
        </div>
        <div class="text-center mt-2">
            <a href="index.php" class="small"><i class="fas fa-arrow-left me-1"></i> Anasayfaya Dön</a>
        </div>
    </div>

</body>
</html>