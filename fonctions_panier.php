<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function cartInit(): void {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}


function cartAdd(int $productId, int $qty = 1): void {
    cartInit();
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $qty;
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}


function cartUpdate(int $productId, int $qty): void {
    cartInit();
    if ($qty <= 0) {
        cartRemove($productId);
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}


function cartRemove(int $productId): void {
    cartInit();
    unset($_SESSION['cart'][$productId]);
}


function cartClear(): void {
    $_SESSION['cart'] = [];
}


function cartGetItems(PDO $pdo): array {
    cartInit();
    if (empty($_SESSION['cart'])) {
        return [];
    }

    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $items = [];
    foreach ($products as $product) {
        $pid = (int)$product['id'];
        $qty = $_SESSION['cart'][$pid];
        $items[] = [
            'product'   => $product,
            'quantity'  => $qty,
            'subtotal'  => $product['price'] * $qty,
        ];
    }
    return $items;
}


function cartGetTotal(PDO $pdo): float {
    $total = 0.0;
    foreach (cartGetItems($pdo) as $item) {
        $total += $item['subtotal'];
    }
    return $total;
}


function cartGetCount(): int {
    cartInit();
    return array_sum($_SESSION['cart']);
}
