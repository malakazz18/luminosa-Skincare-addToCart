<?php


require_once 'config.php';
require_once 'fonctions_panier.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

verifierCsrf();
exigerConnexion();

$pdo  = getDB();
$user = $_SESSION['user'];

$items = cartGetItems($pdo);
if (empty($items)) {
    $_SESSION['order_errors'] = ['Votre panier est vide.'];
    header('Location: index.php');
    exit;
}

$total = cartGetTotal($pdo);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, shipping_address, total_price)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $fullName = $user['first_name'] . ' ' . $user['last_name'];
    $stmt->execute([
        $user['id'],
        $fullName,
        $user['email'],
        $user['phone'],
        $user['address'],
        $total,
    ]);
    $orderId = (int)$pdo->lastInsertId();

    $stmt2 = $pdo->prepare(
        'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)'
    );
    foreach ($items as $item) {
        $stmt2->execute([
            $orderId,
            $item['product']['id'],
            $item['quantity'],
            $item['product']['price'],
        ]);
        $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?')
            ->execute([$item['quantity'], $item['product']['id']]);
    }

    $pdo->commit();
    cartClear();

    $_SESSION['order_success'] = [
        'name'    => $user['first_name'],
        'total'   => $total,
        'address' => $user['address'],
    ];
    header('Location: confirmation.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['order_errors'] = ["Une erreur est survenue lors de la commande. Veuillez réessayer."];
    header('Location: index.php');
    exit;
}
