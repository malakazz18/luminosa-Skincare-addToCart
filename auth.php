<?php

require_once 'config.php';
require_once 'fonctions_panier.php';

header('Content-Type: application/json');

// Rejeter toute requête non-POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
    exit;
}

$pdo    = getDB();
$action = $_POST['action'] ?? '';


if ($action !== 'logout') {
    verifierCsrf();
}

switch ($action) {

    case 'register':
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (limiteAtteinte("register_{$ip}", 5, 600)) {
            echo json_encode(['success' => false, 'errors' => ['Trop de tentatives. Réessayez dans 10 minutes.']]);
            exit;
        }

        $firstName = nettoyerChaine($_POST['first_name']  ?? '');
        $lastName  = nettoyerChaine($_POST['last_name']   ?? '');
        $email     = trim($_POST['email']                 ?? '');
        $phone     = nettoyerChaine($_POST['phone']       ?? '');
        $address   = nettoyerChaine($_POST['address']     ?? '');
        $password  = $_POST['password']                   ?? '';
        $confirm   = $_POST['password_confirm']           ?? '';

        $errors = [];
        if ($firstName === '')                                  { $errors[] = 'Le prénom est requis.'; }
        if ($lastName  === '')                                  { $errors[] = 'Le nom est requis.'; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))        { $errors[] = 'Email invalide.'; }
        if (!preg_match('/^\+?[\d\s\-]{8,15}$/', $phone))     { $errors[] = 'Numéro de téléphone invalide.'; }
        if ($address === '')                                    { $errors[] = "L'adresse est requise."; }
        if (strlen($password) < 6)                             { $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.'; }
        if ($password !== $confirm)                            { $errors[] = 'Les mots de passe ne correspondent pas.'; }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }

        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([strtolower($email)]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'errors' => ['Cet email est déjà utilisé.']]);
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare(
            'INSERT INTO users (first_name, last_name, email, phone, address, password_hash)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$firstName, $lastName, strtolower($email), $phone, $address, $hash]);
        $userId = (int)$pdo->lastInsertId();

        regenererSession();
        $_SESSION['user'] = [
            'id'         => $userId,
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => strtolower($email),
            'phone'      => $phone,
            'address'    => $address,
        ];

        echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
        break;

    case 'login':
        $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $cle = "login_{$ip}";

        if (limiteAtteinte($cle, 5, 300)) {
            echo json_encode(['success' => false, 'errors' => ['Trop de tentatives. Réessayez dans 5 minutes.']]);
            exit;
        }

        $email    = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password']              ?? '';

        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            echo json_encode(['success' => false, 'errors' => ['Email ou mot de passe incorrect.']]);
            exit;
        }

        reinitialiserLimite($cle);
        regenererSession();

        $_SESSION['user'] = [
            'id'         => (int)$user['id'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'email'      => $user['email'],
            'phone'      => $user['phone'],
            'address'    => $user['address'],
        ];


        if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                ->execute([$newHash, $user['id']]);
        }

        echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
        break;


    case 'logout':
        $_SESSION = [];
        session_destroy();
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Action inconnue.']);
}
