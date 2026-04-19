<?php

require_once 'config.php';
require_once 'fonctions_panier.php';

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name  = trim($_POST['customer_name']  ?? '');
$email = trim($_POST['customer_email'] ?? '');

// Basic validation
$errors = [];
if ($name === '')                    { $errors[] = 'Votre nom est requis.'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Une adresse e-mail valide est requise.'; }

$items = cartGetItems($pdo);
if (empty($items))                   { $errors[] = 'Votre panier est vide.'; }

if (!empty($errors)) {

    $_SESSION['order_errors'] = $errors;
    header('Location: index.php#cart-sidebar');
    exit;
}

$total = cartGetTotal($pdo);

try {
    $pdo->beginTransaction();

   
    $stmt = $pdo->prepare(
        'INSERT INTO orders (customer_name, customer_email, total_price) VALUES (?, ?, ?)'
    );
    $stmt->execute([$name, $email, $total]);
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
        'order_id' => $orderId,
        'name'     => $name,
        'total'    => $total,
    ];
    header('Location: confirmation.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['order_errors'] = ['Une erreur est survenue lors de la commande. Veuillez réessayer.'];
    header('Location: index.php');
    exit;
}
