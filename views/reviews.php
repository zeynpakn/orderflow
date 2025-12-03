<?php
session_start();
require_once '../app/core/db.php';

// --- BACKEND MANTIK BAŞLANGIÇ (KORUNDU) ---
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $rating = $_POST['rating'];
    $comment = htmlspecialchars(trim($_POST['comment']));
    
    $sql = "INSERT INTO cafe_reviews (user_id, rating, comment) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$_SESSION['user_id'], $rating, $comment])) {
        $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> Yorumunuz başarıyla paylaşıldı!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    } else {
        $msg = '<div class="alert alert-danger">Bir hata oluştu.</div>';
    }
}

$sql = "SELECT r.*, u.name, u.surname FROM cafe_reviews r 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC";
$reviews = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$average_rating = 0;
$total_reviews_count = count($reviews);
if ($total_reviews_count > 0) {
    $total_rating_sum = 0;
    foreach ($reviews as $review) {
        $total_rating_sum += $review['rating'];
    }
    $average_rating = round($total_rating_sum / $total_reviews_count, 1);
}
// --- BACKEND MANTIK BİTİŞ ---
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Yorumları - OrderFlow</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SENİN MEVCUT CSS DOSYAN -->
    <link rel="stylesheet" href="../public/css/style_index.css">
</head>

<!-- 'reviews-page' sınıfı ile özel temayı aktif ediyoruz -->
<body class="reviews-page d-flex flex-column min-vh-100">

    <!-- Header (Ana sayfa ile aynı) -->
    <?php include 'modals/header_template.php'; ?>

    <div class="container flex-grow-1 mb-5">
        
        <!-- Başlık -->
        <div class="text-center reviews-header mb-5">
            <h2 class="display-5">Misafir Deneyimleri</h2>
            <p>Kahve tutkusuyla paylaşılan her yorum bizim için değerlidir.</p>
        </div>
        
        <?php echo $msg; ?>

        <div class="row g-5">
            
            <!-- SOL PANEL: İSTATİSTİK VE FORM -->
            <div class="col-lg-4">
                <div class="review-sidebar-card">
                    
                    <!-- Puan Özeti -->
                    <div class="rating-box">
                        <div class="score"><?php echo $average_rating > 0 ? $average_rating : '-'; ?></div>
                        <div class="stars">
                            <?php 
                            $stars = round($average_rating);
                            for($i=0; $i<$stars; $i++) echo '<i class="fas fa-star"></i>'; 
                            for($i=$stars; $i<5; $i++) echo '<i class="far fa-star"></i>'; 
                            ?>
                        </div>
                        <span class="count"><?php echo $total_reviews_count; ?> Değerlendirme</span>
                    </div>

                    <!-- Yorum Formu -->
                    <h5 class="review-form-title">Deneyimini Paylaş</h5>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">PUANINIZ</label>
                                <select name="rating" class="form-select custom-select">
                                    <option value="5">☕☕☕☕☕ (Mükemmel)</option>
                                    <option value="4">☕☕☕☕ (Çok İyi)</option>
                                    <option value="3">☕☕☕ (Ortalama)</option>
                                    <option value="2">☕☕ (Gelişmeli)</option>
                                    <option value="1">☕ (Kötü)</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small text-muted fw-bold">YORUMUNUZ</label>
                                <textarea name="comment" class="form-control custom-textarea" rows="4" placeholder="Kahve tadı nasıldı? Ortamı beğendiniz mi?" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-coffee w-100">
                                Yorumu Gönder <i class="fas fa-paper-plane ms-2"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-light text-center border mb-0" style="background-color: #fdfbf7;">
                            <i class="fas fa-user-lock mb-2 d-block fs-3 text-muted"></i>
                            Yorum yazmak için <br>
                            <a href="login.php" class="fw-bold text-decoration-underline" style="color: #6F4E37;">Giriş Yapın</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- SAĞ PANEL: YORUM LİSTESİ -->
            <div class="col-lg-8">
                <?php if (empty($reviews)): ?>
                    <div class="text-center py-5 opacity-50">
                        <i class="fas fa-coffee fa-3x mb-3" style="color: #dccdc6;"></i>
                        <h4>Henüz yorum yapılmamış.</h4>
                        <p>İlk kahve deneyimini paylaşan sen ol!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="reviewer-header">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Avatar -->
                                    <div class="avatar">
                                        <?php echo mb_strtoupper(mb_substr($review['name'], 0, 1)); ?>
                                    </div>
                                    <!-- İsim ve Tarih -->
                                    <div class="info">
                                        <h6><?php echo htmlspecialchars($review['name']) . " " . mb_substr(htmlspecialchars($review['surname']), 0, 1) . "."; ?></h6>
                                        <span><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                                    </div>
                                </div>
                                <!-- Yıldızlar -->
                                <div class="stars-small">
                                    <?php 
                                    for($i=0; $i<$review['rating']; $i++) echo '<i class="fas fa-star"></i>'; 
                                    for($i=$review['rating']; $i<5; $i++) echo '<i class="far fa-star"></i>'; 
                                    ?>
                                </div>
                            </div>
                            <!-- Yorum Metni -->
                            <div class="comment-text">
                                <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <div class="mt-auto">
        <?php include 'modals/footer_template.php'; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>