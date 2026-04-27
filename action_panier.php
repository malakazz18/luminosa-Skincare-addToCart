<?php

require_once 'config.php';
require_once 'fonctions_panier.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
    exit;
}

verifierCsrf();

$pdo    = getDB();
$action = $_POST['action'] ?? '';

switch ($action) {

    case 'add':
        $id  = nettoyerEntier($_POST['product_id'] ?? 0) ?? 0;
        $qty = nettoyerEntier($_POST['quantity']   ?? 1) ?? 1;
        if ($id > 0 && $qty > 0 && $qty <= 99) {
            cartAdd($id, $qty);
        }
        break;

    case 'update':
        $id  = nettoyerEntier($_POST['product_id'] ?? 0) ?? 0;
        $qty = nettoyerEntier($_POST['quantity']   ?? 0) ?? 0;
        if ($id > 0) {
            cartUpdate($id, $qty);
        }
        break;

    case 'remove':
        $id = nettoyerEntier($_POST['product_id'] ?? 0) ?? 0;
        if ($id > 0) {
            cartRemove($id);
        }
        break;

    case 'clear':
        cartClear();
        break;

    default:
        echo json_encode(['error' => 'Action inconnue.']);
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
