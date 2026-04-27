<?php
require_once 'config.php';
require_once 'fonctions_panier.php';

$success = $_SESSION['order_success'] ?? null;
if (!$success) {
    header('Location: index.php');
    exit;
}
unset($_SESSION['order_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande Confirmée — Luminosa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="confirm-page">

<header>
    <a href="index.php" class="logo">Lumin<em>osa</em></a>
</header>

<main class="confirm-main">
    <div class="confirm-card">
        <div class="confirm-icon"><i class="ri-checkbox-circle-line"></i></div>
        <h1>Merci, <?= htmlspecialchars($success['name']) ?> !</h1>
        <p>Votre commande a bien été confirmée.</p>
        <p class="confirm-total">Total payé : <strong><?= number_format($success['total'], 2) ?> TND</strong></p>
        <p class="confirm-address"><i class="ri-map-pin-line"></i> Livraison à : <?= htmlspecialchars($success['address']) ?></p>
        <p class="confirm-note">Nous vous enverrons des mises à jour par e-mail. Votre peau va vous remercier. ✨</p>
        <a href="index.php" class="btn-checkout" style="display:inline-flex;gap:8px;text-decoration:none;margin-top:10px;">
            <i class="ri-arrow-left-line"></i> Continuer mes achats
        </a>
    </div>
</main>

</body>
</html>
