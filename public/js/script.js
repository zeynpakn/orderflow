// -----------------------------------------------------
// GLOBAL SEPET DEĞİŞKENLERİ
// -----------------------------------------------------
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
let checkoutRunning = false;

// -----------------------------------------------------
// SAYFA YÜKLENDİĞİNDE
// -----------------------------------------------------
$(document).ready(function () {

    updateCartCount();

    // Navbar scroll efekti
    $(window).scroll(function () {
        $('.navbar').toggleClass('scrolled', $(this).scrollTop() > 50);
    });

    // Ürün filtreleme
    $('.filter-btn').click(function () {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        let val = $(this).data('filter');
        if (val === "all") {
            $('.product-item').fadeIn(400);
        } else {
            $('.product-item').hide();
            $(`.product-item[data-category="${val}"]`).fadeIn(400);
        }
    });

    // Ürün sepete ekle
    $(document).on('click', '.add-to-cart', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let price = parseFloat($(this).data('price'));

        let exist = cart.find(i => i.id == id);

        if (exist) exist.quantity++;
        else cart.push({ id, name, price, quantity: 1 });

        localStorage.setItem('cart', JSON.stringify(cart));

        cartCount = cart.reduce((s, i) => s + i.quantity, 0);
        updateCartCount();

        let toast = new bootstrap.Toast(document.getElementById('cartToast'));
        $('.toast-body').text(name + " sepete eklendi!");
        toast.show();
    });

    // Sepet aç
    $(document).on('click', '.fab-cart', function () {
        renderCartModal();
        new bootstrap.Modal(document.getElementById('cartModal')).show();
    });

    // Tek ürün sil
    $(document).on('click', '.remove-item', function () {
        let id = $(this).data('id');
        let idx = cart.findIndex(i => i.id == id);

        if (idx !== -1) {
            cart[idx].quantity--;

            if (cart[idx].quantity <= 0) cart.splice(idx, 1);

            localStorage.setItem('cart', JSON.stringify(cart));

            cartCount = cart.reduce((sum, i) => sum + i.quantity, 0);
            updateCartCount();
            renderCartModal();
        }
    });

    // Login butonu yönlendirme
    $(document).on('click', '#loginBtn', function () {
        window.location.href = "login.php";
    });

    // Kapat butonu
    $(document).on('click', '#closeCartModal', function () {
        closeModal();
    });

    // Sipariş oluşturma — sadece 1 kez çalıştır
    $(document).on('click', '#checkoutBtn', function () {
        if (!$(this).prop('disabled')) checkoutOrder();
    });

});

// -----------------------------------------------------
// Modal kapatma fonksiyonu
// -----------------------------------------------------
function closeModal() {
    let modal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
    modal.hide();
}

// -----------------------------------------------------
// Login yönlendirme fonksiyonu
// -----------------------------------------------------
function loginRedirect() {
    window.location.href = "login.php";
}

// -----------------------------------------------------
// Sepet sayısı güncelle
// -----------------------------------------------------
function updateCartCount() {
    $('#cart-count').text(cartCount);
}

// -----------------------------------------------------
// SEPET MODALINI RENDER ET (TEK DOĞRU SÜRÜM)
// -----------------------------------------------------
function renderCartModal() {
    let html = '';
    let total = 0;

    if (cart.length === 0) {
        html = `
            <div class="text-center py-5">
                <i class="fas fa-shopping-basket fa-3x text-muted mb-3"></i>
                <p class="text-muted">Sepetiniz şu an boş.</p>
            </div>
        `;

        $('#cartItemsList').html(html);
        $('#cartTotal').text(`Toplam: 0.00 ₺`);
        $('#checkoutBtn').prop('disabled', true);
        return;
    }

    cart.forEach(item => {
        let itemTotal = item.price * item.quantity;
        total += itemTotal;

        html += `
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">

                <div class="flex-grow-1">
                    <h6>${item.name}</h6>
                    <small class="text-muted">${item.price.toFixed(2)} ₺ × ${item.quantity}</small>
                </div>

                <div class="text-end">
                    <strong>${itemTotal.toFixed(2)} ₺</strong>
                    <br>
                    <button class="btn btn-sm btn-outline-danger mt-1 remove-item" data-id="${item.id}">
                        1 Adet Ürün Sil
                    </button>
                </div>

            </div>
        `;
    });

    $('#cartItemsList').html(html);
    $('#cartTotal').text(`Toplam: ${total.toFixed(2)} ₺`);

    // Masa no doğrulama
    $('#tableNoInput')
        .off('input')
        .on('input', function () {
            let no = parseInt($(this).val());
            $('#checkoutBtn').prop('disabled', !(no >= 1 && no <= 20));
        })
        .trigger('input');
}

// -----------------------------------------------------
// SİPARİŞ OLUŞTURMA
// -----------------------------------------------------
function checkoutOrder() {

    if (checkoutRunning) return; // Çift tıklamayı engeller
    checkoutRunning = true;

    let tableNo = parseInt($('#tableNoInput').val());
    let total = cart.reduce((a, b) => a + b.price * b.quantity, 0);

    $.ajax({
        url: '../app/controllers/orders_create.php',
        type: 'POST',
        dataType: 'json',
        data: {
            cart: JSON.stringify(cart),
            table_no: tableNo,
            total_price: total
        },
        success: function (res) {
            checkoutRunning = false;

            if (res.success) {

                let toast = new bootstrap.Toast(document.getElementById('cartToast'));
                $('.toast-body').text("Sipariş #" + res.order_id + " oluşturuldu!");
                toast.show();

                cart = [];
                localStorage.removeItem('cart');
                cartCount = 0;
                updateCartCount();

                closeModal();

            } else {
                alert("Sipariş oluşturulamadı: " + res.message);
            }
        },
        error: function () {
            checkoutRunning = false;
            alert("Sunucu hatası!");
        }
    });
}
