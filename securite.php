<?php


function appliquerEntetesSec(): void {

    header('X-Content-Type-Options: nosniff');

    header('X-Frame-Options: SAMEORIGIN');

    header('X-XSS-Protection: 1; mode=block');

    header('Referrer-Policy: strict-origin-when-cross-origin');
}


function regenererSession(): void {
    session_regenerate_id(true);
}



function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}


function verifierCsrf(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'errors' => ['Token de sécurité invalide. Rechargez la page.']]);
        exit;
    }
}

// ── 4. Limitation de tentatives (rate limiting) ──────────────
/**
 * Limite les tentatives répétées (ex: connexion).
 * @param string $cle    Identifiant de l'action (ex: 'login_127.0.0.1')
 * @param int    $max    Nombre max de tentatives
 * @param int    $duree  Fenêtre en secondes
 * @return bool  true si la limite est atteinte
 */
function limiteAtteinte(string $cle, int $max = 5, int $duree = 300): bool {
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    $now = time();
    // Nettoyer les entrées expirées
    $_SESSION['rate_limit'] = array_filter(
        $_SESSION['rate_limit'],
        fn($e) => ($now - $e['debut']) < $duree
    );

    if (!isset($_SESSION['rate_limit'][$cle])) {
        $_SESSION['rate_limit'][$cle] = ['count' => 0, 'debut' => $now];
    }

    $_SESSION['rate_limit'][$cle]['count']++;

    return $_SESSION['rate_limit'][$cle]['count'] > $max;
}


function reinitialiserLimite(string $cle): void {
    unset($_SESSION['rate_limit'][$cle]);
}


function nettoyerChaine(string $valeur): string {
    return htmlspecialchars(strip_tags(trim($valeur)), ENT_QUOTES, 'UTF-8');
}


function nettoyerEntier(mixed $valeur): ?int {
    $v = filter_var($valeur, FILTER_VALIDATE_INT);
    return $v === false ? null : (int)$v;
}


function exigerConnexion(): void {
    if (empty($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }
}

appliquerEntetesSec();
