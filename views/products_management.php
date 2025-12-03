<?php
session_start();
require_once '../app/core/db.php'; // views/products_management.php'den app/core'a ulaşır

// YETKİ KONTROLÜ
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$msg = "";
// Düzeltme: Tablonuzdaki sütun adı 'image' olduğu için default değerini buna göre ayarladık.
$default_image_file = "default.jpg"; 

// -----------------------------------------------------------
// A. ÜRÜN GÜNCELLEME İŞLEMİ (Mevcut)
// -----------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $new_price = floatval($_POST['price']);
    $is_active = isset($_POST['is_active']) ? 1 : 0; 
    
    if ($new_price <= 0) {
        $msg = '<div class="alert alert-danger alert-dismissible fade show">Fiyat sıfırdan büyük olmalıdır.</div>';
    } else {
        $stmt = $pdo->prepare("UPDATE products SET price = ?, is_active = ? WHERE id = ?");
        
        if ($stmt->execute([$new_price, $is_active, $product_id])) {
            $msg = '<div class="alert alert-success alert-dismissible fade show">Ürün #' . $product_id . ' başarıyla güncellendi.</div>';
        } else {
            $msg = '<div class="alert alert-danger">Güncelleme sırasında hata oluştu.</div>';
        }
    }
}

// -----------------------------------------------------------
// B. YENİ EK: YENİ ÜRÜN EKLEME İŞLEMİ (Düzeltildi)
// -----------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_new_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['new_price']);
    $category_id = intval($_POST['category_id']);
    
    if (empty($name) || $price <= 0 || $category_id <= 0) {
        $msg = '<div class="alert alert-danger alert-dismissible fade show">Lütfen ürün adı, geçerli fiyat ve kategori seçimi yapın.</div>';
    } else {
        // Düzeltme: description boşsa NULL yerine boş string gönderiyoruz
        // description_to_insert, veritabanı NULL kabul etmiyorsa boş string atanmalı.
        $description_to_insert = !empty($description) ? $description : '';
        
        // Düzeltme: image sütunu kullanıldı
        // is_active varsayılan olarak 1 (Aktif) ayarlandı.
        $sql = "INSERT INTO products (category_id, name, description, price, image, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$category_id, $name, $description_to_insert, $price, $default_image_file, 1])) {
            $msg = '<div class="alert alert-success alert-dismissible fade show">Yeni ürün "' . htmlspecialchars($name) . '" başarıyla eklendi.</div>';
        } else {
             // Hata tespiti için geçici olarak kullanılabilir: 
             // $error_info = $stmt->errorInfo();
             // $msg = '<div class="alert alert-danger">Ürün eklenirken bir hata oluştu: ' . $error_info[2] . '</div>';
             $msg = '<div class="alert alert-danger">Ürün eklenirken bir hata oluştu.</div>';
        }
    }
}


// -----------------------------------------------------------
// C. VERİ ÇEKME VE GRUPLAMA
// -----------------------------------------------------------
// 1. KATEGORİLERİ ÇEKME (Yeni ürün formu için gerekli)
$categories = $pdo->query("SELECT * FROM categories ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// 2. ÜRÜNLERİ KATEGORİLERİYLE BİRLİKTE ÇEKME (Düzeltme: image sütunu çekildi)
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
    <title>Ürün & Fiyat Yönetimi - OrderFlow Admin</title>
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

        <div class="d-flex justify-content-between align-items-center mb-5 mt-4">
            <h2 style="color: #6F4E37; font-family: 'Playfair Display', serif;"><i class="fas fa-mug-hot me-2"></i>Ürün & Fiyat Yönetimi</h2>
            <button class="btn btn-primary" style="background-color: #D4A017; border-color: #D4A017;" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fas fa-plus me-2"></i> Yeni Ürün Ekle
            </button>
        </div>
        
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
    
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #6F4E37; color: white;">
            <h5 class="modal-title" id="addProductModalLabel"><i class="fas fa-mug-hot me-2"></i> Yeni Ürün Oluştur</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST">
              <div class="modal-body">
                
                <div class="mb-3">
                  <label for="productName" class="form-label">Ürün Adı</label>
                  <input type="text" class="form-control" id="productName" name="name" required>
                </div>
                
                <div class="mb-3">
                  <label for="productCategory" class="form-label">Kategori</label>
                  <select class="form-select" id="productCategory" name="category_id" required>
                    <option value="">Kategori Seçin</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="productPrice" class="form-label">Fiyat (₺)</label>
                  <input type="number" step="0.01" min="0.01" class="form-control" id="productPrice" name="new_price" required>
                </div>

                <div class="mb-3">
                  <label for="productDescription" class="form-label">Açıklama</label>
                  <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                  <small class="text-muted">Görsel otomatik olarak **<?php echo $default_image_file; ?>** atanacaktır.</small>
                </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="submit" name="add_new_product" class="btn btn-primary" style="background-color: #6F4E37; border-color: #6F4E37;">
                    <i class="fas fa-plus-circle me-1"></i> Ürünü Ekle
                </button>
              </div>
          </form>
        </div>
      </div>
    </div>
    <?php include 'footer_template.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>