<?php
// Bu dosya, sabit Footer'ı ve tüm sayfanın kapanış etiketlerini içerir.
?>
    <footer class="bg-dark py-5">
        <div class="container text-white"> 
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="mb-3" style="font-family: 'Playfair Display', serif;">Lusso Coffee</h4>
                    <p>Kahve tutkusunu sanata dönüştürdüğümüz mekanımıza hoş geldiniz. En iyi
                        çekirdekler, en iyi anlar için.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Çalışma Saatleri</h5>
                    <ul class="list-unstyled">
                        <li>Hafta İçi: 08:00 - 23:00</li>
                        <li>Hafta Sonu: 09:00 - 00:00</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">İletişim</h5>
                    <p><i class="fas fa-map-marker-alt me-2"></i> Bağdat Caddesi No:12, İstanbul</p>
                    <p><i class="fas fa-phone me-2"></i> +90 212 555 00 00</p>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center"> 
                <small>&copy; 2025 Lusso Coffee House. Laravel Project Design.</small>
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