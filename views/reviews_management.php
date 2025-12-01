<?php
session_start();
require_once '../app/core/db.php';

// YETKİ KONTROLÜ
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$msg = "";

// 1. KAFE GENEL YORUMLARINI ÇEKME
$cafeReviewsSql = "SELECT r.*, u.name, u.surname FROM cafe_reviews r 
                   JOIN users u ON r.user_id = u.id 
                   ORDER BY r.created_at DESC";
$cafeReviews = $pdo->query($cafeReviewsSql)->fetchAll(PDO::FETCH_ASSOC);

// 2. SİPARİŞ BAZLI YORUMLARI ÇEKME (Müşterinin "Puanla" butonundan gelen gizli yorumlar)
$orderReviewsSql = "SELECT orv.*, u.name, u.surname, o.id as order_id, o.table_no FROM order_reviews orv
                    JOIN users u ON orv.user_id = u.id
                    JOIN orders o ON orv.order_id = o.id
                    ORDER BY orv.created_at DESC";
$orderReviews = $pdo->query($orderReviewsSql)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Geri Bildirim Yönetimi - OrderFlow Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/style_index.css"> 
</head>
<body class="profile-page d-flex flex-column min-vh-100">

    <?php include 'header_template.php'; ?>

    <div class="container flex-grow-1 my-5" style="padding-top: 50px;">
        
        <?php echo $msg; ?>

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-list-alt me-2"></i> Sipariş Akışı
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="products_management.php">
                    <i class="fas fa-mug-hot me-2"></i> Ürün & Fiyat Yönetimi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active">
                    <i class="fas fa-comments me-2"></i> Müşteri Geri Bildirimleri
                </a>
            </li>
        </ul>
        <h2 class="mb-5" style="color: #6F4E37; font-family: 'Playfair Display', serif;"><i class="fas fa-comments me-2"></i>Müşteri Geri Bildirimleri</h2>
        
        <div class="card admin-card border-0 shadow-sm mb-5">
            <div class="card-header table-header-coffee">
                <h5 class="mb-0">Gizli Sipariş Değerlendirmeleri</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($orderReviews)): ?>
                    <p class="p-4 text-center text-muted">Henüz sipariş bazlı gizli geri bildirim yapılmamış.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sipariş #</th>
                                    <th>Masa #</th>
                                    <th>Müşteri</th>
                                    <th>Puan</th>
                                    <th>Yorum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderReviews as $review): ?>
                                <tr>
                                    <td class="fw-bold"><a href="#" class="text-primary">#<?php echo $review['order_id']; ?></a></td>
                                    <td><span class="badge bg-danger"><?php echo $review['table_no']; ?></span></td>
                                    <td><?php echo htmlspecialchars($review['name'] . ' ' . $review['surname']); ?></td>
                                    <td>
                                        <?php for($i=0; $i<$review['rating']; $i++) echo '<i class="fas fa-star text-warning small"></i>'; ?>
                                    </td>
                                    <td><?php echo nl2br(htmlspecialchars($review['comment'])); ?></td>
                                    <td class="small text-muted"><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card admin-card border-0 shadow-sm">
            <div class="card-header table-header-coffee">
                <h5 class="mb-0">Genel Kafe Deneyimi Yorumları</h5>
            </div>
            <div class="card-body p-0">
                 <?php if (empty($cafeReviews)): ?>
                    <p class="p-4 text-center text-muted">Henüz genel kafe yorumu yapılmamış.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Müşteri</th>
                                    <th>Puan</th>
                                    <th>Yorum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cafeReviews as $review): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($review['name'] . ' ' . $review['surname']); ?></td>
                                    <td>
                                        <?php for($i=0; $i<$review['rating']; $i++) echo '<i class="fas fa-star text-warning small"></i>'; ?>
                                    </td>
                                    <td><?php echo nl2br(htmlspecialchars($review['comment'])); ?></td>
                                    <td class="small text-muted"><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer_template.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>