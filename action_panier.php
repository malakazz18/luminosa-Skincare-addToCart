<?php

require_once 'config.php';
require_once 'fonctions_panier.php';

header('Content-Type: application/json');

$pdo    = getDB();
$action = $_POST['action'] ?? '';

switch ($action) {

    case 'add':
        $id  = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['quantity']   ?? 1);
        if ($id > 0 && $qty > 0) {
            cartAdd($id, $qty);
        }
        break;

    case 'update':
        $id  = (int)($_POST['product_id'] ?? 0);
        $qty = (int)($_POST['quantity']   ?? 0);
        if ($id > 0) {
            cartUpdate($id, $qty);
        }
        break;

    case 'remove':
        $id = (int)($_POST['product_id'] ?? 0);
        if ($id > 0) {
            cartRemove($id);
        }
        break;

    case 'clear':
        cartClear();
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
        exit;
}


$items = cartGetItems($pdo);
$rows  = [];
foreach ($items as $item) {
    $rows[] = [
        'id'       => $item['product']['id'],
        'name'     => $item['product']['name'],
        'image'    => $item['product']['image'],
        'price'    => $item['product']['price'],
        'quantity' => $item['quantity'],
        'subtotal' => $item['subtotal'],
    ];
}

echo json_encode([
    'success' => true,
    'items'   => $rows,
    'total'   => cartGetTotal($pdo),
    'count'   => cartGetCount(),
]);
