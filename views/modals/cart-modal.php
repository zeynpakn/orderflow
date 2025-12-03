<?php
// Modal içinde session bilgisi yoksa burada yükleyelim
if (!isset($_SESSION)) session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<!-- Sepet Pop-up Modal'ı -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Alışveriş Sepeti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat" onclick="closeModal()"></button>
            </div>

            <div class="modal-body">
                <!-- Dinamik liste buraya gelecek (JS ile doldurulacak) -->
                <div id="cartItemsList">
                    <!-- Boş sepet görüntüsü JS tarafından yönetilir -->
                </div>
            </div>

            <div class="modal-footer">

                <div class="w-100 mb-3">
                    <div class="row">

                        <div class="col-md-6">
                            <label for="tableNoInput" class="form-label fw-bold">Masa No (1-20)</label>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-chair text-primary"></i>
                                </span>

                                <input type="number"
                                       class="form-control form-control-lg border-start-0 shadow-sm"
                                       id="tableNoInput"
                                       min="1"
                                       max="20"
                                       step="1"
                                       value="1"
                                       required
                                       style="border-left: none; font-weight: bold; text-align: center;">

                                <span class="input-group-text bg-light border-start-0">
                                    <i class="fas fa-hashtag text-primary"></i>
                                </span>
                            </div>

                            <div class="form-text small mt-1">
                                Ok tuşları ile artır/azaltın veya tıklayarak değiştirin.
                            </div>
                        </div>

                        <div class="col-md-6 d-flex align-items-end justify-content-end">
                            <span id="cartTotal"
                                  class="fw-bold fs-4 text-primary"
                                  style="font-size: 1.5rem; border-bottom: 2px solid #0d6efd; padding-bottom: 5px;">
                                  Toplam: 0.00 ₺
                            </span>
                        </div>

                    </div>
                </div>

                <!-- ALT BUTONLAR -->
                <button type="button" class="btn btn-secondary me-2" onclick="closeModal()">Kapat</button>

                <?php if (!$is_logged_in): ?>
                    <button type="button"
                            class="btn btn-outline-primary me-2"
                            onclick="loginRedirect()">
                        Giriş Yap
                    </button>
                <?php endif; ?>

                <button type="button"
                        class="btn btn-primary"
                        id="checkoutBtn"
                        disabled
                        onclick="checkoutOrder()">
                    Sipariş Oluştur
                </button>

            </div>

        </div>
    </div>
</div>
