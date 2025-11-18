<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrderFlow</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg fixed-top navbar-dark">
        <div class="container">
            <a class="navbar-brand fs-3" href="#"><i class="fas fa-coffee me-2"></i>Lusso Coffee</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Anasayfa</a></li>
                    <li class="nav-item"><a class="nav-link" href="#menu">Menü</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Hakkımızda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">İletişim</a></li>
                    <li class="nav-item ms-3">
                        <a href="#" class="btn btn-outline-light rounded-pill px-4">Giriş Yap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3 animate__animated animate__fadeInDown">Kahvenin Sanata Dönüştüğü Yer</h1>
            <p class="lead mb-4">Özel kavrulmuş çekirdekler, usta eller ve eşsiz bir atmosfer.</p>
            <a href="#menu" class="btn btn-custom btn-lg shadow-lg">Siparişe Başla</a>
        </div>
    </header>

    <section id="menu" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h5 class="text-uppercase text-muted ls-2">Keşfet</h5>
                <h2 class="display-5 fw-bold" style="color: var(--primary-color);">Özel Menümüz</h2>
                <div style="width: 60px; height: 3px; background: var(--secondary-color); margin: 20px auto;"></div>
            </div>

            <div class="text-center mb-5">
                <button class="btn btn-outline-dark rounded-pill px-4 me-2 active filter-btn" data-filter="all">Tümü</button>
                <button class="btn btn-outline-dark rounded-pill px-4 me-2 filter-btn" data-filter="sicak">Sıcaklar</button>
                <button class="btn btn-outline-dark rounded-pill px-4 me-2 filter-btn" data-filter="soguk">Soğuklar</button>
                <button class="btn btn-outline-dark rounded-pill px-4 filter-btn" data-filter="tatli">Tatlılar</button>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3 product-item" data-category="sicak">
                    <div class="product-card h-100">
                        <div class="card-img-wrapper">
                            <span class="price-tag">65 ₺</span>
                            <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="Latte">
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold">Caramel Latte</h5>
                            <p class="card-text text-muted small">Espresso, buharlanmış süt ve karamel sosu.</p>
                            <button class="btn btn-custom btn-sm w-100 add-to-cart" data-name="Caramel Latte">
                                <i class="fas fa-plus me-1"></i> Sepete Ekle
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 product-item" data-category="tatli">
                    <div class="product-card h-100">
                        <div class="card-img-wrapper">
                            <span class="price-tag">120 ₺</span>
                            <img src="https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="Cheesecake">
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold">San Sebastian</h5>
                            <p class="card-text text-muted small">Belçika çikolatalı sos eşliğinde.</p>
                            <button class="btn btn-custom btn-sm w-100 add-to-cart" data-name="San Sebastian">
                                <i class="fas fa-plus me-1"></i> Sepete Ekle
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 product-item" data-category="soguk">
                    <div class="product-card h-100">
                        <div class="card-img-wrapper">
                            <span class="price-tag">75 ₺</span>
                            <img src="https://cdn.shopify.com/s/files/1/0569/3987/2340/files/evde-cold-brew_480x480.webp?v=1718216553" class="card-img-top" alt="Cold Brew">
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold">Cold Brew</h5>
                            <p class="card-text text-muted small">12 saat demlenmiş yoğun kahve tadı.</p>
                            <button class="btn btn-custom btn-sm w-100 add-to-cart" data-name="Cold Brew">
                                <i class="fas fa-plus me-1"></i> Sepete Ekle
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 product-item" data-category="sicak">
                    <div class="product-card h-100">
                        <div class="card-img-wrapper">
                            <span class="price-tag">45 ₺</span>
                            <img src="https://images.unsplash.com/photo-1559496417-e7f25cb247f3?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" class="card-img-top" alt="Türk Kahvesi">
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold">Türk Kahvesi</h5>
                            <p class="card-text text-muted small">Geleneksel lezzet, çifte kavrulmuş.</p>
                            <button class="btn btn-custom btn-sm w-100 add-to-cart" data-name="Türk Kahvesi">
                                <i class="fas fa-plus me-1"></i> Sepete Ekle
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="mb-3" style="font-family: 'Playfair Display', serif;">Lusso Coffee</h4>
                    <p class="text-muted">Kahve tutkusunu sanata dönüştürdüğümüz mekanımıza hoş geldiniz. En iyi
                        çekirdekler, en iyi anlar için.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Çalışma Saatleri</h5>
                    <ul class="list-unstyled text-muted">
                        <li>Hafta İçi: 08:00 - 23:00</li>
                        <li>Hafta Sonu: 09:00 - 00:00</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">İletişim</h5>
                    <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> Bağdat Caddesi No:12, İstanbul</p>
                    <p class="text-muted"><i class="fas fa-phone me-2"></i> +90 212 555 00 00</p>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center text-muted">
                <small>&copy; 2025 OrderFlow. Laravel Project Design.</small>
            </div>
        </div>
    </footer>

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
    
    <script src="script.js"></script>

</body>
</html>