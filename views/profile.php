<?php
session_start();
// views/profile.php'den app/core'a ulaşır
if (file_exists('../app/core/db.php')) {
    require_once '../app/core/db.php';
} else {
    die("HATA: Veritabanı dosyası bulunamadı.");
}

// YÖNLENDİRME KONTROLÜ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
// Rol kontrolü
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$msg = "";


// --- ADMIN VE USER ORTAK VERİ ÇEKME: GİRİŞ YAPAN KULLANICININ BİLGİSİ ---
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// --- SİPARİŞ DETAYLARINI ÇEKME FONKSİYONU ---
function getOrderItems($pdo, $order_id) {
    $stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- ADMIN İŞLEMLERİ VE VERİ ÇEKME ---
if ($isAdmin) {
    // SİPARİŞ DURUMU GÜNCELLEME İŞLEMİ
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
        $order_id = $_POST['order_id'];
        $new_status = $_POST['new_status'];
        
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $order_id])) {
            $msg = '<div class="alert alert-success alert-dismissible fade show">Sipariş #' . $order_id . ' durumu güncellendi.</div>';
        } else {
            $msg = '<div class="alert alert-danger">Güncelleme sırasında hata oluştu.</div>';
        }
    }

    // YENİ EK: FİLTRELEME MANTIĞI
    $filter_type = $_GET['filter'] ?? 'all'; // URL'den filter parametresini al, yoksa 'all' varsay
    $today = date('Y-m-d');
    
    $sql_where = "";
    
    if ($filter_type === 'waiting') {
        // Bekleyen Siparişler: Hazırlanıyor veya Hazır durumunda olanlar (bugün)
        $sql_where = "WHERE (o.status = 'hazırlanıyor' OR o.status = 'hazır') AND DATE(o.created_at) = '$today'";
    } elseif ($filter_type === 'delivered_today') {
        // Bugün Teslim Edilenler
        $sql_where = "WHERE o.status = 'teslim edildi' AND DATE(o.created_at) = '$today'";
    } elseif ($filter_type === 'total_today') {
        // Bugün Verilen Tüm Siparişler
        $sql_where = "WHERE DATE(o.created_at) = '$today'";
    }
    // 'all' durumunda filtre yok (tüm siparişler)

    // TÜM SİPARİŞLERİ ÇEKME (Filtre uygulanmış hali)
    $sql = "SELECT o.*, u.name, u.surname, u.email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            " . $sql_where . "
            ORDER BY o.created_at DESC";
    $orders_admin = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // İSTATİSTİK KARTLARI İÇİN VERİLERİ ÇEK (Filtre uygulanmadan, bugünün istatistiklerini göstermek için)
    // Bu sorgu filtreden bağımsız çalışmalı, aksi takdirde kartlardaki sayılar yanlış olur.
    $sql_stats = "SELECT id, status, created_at FROM orders WHERE DATE(created_at) = '$today'";
    $orders_today_stats = $pdo->query($sql_stats)->fetchAll(PDO::FETCH_ASSOC);

    $waiting_orders_count = count(array_filter($orders_today_stats, function($o) { return $o['status'] === 'hazırlanıyor' || $o['status'] === 'hazır'; }));
    $delivered_today_count = count(array_filter($orders_today_stats, function($o) { return $o['status'] === 'teslim edildi'; }));
    $total_today_count = count($orders_today_stats);
    $total_all_count = $pdo->query("SELECT COUNT(id) FROM orders")->fetchColumn();


} else {
    // NORMAL KULLANICI SİPARİŞLERİNİ ÇEK
    $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $orderStmt->execute([$user_id]);
    $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mesaj kontrolü
    if(isset($_GET['success'])) $msg = '<div class="alert alert-success">Sipariş değerlendirmeniz alındı. Teşekkürler!</div>';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isAdmin ? 'Yönetici Paneli' : 'Profilim'; ?> - OrderFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/style_index.css">
    
    <style>
        .table-title {
            color: #4B3621;
            border-bottom: 2px solid #6F4E37;
            padding-bottom: 10px;
            margin-top: 20px;
            font-size: 1.5rem;
        }
    </style>
</head>
<body class="profile-page d-flex flex-column min-vh-100">
    
    <?php include 'header_template.php'; ?>

    <div class="container flex-grow-1 my-5">
        
        <?php echo $msg; ?>
        
        <?php if ($isAdmin): ?>
            
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active">
                        <i class="fas fa-list-alt me-2"></i> Sipariş Akışı
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products_management.php"> 
                        <i class="fas fa-mug-hot me-2"></i> Ürün & Fiyat Yönetimi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reviews_management.php"> 
                        <i class="fas fa-comments me-2"></i> Müşteri Geri Bildirimleri
                    </a>
                </li>
            </ul>
            <h2 class="mb-5 mt-4" style="color: #6F4E37; font-family: 'Playfair Display', serif;"><i class="fas fa-tools me-2"></i>Yönetici Paneli - Sipariş Akışı</h2>
            
            <div class="row mb-5 g-4">
                <div class="col-md-4">
                    <a href="profile.php?filter=waiting" class="text-decoration-none">
                        <div class="stat-card stat-card-clickable">
                            <p class="text-muted mb-1">Toplam Bekleyen Sipariş (Bugün)</p>
                            <h3 class="fw-bold" style="color: #E9B200;"><i class="fas fa-hourglass-half me-2"></i>
                                <?php echo $waiting_orders_count; ?>
                            </h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="profile.php?filter=delivered_today" class="text-decoration-none">
                        <div class="stat-card stat-card-primary stat-card-clickable"> 
                            <p class="text-muted mb-1">Teslim Edilen (Bugün)</p>
                            <h3 class="fw-bold text-success"><i class="fas fa-check-circle me-2"></i>
                                <?php echo $delivered_today_count; ?>
                            </h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                     <a href="profile.php?filter=total_today" class="text-decoration-none">
                        <div class="stat-card stat-card-dark stat-card-clickable">
                            <p class="text-muted mb-1">Toplam Sipariş (Bugün)</p>
                            <h3 class="fw-bold" style="color: #A67B5B;"><i class="fas fa-coffee me-2"></i>
                                <?php echo $total_today_count; ?>
                            </h3>
                        </div>
                    </a>
                </div>
            </div>
            
            <?php 
                $table_title = "Tüm Siparişler";
                if ($filter_type === 'waiting') $table_title = "Bugünün Bekleyen Siparişleri";
                elseif ($filter_type === 'delivered_today') $table_title = "Bugün Teslim Edilen Siparişler";
                elseif ($filter_type === 'total_today') $table_title = "Bugün Verilen Tüm Siparişler";
            ?>
            <h4 class="table-title d-flex justify-content-between align-items-center">
                <?php echo $table_title; ?>
                <?php if($filter_type != 'all'): ?>
                    <a href="profile.php" class="btn btn-sm btn-outline-secondary">Tümünü Göster</a>
                <?php endif; ?>
            </h4>

            <div class="card admin-card border-0 shadow-sm mt-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="table-header-coffee">
                                <tr>
                                    <th>#ID</th>
                                    <th>Masa No</th>
                                    <th>Müşteri (Adı/Email)</th>
                                    <th>Detay</th>
                                    <th>Toplam</th>
                                    <th>Zaman</th>
                                    <th>Durum</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders_admin)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted p-4">Bu kritere uygun sipariş bulunmamaktadır.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($orders_admin as $order): 
                                    $items = getOrderItems($pdo, $order['id']); 
                                    $itemsString = implode(', ', array_map(function($item) {
                                        return $item['quantity'] . 'x ' . $item['name'];
                                    }, $items));
                                ?>
                                <tr>
                                    <td class="fw-bold">#<?php echo $order['id']; ?></td>
                                    <td><span class="badge bg-danger"><?php echo $order['table_no']; ?></span></td>
                                    <td><?php echo $order['name'] ? htmlspecialchars($order['name']) . ' (' . htmlspecialchars($order['email']) . ')' : '<span class="text-muted">Ziyaretçi</span>'; ?></td>
                                    <td class="text-muted small"><?php echo $itemsString; ?></td>
                                    <td class="fw-bold"><?php echo number_format($order['total_price'], 2, ',', '.'); ?> ₺</td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <?php 
                                            $statusClass = [
                                                'hazırlanıyor' => 'bg-warning text-dark',
                                                'hazır' => 'bg-info',
                                                'teslim edildi' => 'bg-success',
                                                'iptal' => 'bg-danger'
                                            ];
                                            echo '<span class="badge ' . ($statusClass[$order['status']] ?? 'bg-secondary') . '">' . $order['status'] . '</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="new_status" class="form-select form-select-sm me-2" style="width: 120px;">
                                                <option value="hazırlanıyor" <?php if ($order['status'] == 'hazırlanıyor') echo 'selected'; ?>>Hazırlanıyor</option>
                                                <option value="hazır" <?php if ($order['status'] == 'hazır') echo 'selected'; ?>>Hazır</option>
                                                <option value="teslim edildi" <?php if ($order['status'] == 'teslim edildi') echo 'selected'; ?>>Teslim Edildi</option>
                                                <option value="iptal" <?php if ($order['status'] == 'iptal') echo 'selected'; ?>>İptal</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-sm btn-status-update">
                                                Kaydet
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <h2 class="mb-4" style="color: #6F4E37; font-family: 'Playfair Display', serif;"><i class="fas fa-id-card me-2"></i>Profil Bilgileri</h2>

            <div class="profile-header d-flex align-items-center justify-content-between mb-5 p-4 bg-white shadow-sm rounded">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 30px; background-color: #A67B5B !important;">
                            <?php echo strtoupper(mb_substr($user['name'], 0, 1)); ?>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h3 class="mb-1" style="color: #4B3621;"><?php echo $user['name'] . ' ' . $user['surname']; ?></h3>
                        <p class="text-muted mb-0"><i class="fas fa-envelope me-2"></i><?php echo $user['email']; ?></p>
                        <p class="text-muted mb-0"><small>Üyelik Tarihi: <?php echo date('d.m.Y', strtotime($user['created_at'])); ?></small></p>
                    </div>
                </div>
            </div>
            
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
                                        <td class="text-success fw-bold"><?php echo number_format($order['total_price'], 2, ',', '.'); ?> ₺</td>
                                        <td>
                                            <?php 
                                            $statusBadge = [
                                                'hazırlanıyor' => 'bg-warning text-dark',
                                                'hazır' => 'bg-info',
                                                'teslim edildi' => 'bg-success',
                                                'iptal' => 'bg-danger'
                                            ];
                                            echo '<span class="badge ' . ($statusBadge[$order['status']] ?? 'bg-secondary') . '">' . $order['status'] . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if($order['status'] == 'teslim edildi'): ?>
                                                <button type="button" class="btn btn-sm" style="background-color: #D4A017; color: white;" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $order['id']; ?>">
                                                    <i class="fas fa-star"></i> Puanla
                                                </button>

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
        <?php endif; ?>
    </div>

    <?php include 'footer_template.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>