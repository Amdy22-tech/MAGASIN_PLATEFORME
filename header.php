<?php
// header.php - En-tête commun à toutes les pages
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMDY'S - <?= $pageTitle ?? 'Plateforme Informatique' ?></title>
    <style>
        :root {
            --primary: #0a2540;
            --accent: #e63946;
            --gold: #f4a261;
            --light: #f8f9fa;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; color: #333; }

        /* HEADER */
        .main-header {
            background: linear-gradient(135deg, #0a2540 0%, #1a3a5c 50%, #0a2540 100%);
            color: white;
            padding: 0;
            box-shadow: var(--shadow);
        }
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 30px;
            border-bottom: 2px solid rgba(244,162,97,0.4);
        }
        .logo-left, .logo-right {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
        .logo-circle {
            width: 70px; height: 70px;
            border-radius: 50%;
            border: 3px solid var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 11px; text-align: center;
            background: rgba(255,255,255,0.1);
            color: var(--gold);
            line-height: 1.2;
            padding: 5px;
        }
        .logo-label { font-size: 10px; color: rgba(255,255,255,0.7); text-align: center; max-width: 80px; }
        .header-center { text-align: center; flex: 1; }
        .brand-name {
            font-size: 2.8rem; font-weight: 900; letter-spacing: 4px;
            background: linear-gradient(90deg, #f4a261, #ffffff, #f4a261);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-shadow: none;
        }
        .brand-tagline { font-size: 0.85rem; color: rgba(255,255,255,0.7); letter-spacing: 2px; margin-top: 2px; }

        /* NAV */
        .main-nav {
            background: rgba(0,0,0,0.2);
            padding: 8px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
        }
        .nav-links { display: flex; gap: 6px; flex-wrap: wrap; }
        .nav-links a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .nav-links a:hover, .nav-links a.active {
            background: var(--gold);
            color: var(--primary);
            border-color: var(--gold);
        }
        .nav-user { display: flex; align-items: center; gap: 10px; font-size: 0.82rem; color: rgba(255,255,255,0.8); }
        .nav-user strong { color: var(--gold); }
        .btn-logout {
            background: var(--accent); color: white;
            padding: 5px 12px; border-radius: 15px;
            text-decoration: none; font-size: 0.78rem;
            transition: opacity 0.2s;
        }
        .btn-logout:hover { opacity: 0.85; }

        /* CONTENU */
        .page-content { padding: 25px 30px; max-width: 1400px; margin: 0 auto; }
        .page-title {
            font-size: 1.5rem; font-weight: 700; color: var(--primary);
            margin-bottom: 20px; padding-bottom: 10px;
            border-bottom: 3px solid var(--gold);
            display: flex; align-items: center; gap: 10px;
        }

        /* TABLEAUX */
        .table-container { background: white; border-radius: 12px; box-shadow: var(--shadow); overflow: hidden; }
        .table-header {
            padding: 18px 20px;
            display: flex; justify-content: space-between; align-items: center;
            background: var(--primary); color: white;
        }
        .table-header h3 { font-size: 1rem; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8f9fa; }
        th { padding: 12px 15px; text-align: left; font-size: 0.82rem; font-weight: 700;
             color: #555; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e0e0e0; }
        td { padding: 11px 15px; font-size: 0.88rem; border-bottom: 1px solid #f0f0f0; }
        tr:hover td { background: #fafbff; }
        tr:last-child td { border-bottom: none; }

        /* BOUTONS */
        .btn { padding: 9px 20px; border: none; border-radius: 8px; cursor: pointer;
               font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-block;
               transition: all 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #1a3a5c; transform: translateY(-1px); }
        .btn-success { background: #2d6a4f; color: white; }
        .btn-success:hover { background: #1b4332; }
        .btn-danger { background: var(--accent); color: white; }
        .btn-danger:hover { background: #c1121f; }
        .btn-warning { background: var(--gold); color: var(--primary); }
        .btn-warning:hover { background: #e08c4a; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-quitter { background: #6c757d; color: white; }

        /* ALERTES */
        .alert { padding: 12px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 0.88rem; }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .alert-danger  { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .alert-info    { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }

        /* BADGE */
        .badge { padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger  { background: #f8d7da; color: #721c24; }
        .badge-info    { background: #d1ecf1; color: #0c5460; }

        /* MODAL */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0;
            width: 100%; height: 100%; background: rgba(0,0,0,0.5);
            z-index: 999; align-items: center; justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: white; border-radius: 14px; padding: 30px;
            width: 90%; max-width: 500px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-title { font-size: 1.2rem; font-weight: 700; color: var(--primary); margin-bottom: 20px;
                       padding-bottom: 10px; border-bottom: 2px solid var(--gold); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 0.83rem; font-weight: 600; color: #555; margin-bottom: 5px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 9px 12px; border: 1.5px solid #ddd; border-radius: 7px;
            font-size: 0.88rem; transition: border-color 0.2s;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none; border-color: var(--primary);
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }

        /* FOOTER */
        .main-footer {
            background: var(--primary); color: rgba(255,255,255,0.6);
            text-align: center; padding: 16px;
            font-size: 0.8rem; margin-top: 40px;
        }
        .main-footer strong { color: var(--gold); }
    </style>
</head>
<body>
<header class="main-header">
    <div class="header-top">
        <div class="logo-left">
            <a><img src="logo_uac.jpg" height="50" alt="UAC"></a>
            
            <div class="logo-label">Université d'Abomey-Calavi</div>
        </div>
        <div class="header-center">
            <div class="brand-name">AMDY'S</div>
            <div class="brand-tagline">⚡ Votre partenaire informatique de confiance ⚡</div>
        </div>
        <div class="logo-right">
            <a><img src="logo_eneam.jpg" height="50" alt="ENEAM"></a>
            
            <div class="logo-label">École Nationale d'Éco. Appliquée et de Management</div>
        </div>
    </div>
    <?php if (isLoggedIn()): ?>
    <nav class="main-nav">
        <div class="nav-links">
            <a href="accueil.php" <?= basename($_SERVER['PHP_SELF'])=='accueil.php'?'class="active"':'' ?>>🏠 Accueil</a>
            <?php if (isVendeur()): ?>
            <a href="clients.php"  <?= basename($_SERVER['PHP_SELF'])=='clients.php'?'class="active"':'' ?>>👥 Clients</a>
            <a href="users.php"    <?= basename($_SERVER['PHP_SELF'])=='users.php'?'class="active"':'' ?>>👤 Utilisateurs</a>
            <a href="articles.php" <?= basename($_SERVER['PHP_SELF'])=='articles.php'?'class="active"':'' ?>>💻 Articles</a>
            <a href="ventes.php"   <?= basename($_SERVER['PHP_SELF'])=='ventes.php'?'class="active"':'' ?>>💰 Ventes</a>
            <a href="commandes.php"<?= basename($_SERVER['PHP_SELF'])=='commandes.php'?'class="active"':'' ?>>📦 Commandes</a>
            <a href="effectuer_vente.php" <?= basename($_SERVER['PHP_SELF'])=='effectuer_vente.php'?'class="active"':'' ?>>🛒 Effectuer une vente</a>
            <?php else: ?>
            <a href="articles.php" <?= basename($_SERVER['PHP_SELF'])=='articles.php'?'class="active"':'' ?>>💻 Articles</a>
            <a href="mes_commandes.php" <?= basename($_SERVER['PHP_SELF'])=='mes_commandes.php'?'class="active"':'' ?>>📦 Mes Commandes</a>
            <a href="passer_commande.php" <?= basename($_SERVER['PHP_SELF'])=='passer_commande.php'?'class="active"':'' ?>>🛒 Passer une commande</a>
            <?php endif; ?>
        </div>
        <div class="nav-user">
            Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['prenom'] ?? '') ?> <?= htmlspecialchars($_SESSION['nom'] ?? '') ?></strong>
            <span class="badge <?= isVendeur() ? 'badge-warning' : 'badge-info' ?>">
                <?= isVendeur() ? '🏪 Vendeur' : '🙋 Client' ?>
            </span>
            <a href="logout.php" class="btn-logout">🚪 Déconnexion</a>
        </div>
    </nav>
    <?php endif; ?>
</header>
