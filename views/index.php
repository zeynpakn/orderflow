<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrderFlow - Sipariş Sistemi</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="../public/css/style_index.css">

</head>

<body>

    <?php 
    // Header dosyasını buraya dahil ediyoruz
    include 'header_template.php';  // views/'den root'a: ../ ekle
    ?>

    <header class="hero">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3 animate__animated animate__fadeInDown">Kahvenin Sanata Dönüştüğü Yer</h1>
            <p class="lead mb-4">Özel kavrulmuş çekirdekler, usta eller ve eşsiz bir atmosfer.</p>
            <a href="#menu" class="btn btn-custom btn-lg shadow-lg">Siparişe Başla</a>
        </div>
    </header>

    <?php include '../app/controllers/products_get.php'  ?>

    <!-- Modal'ı buradan include et (views/'den modals/'e: doğru path) -->
    <?php include 'modals/cart-modal.php'; ?>

    <?php include 'footer_template.php';  // views/'den root'a: ../ ekle ?>

    <div class="fab-cart">
        <i class="fas fa-shopping-basket"></i>
        <span class="cart-badge" id="cart-count">0</span>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto"><i class="fas fa-check-circle"></i> Başarılı</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Ürün sepete eklendi!
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS'i ayrı dosyaya taşıdık -->
    <script src="../public/js/script.js"></script>

</body>

</html>