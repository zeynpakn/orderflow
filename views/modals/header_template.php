<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Admin mi kontrolü yapıldı
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
?>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark" style="background-color: #1a1a1a;">
    <div class="container">
        <a class="navbar-brand fs-3" href="index.php"><i class="fas fa-coffee me-2"></i>OrderFlow</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Anasayfa</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#menu">Menü</a></li>
                
                <li class="nav-item"><a class="nav-link text-warning" href="reviews.php"><i class="fas fa-comments me-1"></i>Müşteri Yorumları</a></li>
                
                <li class="nav-item"><a class="nav-link" href="index.php#hakkimizda">Hakkımızda</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle btn btn-outline-light rounded-pill px-3" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> 
                            Merhaba, <?php echo explode(" ", $_SESSION['user_name'])[0]; ?> 
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-<?php echo $isAdmin ? 'tools' : 'id-card'; ?> me-2"></i>
                                    <?php echo $isAdmin ? 'Yönetici Paneli' : 'Profilim'; ?>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                        </ul>
                    </li>
                
                <?php else: ?>
                    <li class="nav-item ms-3">
                        <a href="login.php" class="btn btn-outline-light rounded-pill px-4">Giriş Yap</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>