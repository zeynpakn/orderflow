<?php
session_start();
require_once '../app/core/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $comment = htmlspecialchars(trim($_POST['comment']));

    // Veritabanına Kaydet
    $sql = "INSERT INTO order_reviews (user_id, order_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$user_id, $order_id, $rating, $comment])) {
        header("Location: profile.php?success=1");
    } else {
        echo "Hata oluştu. Lütfen tekrar deneyin.";
    }
} else {
    header("Location: login.php");
}
?>