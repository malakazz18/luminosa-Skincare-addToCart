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

$user = $_SESSION['user'] ?? null;
$csrfToken = csrfToken();
?>
<!DOCTYPE html>
<html lang="fr">
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
    <div class="header-right">
        <?php if ($user): ?>
            <div class="user-menu">
                <button class="user-btn" id="userMenuBtn">
                    <i class="ri-user-line"></i>
                    <span><?= htmlspecialchars($user['first_name']) ?></span>
                    <i class="ri-arrow-down-s-line"></i>
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <p class="user-dropdown-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                    <p class="user-dropdown-email"><?= htmlspecialchars($user['email']) ?></p>
                    <hr>
                    <button class="user-dropdown-item" id="btnLogout">
                        <i class="ri-logout-box-line"></i> Se déconnecter
                    </button>
                </div>
            </div>
        <?php else: ?>
            <button class="btn-auth" id="btnOpenAuth">
                <i class="ri-user-line"></i> Connexion
            </button>
        <?php endif; ?>
        <button class="cart-toggle" id="cartToggle" aria-label="Ouvrir le panier">
            <i class="ri-shopping-bag-line"></i>
            <span class="cart-badge <?= $cartCount > 0 ? 'visible' : '' ?>" id="cartBadge">
                <?= $cartCount ?>
            </span>
        </button>
    </div>
</header>


<div class="modal-overlay" id="authOverlay">
    <div class="modal" id="authModal">
        <button class="modal-close" id="authClose"><i class="ri-close-line"></i></button>

        <!-- Tabs -->
        <div class="auth-tabs">
            <button class="auth-tab active" data-tab="login">Connexion</button>
            <button class="auth-tab" data-tab="register">Créer un compte</button>
        </div>


        <div class="auth-panel active" id="tabLogin">
            <h2 class="auth-title">Bon retour ✨</h2>
            <div class="auth-errors" id="loginErrors"></div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="loginEmail" placeholder="votre@email.com" autocomplete="email">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" id="loginPassword" placeholder="••••••••" autocomplete="current-password">
            </div>
            <button class="btn-submit" id="btnLogin">
                <i class="ri-login-box-line"></i> Se connecter
            </button>
            <p class="auth-switch">Pas encore de compte ? <a href="#" data-switch="register">Créer un compte</a></p>
        </div>


        <div class="auth-panel" id="tabRegister">
            <h2 class="auth-title">Rejoignez Luminosa </h2>
            <div class="auth-errors" id="registerErrors"></div>
            <div class="form-row">
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" id="regFirstName" placeholder="Yasmine" autocomplete="given-name">
                </div>
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" id="regLastName" placeholder="Ben Ali" autocomplete="family-name">
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="regEmail" placeholder="votre@email.com" autocomplete="email">
            </div>
            <div class="form-group">
                <label>Numéro de téléphone</label>
                <input type="tel" id="regPhone" placeholder="+216 XX XXX XXX" autocomplete="tel">
            </div>
            <div class="form-group">
                <label>Adresse de livraison</label>
                <textarea id="regAddress" placeholder="Rue, ville, code postal..." rows="2" autocomplete="street-address"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" id="regPassword" placeholder="••••••••" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label>Confirmer</label>
                    <input type="password" id="regConfirm" placeholder="••••••••" autocomplete="new-password">
                </div>
            </div>
            <button class="btn-submit" id="btnRegister">
                <i class="ri-user-add-line"></i> Créer mon compte
            </button>
            <p class="auth-switch">Déjà un compte ? <a href="#" data-switch="login">Se connecter</a></p>
        </div>
    </div>
</div>

<aside class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
        <h2>Votre Panier</h2>
        <button class="cart-close" id="cartClose" aria-label="Fermer le panier">
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
                    <button class="cart-item-remove" aria-label="Retirer l'article">
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

        <?php if ($user): ?>

            <div class="checkout-summary">
                <div class="checkout-summary-row"><i class="ri-user-line"></i> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                <div class="checkout-summary-row"><i class="ri-phone-line"></i> <?= htmlspecialchars($user['phone']) ?></div>
                <div class="checkout-summary-row"><i class="ri-map-pin-line"></i> <?= htmlspecialchars($user['address']) ?></div>
            </div>
            <form action="passer_commande.php" method="POST" class="checkout-form">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="btn-checkout">
                    <i class="ri-secure-payment-line"></i> Confirmer la commande
                </button>
            </form>
        <?php else: ?>

            <div class="checkout-guest">
                <p><i class="ri-lock-line"></i> Connectez-vous pour finaliser votre commande</p>
                <button class="btn-checkout" id="btnCheckoutLogin">
                    <i class="ri-user-line"></i> Se connecter / S'inscrire
                </button>
            </div>
        <?php endif; ?>

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
    <p>© <?= date('Y') ?> Luminosa. Créé par Azzouz Malek G1 et Lahbib Malek G2.</p>
</footer>

<div class="toast" id="toast"></div>


<script>
    const IS_LOGGED_IN = <?= $user ? 'true' : 'false' ?>;
    const CSRF_TOKEN = '<?= $csrfToken ?>';
</script>
<script src="shop.js"></script>
</body>
</html>
