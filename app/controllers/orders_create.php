<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../core/db.php';

// Sadece POST kabul edilir
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST olmalı']);
    exit;
}

// Değerler
$cart = json_decode($_POST['cart'] ?? '[]', true);
$table_no = $_POST['table_no'] ?? null;
$total_price = $_POST['total_price'] ?? 0;

// Kullanıcı yoksa anonim = 0
$user_id = $_SESSION['user_id'] ?? 0;

// Kontrol
if (empty($cart) || !$table_no || $total_price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Eksik veri']);
    exit;
}

try {
    $pdo->beginTransaction();

    // SİPARİŞ EKLE
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, table_no, total_price, status)
        VALUES (?, ?, ?, 'hazırlanıyor')
    ");
    $stmt->execute([$user_id, $table_no, $total_price]);

    $order_id = $pdo->lastInsertId();

    // ÜRÜN EKLE
    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($cart as $item) {
        $itemStmt->execute([
            $order_id,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id
    ]);

} catch (PDOException $e) {

    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
