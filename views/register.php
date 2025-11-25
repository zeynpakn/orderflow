<?php
session_start();

if (file_exists('../app/core/db.php')) {
    require_once '../app/core/db.php';
} else {
    die("HATA: Veritabanı dosyası bulunamadı. Yol: ../app/core/db.php");
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($name) || empty($surname) || empty($email) || empty($password)) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        try {
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$email]);
            
            if ($checkStmt->rowCount() > 0) {
                $error = "Bu e-posta adresi zaten sistemde kayıtlı.";
            } else {
                $sql = "INSERT INTO users (name, surname, email, password, role, is_active) VALUES (?, ?, ?, ?, 'user', 1)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$name, $surname, $email, $password])) {
                    $success = "Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...";
                    header("refresh:2;url=login.php"); 
                } else {
                    $error = "Kayıt sırasında teknik bir hata oluştu.";
                }
            }
        } catch (PDOException $e) {
            $error = "Veritabanı Hatası: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - OrderFlow</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/style_index.css">
</head>
<body class="bg-login-image">

    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="mb-2" style="font-family: 'Playfair Display', serif;">OrderFlow</h2>
            <p class="text-white-50">Aramıza Katılın</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger py-2 text-center small"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="alert alert-success py-2 text-center small"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" name="name" placeholder="Adınız" required>
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                <input type="text" class="form-control" name="surname" placeholder="Soyadınız" required>
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" name="email" placeholder="E-posta Adresi" required>
            </div>

            <div class="mb-4 input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="password" placeholder="Şifre Belirle" required>
            </div>

            <button type="submit" class="btn btn-login">Kayıt Ol</button>
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