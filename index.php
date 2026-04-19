<?php
require_once 'config.php';
require_once 'fonctions_panier.php';

$pdo = getDB();


$products = $pdo->query('SELECT * FROM products ORDER BY id')->fetchAll();


$orderErrors  = $_SESSION['order_errors']  ?? [];
$orderSuccess = $_SESSION['order_success'] ?? null;
unset($_SESSION['order_errors'], $_SESSION['order_success']);

$cartCount = cartGetCount();
$cartItems = cartGetItems($pdo);
$cartTotal = cartGetTotal($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luminosa — Boutique Skincare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <a href="index.php" class="logo">Lumin<em>osa</em></a>
    <button class="cart-toggle" id="cartToggle" aria-label="Open cart">
        <i class="ri-shopping-bag-line"></i>
        <span class="cart-badge <?= $cartCount > 0 ? 'visible' : '' ?>" id="cartBadge">
            <?= $cartCount ?>
        </span>
    </button>
</header>


<aside class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
        <h2>Votre Panier</h2>
        <button class="cart-close" id="cartClose" aria-label="Close cart">
            <i class="ri-close-line"></i>
        </button>
    </div>

    <?php if (!empty($orderErrors)): ?>
    <div class="flash flash-error">
        <?php foreach ($orderErrors as $err): ?>
            <p><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="cart-items" id="cartItems">
        <?php if (empty($cartItems)): ?>
            <p class="cart-empty">Votre panier est vide.</p>
        <?php else: ?>
            <?php foreach ($cartItems as $item): ?>
            <div class="cart-item" data-id="<?= $item['product']['id'] ?>">
                <img src="<?= htmlspecialchars($item['product']['image']) ?>"
                     alt="<?= htmlspecialchars($item['product']['name']) ?>">
                <div class="cart-item-info">
                    <p class="cart-item-name"><?= htmlspecialchars($item['product']['name']) ?></p>
                    <p class="cart-item-price"><?= number_format($item['product']['price'], 2) ?> TND</p>
                    <div class="qty-control">
                        <button class="qty-btn" data-action="decrement">−</button>
                        <span class="qty-value"><?= $item['quantity'] ?></span>
                        <button class="qty-btn" data-action="increment">+</button>
                    </div>
                </div>
                <div class="cart-item-right">
                    <span class="cart-item-subtotal"><?= number_format($item['subtotal'], 2) ?> TND</span>
                    <button class="cart-item-remove" aria-label="Remove item">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="cart-footer" id="cartFooter" <?= empty($cartItems) ? 'style="display:none"' : '' ?>>
        <div class="cart-total">
            <span>Total</span>
            <span id="cartTotal"><?= number_format($cartTotal, 2) ?> TND</span>
        </div>
        <form action="passer_commande.php" method="POST" class="checkout-form">
            <input type="text"  name="customer_name"  placeholder="Votre nom complet"  required>
            <input type="email" name="customer_email" placeholder="votre@email.com"  required>
            <button type="submit" class="btn-checkout">
                <i class="ri-secure-payment-line"></i> Confirmer la commande
            </button>
        </form>
        <button class="btn-clear" id="btnClear">Vider le panier</button>
    </div>
</aside>
<div class="cart-overlay" id="cartOverlay"></div>


<main>
    <section class="hero">
        <p class="hero-label">Skincare Coréenne</p>
        <h1>Votre peau.<br><em>Votre rituel.</em></h1>
        <p class="hero-sub">Des formules soigneusement sélectionnées pour chaque type de peau.</p>
    </section>

    <section class="products" id="products">
        <div class="product-grid">
            <?php foreach ($products as $i => $p): ?>
            <article class="product-card" style="--delay:<?= $i * 60 ?>ms">
                <div class="product-img-wrap">
                    <img src="<?= htmlspecialchars($p['image']) ?>"
                         alt="<?= htmlspecialchars($p['name']) ?>"
                         loading="lazy">
                    <?php if ($p['stock'] <= 5 && $p['stock'] > 0): ?>
                        <span class="badge badge-low">Plus que <?= $p['stock'] ?></span>
                    <?php elseif ($p['stock'] == 0): ?>
                        <span class="badge badge-out">Épuisé</span>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h2 class="product-name"><?= htmlspecialchars($p['name']) ?></h2>
                    <p class="product-desc"><?= htmlspecialchars($p['description']) ?></p>
                    <div class="product-footer">
                        <span class="product-price"><?= number_format($p['price'], 2) ?> TND</span>
                        <?php if ($p['stock'] > 0): ?>
                        <button class="btn-add"
                                data-id="<?= $p['id'] ?>"
                                data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                                aria-label="Ajouter au panier">
                            <i class="ri-shopping-bag-line"></i> Ajouter
                        </button>
                        <?php else: ?>
                        <button class="btn-add btn-disabled" disabled>Épuisé</button>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<footer>
    <p>© <?= date('Y') ?> Luminosa. Créé par Malek Azzouz 2LIG1 et Malek Lahbib 2LIG2.</p>
</footer>

<div class="toast" id="toast"></div>

<script src="shop.js"></script>
</body>
</html>
