# ☕ **OrderFlow – Çok Katlı Kafe Sipariş & Yönetim Sistemi**

OrderFlow, çok katlı kafelerde hem müşterilerin sipariş sürecini kolaylaştırmak hem de yöneticilerin sipariş–ürün yönetimini tek bir merkezi panel üzerinden hızlıca takip edebilmesini sağlamak amacıyla geliştirilmiş, modern ve genişletilebilir bir web uygulamasıdır.

Tamamen PHP ile geliştirilmiş olup, MySQL veritabanı üzerinde çalışır. Arayüzü latte–coffee temalı, sade ve mobil uyumlu olacak şekilde tasarlanmıştır.

<br>

## 🎯 **Projenin Amacı**

Geleneksel kafe işletmelerinde sipariş süreci çoğu zaman iş yükünü artırır ve karışıklığa neden olur. OrderFlow, bu süreci dijitalleştirerek:

* Müşterinin hızlıca ürün seçip sipariş verebilmesini,
* Anonim müşterilerin bile herhangi bir kayıt olmadan sipariş oluşturabilmesini,
* Yöneticinin tüm sipariş akışını tek panelden anlık takip edebilmesini,
* Ürün, fiyat ve kategori yönetiminin kolayca yapılabilmesini,
* Kafe deneyimi ve sipariş bazlı yorumların sistem üzerinden değerlendirilebilmesini,

sağlayan uçtan uca bir çözüm sunar.

<br> 

## 👥 **Kimler İçin Tasarlandı?**

### 🧍‍♀️ **Müşteri Tarafından Kullanımı**

OrderFlow’un müşteri deneyimi tamamen hız ve kolaylık üzerine kuruludur.
Müşteriler:

* Menüdeki ürünleri kategori bazlı filtrelerle görebilir,
* Ürünleri sepete ekleyebilir,
* Masa numarası seçerek anında sipariş verebilir,
* Dilerse **kayıt olmadan** “Anonim” olarak sipariş oluşturabilir,
* Kafe deneyimi hakkında yorum yapabilir,
* Sipariş sonrası gizli değerlendirme bırakabilir (tat, hız, servis memnuniyeti gibi),

Kayıt olan kullanıcılar ise ek olarak:

* **Geçmiş siparişlerini görüntüleyebilir**,
* Profil bilgilerini görebilir.

Kayıt olmayanlar sipariş verebilir ancak geçmişe erişemez — bu da sistemin esnekliğini artırır.

<br>

### 🛠️ **Yönetici (Admin) Tarafından Kullanımı**

Yönetici paneli işletmenin tüm operasyonel akışını kolaylaştırır.

Yöneticiler:

* Anlık sipariş akışını görebilir (bekleyen, hazırlanıyor, teslim edildi),
* Siparişlere masa numarası, toplam tutar, müşteri bilgisi (gerekirse anonim) ile ulaşabilir,
* Ürünlerin fiyatlarını düzenleyebilir,
* Ürünleri aktif/pasif durumuna alabilir,
* Gelen yorumları yönetebilir (genel kafe yorumları + sipariş bazlı gizli değerlendirmeler),
* Günlük toplam sipariş, teslim edilen sipariş, bekleyen sipariş sayılarını görebilir.

Bu yapı sayesinde kafe personeli, sipariş alım sürecini sıfır iletişim ile yönetebilir; sadece panel üzerinden sipariş durumlarını güncellemesi yeterlidir.


<br>

## ⚙️ **Kullanılan Teknolojiler**

### Backend

* **PHP 8+**
* **PDO** – Güvenli veritabanı bağlantısı
* **MySQL / MariaDB**

### Frontend

* **HTML5**
* **CSS3**
* **Bootstrap 5**
* **JavaScript**
* **jQuery**

### Tasarım & Yapı

* Component tabanlı header–footer yapısı
* Latte–coffee UI teması
* Responsive tasarım

<br>

## 🗄️ **Veritabanı Yapısı**

Aşağıdaki tablolar kullanılmaktadır:

* **users** → müşteriler
* **products** → ürün listesi
* **categories** → ürün kategorileri
* **orders** → siparişlerin ana tablosu
* **order_items** → sipariş içindeki ürünler
* **cafe_reviews** → genel kafe değerlendirmeleri
* **order_reviews** → sipariş bazlı gizli değerlendirmeler

> Veritabanı örneği repoya `cafe_db.sql` şeklinde eklenmiştir.
> Projeyi kendinizde de çalıştırıp görmek isterseniz bu kodu kopyalayıp gerekli yere yapıştırmanız yeterli olacaktır.

<br>

## 📂 **Proje Yapısı**

```
orderflow/
├── app/
│   └── controllers/               # İş mantığı (sipariş, ürün, yorum işlemleri)
│       ├── orders_create.php
│       └── products_get.php
│
├── core/
│   ├── config.local.php           # Kullanıcının kendi DB ayarlarını eklediği dosya
│   └── db.php                     # PDO veritabanı bağlantısı
│
├── public/
│   ├── css/
│   │   └── style_index.css        # Ana stil dosyası
│   │
│   ├── img/                       # Ürün görselleri
│   │   ├── caramel_latte.jpg
│   │   ├── cold_brew.jpg
│   │   ├── san_sebastian.jpg
│   │   └── turk_kahvesi.jpg
│   │
│   └── js/
│       └── script.js              # Genel JS fonksiyonları
│
├── views/
│   ├── modals/                    # Yeniden kullanılabilir modal bileşenleri
│   │   ├── cart-modal.php
│   │   ├── footer_template.php
│   │   └── header_template.php
│   │
│   ├── index.php                  # Ana sayfa (menü listesi)
│   ├── login.php                  # Kullanıcı girişi
│   ├── logout.php                 # Oturum kapatma
│   ├── products_management.php    # Yönetici ürün/fiyat yönetimi
│   ├── profile.php                # Kullanıcı profil sayfası
│   ├── register.php               # Kayıt olma sayfası
│   ├── reviews_management.php     # Yorum yönetim paneli (admin)
│   ├── reviews.php                # Misafir & müşteri yorum sayfası
│   └── submit_order_review.php    # Sipariş bazlı gizli değerlendirme
│
├── cafe_db.sql                    # Database bağlantısı için kullandıktan sonra dosyayı silebilirsiniz
├── .gitignore
└── README.md
```


<br>

## 🔧 **Kurulum**

### 1️⃣ Depoyu klonlayın

```
git clone https://github.com/zeynpakn/orderflow.git
```

### 2️⃣ Veritabanı oluşturun

phpMyAdmin üzerinden yeni bir veritabanı açın
ve repodaki cafe_db.sql dosyasını içe aktarın:

```
cafe_db.sql
```

### 3️⃣ `config.local.php` dosyasını oluşturun

`core/config.local.php` içine:

```php
<?php
$local_db_host = 'localhost';
$local_db_name = 'orderflow';
$local_db_user = '';
$local_db_pass = '';
?>
```

> Bu dosyaya kendi database bilgilerinizi girmelisiniz.

### 4️⃣ Uygulamayı çalıştırın

```
http://localhost/orderflow/views/index.php
```

<br>

## 🧩 **Neden OrderFlow?**

* Müşteriler için hızlı, kayıt zorunluluğu olmadan sipariş akışı sağlar.
* İşletme için sipariş karmaşasını ortadan kaldırır.
* Menü yönetimi, fiyat düzenleme ve sipariş takibi tek panelde birleştirilmiştir.
* Yönetici paneli ile tüm operasyon gerçek zamanlı ve basit bir şekilde kontrol edilir.
* Yorum ve değerlendirmeler, işletmenin kendini geliştirmesine olanak tanır.
* Tasarım olarak modern, sıcak, profesyonel bir kahve teması sunar.

<br>

## 👥 **Geliştiriciler**

* **[Hatice Kübra Ülke](https://github.com/hkubrau)**
* **[Zeynep Akın](https://github.com/zeynpakn)**
