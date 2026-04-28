
# Luminosa — Boutique Skincare
### Application e-commerce PHP + MySQL par Azzouz Malek 2LIG1 et Lahbib Malek 2LIG2

---

## Structure des fichiers

```
luminosa/
├── config.php              ← Connexion PDO + démarrage session sécurisé
├── securite.php            ← Helpers sécurité (CSRF, rate limit, headers…)
├── fonctions_panier.php    ← Fonctions panier basées sur $_SESSION
├── action_panier.php       ← Endpoint AJAX panier (add/update/remove/clear)
├── auth.php                ← Endpoint AJAX auth (register/login/logout)
├── passer_commande.php     ← Traitement et enregistrement de la commande
├── index.php               ← Page principale (boutique + panier + modale auth)
├── confirmation.php        ← Page de confirmation post-commande
├── style.css               ← Feuille de style complète (palette pastel)
├── shop.js                 ← JavaScript (panier AJAX + modale auth)

```

---

## Base de données (4 tables)

| Table         | Rôle                                              |
|---------------|---------------------------------------------------|
| `products`    | Catalogue produits (nom, prix, stock, image)      |
| `users`       | Comptes clients (nom, email, téléphone, adresse)  |
| `orders`      | Commandes (client, livraison, total)              |
| `order_items` | Lignes de commande (produit × quantité × prix)    |

---

## Installation

### Prérequis
- PHP 8.1+
- MySQL 5.7+ ou MariaDB 10.4+
- XAMPP / WAMP / Laragon (ou serveur PHP natif)

### Étapes

**1. Placer le projet**
```
htdocs/luminosa/   ← XAMPP
www/luminosa/      ← WAMP / Laragon
```

**2. Importer la base de données**
```bash
mysql -u root -p < database.sql
mysql -u root -p skincare_shop < auth_migration.sql
```
Ou via phpMyAdmin → Import, dans cet ordre.

**3. Configurer la connexion**

Ouvrir `config.php` et modifier :
```php
define('DB_USER', 'root');   // votre utilisateur MySQL
define('DB_PASS', '');       // votre mot de passe MySQL
define('DB_NAME', 'skincare_shop');
```

**4. Placer les images produits**
```
luminosa/skincare images/   ← dossier des images
```

**5. Lancer**
```bash
php -S localhost:8000
# ou simplement ouvrir via XAMPP
```

**6. Accéder**
```
http://localhost:8000/index.php
```

---

## Fonctionnalités

- Catalogue produits chargé depuis MySQL
- Panier persistant en session PHP (add / update / remove / clear)
- Badge panier en temps réel
- Toast notifications
- Inscription avec : prénom, nom, email, téléphone, adresse de livraison
- Connexion / déconnexion via modale
- Commande réservée aux utilisateurs connectés
- Résumé livraison affiché dans le panier
- Page de confirmation post-commande
- Décrémentation du stock à l'achat
- Messages d'erreur flash

---

## Sécurité (securite.php)

| Mesure | Détail |
|---|---|
| En-têtes HTTP | `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection` |
| Cookies session | `HttpOnly`, `SameSite=Lax` |
| CSRF | Token généré par `random_bytes(32)`, vérifié sur tous les POST |
| Rate limiting | 5 tentatives max / 5 min pour la connexion, 5 / 10 min pour l'inscription |
| Mots de passe | Hashés avec `PASSWORD_BCRYPT`, coût 12, rehash automatique |
| Régénération session | Après chaque connexion / inscription |
| Requêtes SQL | 100 % PDO préparées (aucune injection possible) |
| Sorties HTML | `htmlspecialchars()` sur toutes les données affichées |
| Validation entrées | `nettoyerChaine()`, `nettoyerEntier()`, `filter_var()` |

---

## Notes techniques

- Aucun framework : PHP natif + PDO
- JavaScript vanilla (aucune dépendance)
- Transactions MySQL pour l'enregistrement des commandes
- `session_destroy()` complet à la déconnexion

voici une demonstration du projet :



https://github.com/user-attachments/assets/69353ec7-e605-487e-b5ce-c2e1507fffad







