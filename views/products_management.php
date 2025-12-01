<?php
session_start();
require_once '../app/core/db.php'; // views/products_management.php'den app/core'a ulaşır

// YETKİ KONTROLÜ
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$msg = "";

// -----------------------------------------------------------
// A. ÜRÜN GÜNCELLEME İŞLEMİ
// -----------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $new_price = floatval($_POST['price']);
    // is_active checkbox'ından gelen değer. İşaretliyse '1', değilse '0' olarak ayarlanır.
    $is_active = isset($_POST['is_active']) ? 1 : 0; 
    
    // Fiyatın pozitif olduğundan emin olalım
    if ($new_price <= 0) {
        $msg = '<div class="alert alert-danger alert-dismissible fade show">Fiyat sıfırdan büyük olmalıdır.</div>';
    } else {
        $stmt = $pdo->prepare("UPDATE products SET price = ?, is_active = ? WHERE id = ?");
        
        if ($stmt->execute([$new_price, $is_active, $product_id])) {
            $msg = '<div class="alert alert-success alert-dismissible fade show">Ürün (ID: ' . $product_id . ') başarıyla güncellendi.</div>';
        } else {
            $msg = '<div class="alert alert-danger">Güncelleme sırasında hata oluştu.</div>';
        }
    }
}

// -----------------------------------------------------------
// B. ÜRÜNLERİ KATEGORİLERİYLE BİRLİKTE ÇEKME
// -----------------------------------------------------------
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id
        ORDER BY c.name, p.name";
$products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Ürünleri kategoriye göre grupla
$grouped_products = [];
foreach ($products as $product) {
    $grouped_products[$product['category_name']][] = $product;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ürün Yönetimi - OrderFlow Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../public/css/style_index.css"> 
</head>
<body class="profile-page d-flex flex-column min-vh-100">

    <?php include 'modals/header_template.php'; ?>

    <div class="container flex-grow-1 my-5" style="padding-top: 50px;">
        
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-list-alt me-2"></i> Sipariş Akışı
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active">
                    <i class="fas fa-mug-hot me-2"></i> Ürün & Fiyat Yönetimi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reviews_management.php"> 
                    <i class="fas fa-comments me-2"></i> Müşteri Geri Bildirimleri
                </a>
            </li>
        </ul>
        <h2 class="mb-5" style="color: #6F4E37; font-family: 'Playfair Display', serif;">Ürün ve Fiyat Yönetimi</h2>
        <?php echo $msg; ?>

        <?php if (empty($grouped_products)): ?>
            <div class="alert alert-info">Henüz sisteme kayıtlı ürün bulunmamaktadır.</div>
        <?php endif; ?>

        <div class="row g-4">
            <?php foreach ($grouped_products as $category_name => $products): ?>
                <div class="col-12">
                    <div class="card admin-card border-0 shadow-sm">
                        <div class="card-header table-header-coffee">
                            <h5 class="mb-0"><?php echo htmlspecialchars($category_name); ?> (<?php echo count($products); ?> Ürün)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Ürün Adı</th>
                                            <th style="width: 150px;">Fiyat (₺)</th>
                                            <th style="width: 120px;">Aktif/Pasif</th>
                                            <th style="width: 100px;">Kaydet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <form method="POST">
                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                <tr>
                                                    <td class="fw-bold">#<?php echo $product['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                    <td>
                                                        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" class="form-control form-control-sm" required style="width: 100px;">
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="activeSwitch<?php echo $product['id']; ?>" value="1" <?php if ($product['is_active'] == 1) echo 'checked'; ?>>
                                                            <label class="form-check-label" for="activeSwitch<?php echo $product['id']; ?>"><?php echo $product['is_active'] == 1 ? 'Aktif' : 'Pasif'; ?></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="submit" name="update_product" class="btn btn-sm btn-status-update">
                                                            <i class="fas fa-save"></i> Kaydet
                                                        </button>
                                                    </td>
                                                </tr>
                                            </form>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <?php include 'modals/footer_template.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>