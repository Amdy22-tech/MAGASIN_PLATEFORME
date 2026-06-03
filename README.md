# AMDY'S - Plateforme de Vente Informatique
## Projet ENEAM / UAC

---
## 📋 Description du projet

**AMDY’S** est une plateforme web dynamique de vente de produits informatiques développée en **PHP/MySQL** et hébergée localement via **XAMPP**. Elle permet à deux types d’utilisateurs de naviguer sur la plateforme selon leur rôle :

- Les **clients** peuvent s’inscrire, parcourir le catalogue de produits informatiques avec leurs vraies images, et passer des commandes en ligne.
- Les **vendeurs et administrateurs** disposent d’un espace de gestion complet pour gérer les clients, les articles, les utilisateurs et les ventes.



## 📁 Structure des fichiers

```
amdys/
├── database.sql          ← Script SQL (base de données + 20 enregistrements)
├── config.php            ← Configuration BDD + fonctions utilitaires
├── header.php            ← En-tête commun (logos UAC & ENEAM)
├── footer.php            ← Pied de page commun
│
├── index.php             ← Page d'accueil publique (connexion + inscription)
├── logout.php            ← Déconnexion
├── accueil.php           ← Tableau de bord (après connexion)
│
├── clients.php           ← Liste des clients (vendeur uniquement)
├── users.php             ← Liste des utilisateurs/vendeurs (vendeur uniquement)
├── articles.php          ← Catalogue articles (tous + images réelles)
├── articles_edit.php     ← Traitement modification article
├── ventes.php            ← Liste des ventes (vendeur uniquement)
├── commandes.php         ← Liste des commandes (vendeur uniquement)
├── detail_commande.php   ← Détail d'une commande (table CONTENIR)
├── effectuer_vente.php   ← Interface de vente vendeur
│
├── passer_commande.php   ← Passer une commande (client uniquement)
└── mes_commandes.php     ← Mes commandes (client uniquement)
```

---

## 🚀 Installation

### Prérequis
- PHP 7.4+ ou PHP 8+
- MySQL 5.7+ ou MariaDB
- Apache/XAMPP/WAMP/LAMP

### Étapes

1. **Copier les fichiers** dans votre dossier web :
   - XAMPP : `C:/xampp/htdocs/amdys/`
   - Linux  : `/var/www/html/amdys/`

2. **Créer la base de données** :
   - Ouvrir phpMyAdmin
   - Aller dans "SQL"
   - Coller et exécuter le contenu de `database.sql`

3. **Configurer la connexion** dans `config.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');      // votre user MySQL
   define('DB_PASS', '');          // votre mot de passe MySQL
   define('DB_NAME', 'amdys_db');
   ```

4. **Accéder** à : `http://localhost/amdiProject/`

---

## 🔐 Comptes de test

### Vendeurs (mot de passe : `password`)
| Nom               | Email                | Rôle   |
|-------------------|----------------------|--------|
|MOUMOUNI Amdiyatou | amdi@amdys.bj        | Admin  |
| BONI Mariam       | mariam@amdys.bj      | Vendeur|
| MAHAMOUD Wafah    | wafah@amdys.bj       | Vendeur|
| KORA Aicha        |aichakora@amdys.bj    | Vendeur|


### Clients (mot de passe : `password`)
| Nom               | Email                      |
|-------------------|----------------------------|
| AZONHIHO Fabrice  | fabrice.azonhiho@gmail.com |
| GNONLONFOUN Stéph.| stephanie.g@gmail.com      |
| HOUNSOU Théodore  | theodore.h@yahoo.fr        |
| (+ 13 autres)     | voir database.sql          |

> **Note :** Les mots de passe dans la BDD sont hashés avec `password_hash()`.
> Le hash `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uZutLn/GW` correspond au mot de passe `password`.

---

## 🛡️ Sécurité & Rôles

| Page                 | Client | Vendeur |
|----------------------|--------|---------|
| index.php            | ✅     | ✅      |
| accueil.php          | ✅     | ✅      |
| articles.php (voir)  | ✅     | ✅      |
| articles.php (gérer) | ❌     | ✅      |
| clients.php          | ❌     | ✅      |
| users.php            | ❌     | ✅      |
| ventes.php           | ❌     | ✅      |
| commandes.php        | ❌     | ✅      |
| effectuer_vente.php  | ❌     | ✅      |
| passer_commande.php  | ✅     | ❌      |
| mes_commandes.php    | ✅     | ❌      |

---

## 🗄️ Tables de la base de données

| Table       | Description                              |
|-------------|------------------------------------------|
| `users`     | Vendeurs et admins                       |
| `clients`   | Clients inscrits                         |
| `articles`  | Produits informatiques (20 articles)     |
| `commandes` | Commandes passées                        |
| `contenir`  | Lignes de commande (article + quantité)  |
| `ventes`    | Ventes enregistrées par les vendeurs     |

---
	**Relations :** `ventes` → `commandes` → `contenir` → `articles` / `clients`


## 👤 Gestion des rôles et accès

La plateforme distingue **deux espaces distincts et sécurisés** :

### Espace Client
- Accès via `commandes.php`
- Inscription libre depuis `index.php`
- Fonctionnalités : parcourir le catalogue, filtrer par catégorie, rechercher un produit, passer une commande, consulter ses commandes

### Espace Vendeur / Admin
- Accès via `accueil.php`
- Les vendeurs sont **pré-enregistrés** dans la base de données
- À l’inscription, si le nom saisi ne correspond à aucun vendeur existant → l’utilisateur est automatiquement inscrit comme **client**
- Fonctionnalités : tableau de bord, gestion clients, gestion utilisateurs, catalogue articles, suivi des ventes, saisie de ventes

### Règles de sécurité
- Toutes les pages vendeur vérifient la présence de `$_SESSION[‘user_id’]`
- Un client connecté (`$_SESSION[‘client_id’]`) tentant d’accéder à une page vendeur est **automatiquement redirigé** vers `boutique.php`
- Les mots de passe sont hashés avec `password_hash()` (algorithme bcrypt)
- Les requêtes SQL utilisent des **requêtes préparées** (`mysqli prepared statements`) pour prévenir les injections SQL


## 🛠️ Technologies utilisées

| Technologie | Usage |
	
| **PHP 8.x** | Logique serveur, sessions, traitement des formulaires |
| **MySQL** | Stockage des données (via phpMyAdmin / XAMPP) |
| **HTML5 / CSS3** | Structure et mise en page |
| **JavaScript (Vanilla)** | Interactivité côté client (calcul totaux, prévisualisation images) |
| **Font Awesome 6** | Icônes |
| **Google Fonts** | Typographies (Playfair Display, DM Sans) |
| **XAMPP** | Serveur local Apache + MySQL |

