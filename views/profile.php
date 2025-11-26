<?php
session_start();
require_once '../app/core/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Siparişleri çek
$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orderStmt->execute([$user_id]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

// Mesaj kontrolü
$msg = "";
if(isset($_GET['success'])) $msg = '<div class="alert alert-success">Sipariş değerlendirmeniz alındı. Teşekkürler!</div>';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim - OrderFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Linkleri -->
    <link rel="stylesheet" href="../public/css/style_index.css">
   
</head>
<body class="profile-page d-flex flex-column min-vh-100">
    
    <?php include 'header_template.php'; ?>

    <div class="container flex-grow-1">
        <?php echo $msg; ?>
        
        <!-- PROFİL KARTI -->
        <div class="profile-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 30px;">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                </div>
                <div class="flex-grow-1 ms-4">
                    <h3 class="mb-1" style="color: #4B3621;"><?php echo $user['name'] . ' ' . $user['surname']; ?></h3>
                    <p class="text-muted mb-0"><i class="fas fa-envelope me-2"></i><?php echo $user['email']; ?></p>
                    <p class="text-muted mb-0"><small>Üyelik Tarihi: <?php echo date('d.m.Y', strtotime($user['created_at'])); ?></small></p>
                </div>
            </div>
        </div>
        
        <!-- SİPARİŞ GEÇMİŞİ -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0" style="color: #6F4E37;"><i class="fas fa-history me-2"></i>Geçmiş Siparişlerim</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($orders)): ?>
                    <div class="p-5 text-center text-muted">
                        <p>Henüz hiç sipariş vermediniz.</p>
                        <a href="index.php" class="btn btn-primary btn-sm" style="background-color: #6F4E37; border: none;">Menüye Git</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Tarih</th>
                                    <th>Masa</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $order['id']; ?></td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo $order['table_no']; ?></td>
                                    <td class="text-success fw-bold"><?php echo $order['total_price']; ?> ₺</td>
                                    <td>
                                        <?php if($order['status'] == 'hazırlanıyor'): ?>
                                            <span class="badge bg-warning text-dark">Hazırlanıyor</span>
                                        <?php elseif($order['status'] == 'hazır'): ?>
                                            <span class="badge bg-info">Hazır</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Teslim Edildi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($order['status'] == 'teslim edildi'): ?>
                                            <button type="button" class="btn btn-sm" style="background-color: #D4A017; color: white;" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $order['id']; ?>">
                                                <i class="fas fa-star"></i> Puanla
                                            </button>

                                            <!-- MODAL: SİPARİŞ DEĞERLENDİRME -->
                                            <div class="modal fade" id="reviewModal<?php echo $order['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Sipariş #<?php echo $order['id']; ?> Değerlendir</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="submit_order_review.php" method="POST">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                                <div class="mb-3 text-center">
                                                                    <label class="form-label d-block">Puanınız</label>
                                                                    <select name="rating" class="form-select w-50 mx-auto">
                                                                        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                                                        <option value="4">⭐⭐⭐⭐ (4)</option>
                                                                        <option value="3">⭐⭐⭐ (3)</option>
                                                                        <option value="2">⭐⭐ (2)</option>
                                                                        <option value="1">⭐ (1)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Yorumunuz (Sadece Admin görür)</label>
                                                                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                                                <button type="submit" class="btn btn-primary" style="background-color: #6F4E37; border: none;">Gönder</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer'ı çağırıyoruz. mt-auto flex yapısı için gerekli, ancak boşluk yaratan dış div'i kaldırdık. -->
    <?php include 'footer_template.php'; ?>

    <!-- JS Kütüphaneleri BURADA YÜKLENMELİ -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>