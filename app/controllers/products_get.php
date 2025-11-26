<?php
require_once __DIR__ . '/../core/db.php';

// 1. ÜRÜN VERİSİNİ ÇEKME İŞLEMİ
$sql = "SELECT * FROM products WHERE is_active = 1"; 
// PDO nesnenizin adının $pdo olduğunu varsayıyorum (db.php'den gelen)
$stmt = $pdo->query($sql); 
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

function get_slug_from_id($category_id) {
    switch ($category_id) {
        case 1: return 'sicak'; // 1: Sıcak İçecekler
        case 2: return 'soguk'; // 2: Soğuk İçecekler
        case 3: return 'tatli'; // 3: Tatlılar
        default: return 'all'; 
    }
}

/**
 * Fonksiyon: Tek bir ürün için HTML kartını oluşturur ve render eder.
 * @param array $product Ürün bilgileri
 */
function renderProductCard($product) {
    $slug = get_slug_from_id($product['category_id']);
    // Fiyatı TR formatına dönüştürüyoruz
    $priceFormatted = number_format($product['price'], 2, ',', '.');
    // views'den public/img'ye giden yol: ../public/img/
    $imagePath = "../public/img/" . $product['image']; 
    
    // PHP'yi kısa tag'ler ile HTML çıktısı olarak döndürüyoruz (Bu, en temiz yazım şeklidir)
    echo '<div class="col-md-6 col-lg-3 product-item" data-category="' . $slug . '">';
        echo '<div class="product-card h-100">';
            echo '<div class="card-img-wrapper">';
                echo '<span class="price-tag">' . $priceFormatted . ' ₺</span>';
                echo '<img src="' . $imagePath . '" class="card-img-top" alt="' . $product['name'] . '">'; 
            echo '</div>';
            echo '<div class="card-body text-center">';
                echo '<h5 class="card-title fw-bold">' . $product['name'] . '</h5>';
                echo '<p class="card-text text-muted small">' . $product['description'] . '</p>';
                echo '<button class="btn btn-custom btn-sm w-100 add-to-cart" data-id="' . $product['id'] . '" data-name="' . $product['name'] . '" data-price="' . $product['price'] . '">';
                    echo '<i class="fas fa-plus me-1"></i> Sepete Ekle';
                echo '</button>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}
?>

<section id="menu" class="py-5">
    <div class="container">
        <h2 class="text-center display-4 fw-bold mb-5">Özel Menümüz</h2>
        
        <div class="text-center mb-5">
            <button class="btn btn-filter active filter-btn" data-filter="all">Tümü</button>
            <button class="btn btn-filter filter-btn" data-filter="sicak">Sıcak İçecekler</button>
            <button class="btn btn-filter filter-btn" data-filter="soguk">Soğuk İçecekler</button>
            <button class="btn btn-filter filter-btn" data-filter="tatli">Tatlılar</button>
        </div>

        <div class="row g-4 product-list">
            <?php 
            // 3. ÇEKİLEN ÜRÜNLERİ DÖNGÜ İLE LİSTELEME
            if (!empty($products)) {
                foreach ($products as $product) {
                    renderProductCard($product);
                }
            } else {
                // Ürün yoksa bilgilendirme mesajı
                echo '<div class="col-12 text-center">';
                echo '<p class="lead text-muted">Şu an gösterilecek ürün bulunmamaktadır.</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>