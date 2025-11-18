$(document).ready(function () {

    // 1. Sticky Navbar Efekti
    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('.navbar').addClass('scrolled');
        } else {
            $('.navbar').removeClass('scrolled');
        }
    });

    // 2. Kategori Filtreleme (jQuery)
    $('.filter-btn').click(function () {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');

        var value = $(this).attr('data-filter');

        if (value == "all") {
            $('.product-item').fadeIn('1000');
        } else {
            $('.product-item').not('[data-category="' + value + '"]').hide('3000');
            $('.product-item').filter('[data-category="' + value + '"]').show('3000');
        }
    });

    // 3. Sepete Ekleme ve Animasyon
    let cartCount = 0;
    $('.add-to-cart').click(function () {
        // Sayacı Artır
        cartCount++;
        $('#cart-count').text(cartCount);

        // Toast Bildirimi Göster
        var toast = new bootstrap.Toast(document.getElementById('cartToast'));
        $('.toast-body').text($(this).data('name') + " sepete eklendi.");
        toast.show();

        // Sepet İkonunu Sallama Efekti
        $('.fab-cart').css('transform', 'scale(1.2)');
        setTimeout(function () {
            $('.fab-cart').css('transform', 'scale(1)');
        }, 200);
    });
});