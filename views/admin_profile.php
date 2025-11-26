<?php
session_start();
require_once '../app/core/db.php'; // views/admin_profile.php'den app/core'a ulaşır

// YÖNLENDİRME VE YETKİ KONTROLÜ
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['user_role'] !== 'admin') {
    die('<div class="alert alert-danger">Bu sayfaya erişim yetkiniz yoktur. Lütfen yönetici olarak giriş yapın.</div>');
}

$msg = "";

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

// TÜM SİPARİŞLERİ ÇEKME
$sql = "SELECT o.*, u.name, u.surname, u.email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$orders = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// --- Sipariş Detaylarını Çekme (Admin Panelinde gösterilmek üzere) ---
function getOrderItems($pdo, $order_id) {
    $stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetici Paneli - Sipariş Yönetimi</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Yolu: views/admin_profile.php'den views/public/css/style_index.css'e gitmeli -->
    <link rel="stylesheet" href="../public/css/style_index.css"> 
    
    <style>
        /* YENİ COFFEE ADMIN TEMASI */
        body.admin-theme { 
            background-color: #fcf8f3; /* Açık Latte Arkaplan */
            padding-top: 100px;
        }
        .admin-header-nav { 
            background-color: #4B3621 !important; /* Espresso Kahve */
            color: white; 
            padding: 15px 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .admin-card { 
            border-radius: 15px; 
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .table-header-coffee { 
            background-color: #A67B5B !important; /* Latte Tonu */
            color: white !important; 
            font-weight: 600;
        }
        .btn-status-update {
            background-color: #6F4E37;
            border: none;
            color: white;
        }
        .btn-status-update:hover {
            background-color: #4B3621;
        }
        /* Durum Kartları */
        .stat-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            border-left: 5px solid #A67B5B;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body class="admin-theme">

    <!-- ADMIN HEADER (Basitleştirilmiş Navbar) -->
    <div class="admin-header-nav fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <h4 class="mb-0" style="color: #F5F1E9;"><i class="fas fa-tools me-2"></i> Yönetici Paneli</h4>
            <div class="text-white">
                <span class="me-3">Merhaba, <?php echo explode(" ", $_SESSION['user_name'])[0]; ?></span>
                <a href="logout.php" class="btn btn-sm btn-light text-danger rounded-pill"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
            </div>
        </div>
    </div>

    <div class="container">
        <h2 class="mb-5 mt-4" style="color: #6F4E37; font-family: 'Playfair Display', serif;">Anlık Sipariş Akışı</h2>
        <?php echo $msg; ?>

        <!-- İSTATİSTİK KARTLARI (BASIC DASHBOARD) -->
        <div class="row mb-5 g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <p class="text-muted mb-1">Toplam Bekleyen Sipariş</p>
                    <h3 class="fw-bold" style="color: #E9B200;"><i class="fas fa-hourglass-half me-2"></i>
                        <?php echo count(array_filter($orders, function($o) { return $o['status'] === 'hazırlanıyor'; })); ?>
                    </h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-left-color: #6F4E37;">
                    <p class="text-muted mb-1">Teslim Edilen (Son 24 Saat)</p>
                    <h3 class="fw-bold text-success"><i class="fas fa-check-circle me-2"></i>
                        <?php echo count(array_filter($orders, function($o) { return $o['status'] === 'teslim edildi' && strtotime($o['created_at']) > strtotime('-1 day'); })); ?>
                    </h3>
                </div>
            </div>
            <div class="col-md-4">
                 <div class="stat-card" style="border-left-color: #4B3621;">
                    <p class="text-muted mb-1">Toplam Sipariş</p>
                    <h3 class="fw-bold" style="color: #A67B5B;"><i class="fas fa-coffee me-2"></i>
                        <?php echo count($orders); ?>
                    </h3>
                </div>
            </div>
        </div>
        
        <!-- SİPARİŞ LİSTESİ -->
        <div class="card admin-card border-0">
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
                            <?php foreach ($orders as $order): 
                                $items = getOrderItems($pdo, $order['id']);
                                $itemsString = implode(', ', array_map(function($item) {
                                    return $item['quantity'] . 'x ' . $item['name'];
                                }, $items));
                            ?>
                            <tr data-bs-toggle="collapse" data-bs-target="#detail-<?php echo $order['id']; ?>" aria-expanded="false" aria-controls="detail-<?php echo $order['id']; ?>" style="cursor: pointer;">
                                <td class="fw-bold">#<?php echo $order['id']; ?></td>
                                <td><span class="badge bg-danger"><?php echo $order['table_no']; ?></span></td>
                                <td><?php echo $order['name'] ? htmlspecialchars($order['name']) . ' (' . htmlspecialchars($order['email']) . ')' : '<span class="text-muted">Ziyaretçi</span>'; ?></td>
                                <td class="text-muted small"><?php echo $itemsString; ?></td>
                                <td class="fw-bold"><?php echo number_format($order['total_price'], 2, ',', '.'); ?> ₺</td>
                                <td><?php echo date('H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <!-- Durum Gösterimi -->
                                    <?php 
                                        $statusClass = [
                                            'hazırlanıyor' => 'bg-warning text-dark',
                                            'hazır' => 'bg-info',
                                            'teslim edildi' => 'bg-success',
                                            'iptal' => 'bg-danger'
                                        ];
                                        echo '<span class="badge ' . $statusClass[$order['status']] . '">' . $order['status'] . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <!-- Durum Güncelleme Butonu -->
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

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>