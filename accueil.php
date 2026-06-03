<?php
require_once 'config.php';
requireLogin();

$pageTitle = "Tableau de bord";

// Stats pour vendeur
$stats = [];
if (isVendeur()) {
    $stats['clients']   = $conn->query("SELECT COUNT(*) c FROM clients")->fetch_assoc()['c'];
    $stats['articles']  = $conn->query("SELECT COUNT(*) c FROM articles")->fetch_assoc()['c'];
    $stats['commandes'] = $conn->query("SELECT COUNT(*) c FROM commandes")->fetch_assoc()['c'];
    $stats['ventes']    = $conn->query("SELECT COUNT(*) c FROM ventes")->fetch_assoc()['c'];
    $stats['ca']        = $conn->query("SELECT SUM(montant) s FROM ventes")->fetch_assoc()['s'] ?? 0;
} else {
    $cid = $_SESSION['client_id'];
    $stats['commandes'] = $conn->query("SELECT COUNT(*) c FROM commandes WHERE client_id=$cid")->fetch_assoc()['c'];
    $stats['en_attente'] = $conn->query("SELECT COUNT(*) c FROM commandes WHERE client_id=$cid AND statut='en_attente'")->fetch_assoc()['c'];
}

include 'header.php';
?>
<div class="page-content">

<?php if (isVendeur()): ?>
<!-- ====== DASHBOARD VENDEUR ====== -->
<div class="page-title">🏠 Tableau de bord – Espace Vendeur</div>

<!-- Stats -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:28px;">
    <?php
    $cards = [
        ['👥','Clients',    $stats['clients'],   'clients.php',   '#2d6a4f','#d4edda'],
        ['💻','Articles',   $stats['articles'],  'articles.php',  '#0a2540','#d1ecf1'],
        ['📦','Commandes',  $stats['commandes'], 'commandes.php', '#7d4e2d','#fff3cd'],
        ['💰','Ventes',     $stats['ventes'],    'ventes.php',    '#721c24','#f8d7da'],
    ];
    foreach ($cards as [$ico,$lbl,$val,$link,$col,$bg]):
    ?>
    <a href="<?= $link ?>" style="text-decoration:none;">
        <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 4px 15px rgba(0,0,0,0.08);
                    border-left:5px solid <?= $col ?>;transition:transform 0.2s;cursor:pointer;"
             onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
            <div style="font-size:2rem;margin-bottom:8px;"><?= $ico ?></div>
            <div style="font-size:1.8rem;font-weight:900;color:<?= $col ?>;"><?= number_format($val) ?></div>
            <div style="font-size:0.82rem;color:#666;font-weight:600;"><?= $lbl ?></div>
        </div>
    </a>
    <?php endforeach; ?>
    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 4px 15px rgba(0,0,0,0.08);
                border-left:5px solid #8e44ad;">
        <div style="font-size:2rem;margin-bottom:8px;">💵</div>
        <div style="font-size:1.4rem;font-weight:900;color:#8e44ad;"><?= number_format($stats['ca']) ?> F</div>
        <div style="font-size:0.82rem;color:#666;font-weight:600;">Chiffre d'affaires</div>
    </div>
</div>

<!-- Liens rapides -->
<div style="background:white;border-radius:14px;padding:24px;box-shadow:0 4px 15px rgba(0,0,0,0.08);margin-bottom:24px;">
    <div style="font-size:1rem;font-weight:700;color:#0a2540;margin-bottom:18px;padding-bottom:10px;border-bottom:2px solid #f4a261;">
        ⚡ Actions rapides
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;">
        <a href="clients.php"   class="btn btn-primary">👥 Gérer les clients</a>
        <a href="users.php"     class="btn btn-warning">👤 Gérer les utilisateurs</a>
        <a href="articles.php"  class="btn btn-success">💻 Gérer les articles</a>
        <a href="ventes.php"    class="btn btn-danger">💰 Voir les ventes</a>
        <a href="commandes.php" class="btn btn-secondary">📦 Voir les commandes</a>
        <a href="effectuer_vente.php" style="background:#8e44ad;color:white;padding:9px 20px;border-radius:8px;text-decoration:none;font-weight:600;font-size:0.85rem;">🛒 Effectuer une vente</a>
    </div>
</div>

<!-- Dernières commandes -->
<div class="table-container">
    <div class="table-header">
        <h3>📋 Dernières commandes</h3>
        <a href="commandes.php" class="btn btn-warning" style="font-size:0.78rem;padding:6px 14px;">Voir tout</a>
    </div>
    <table>
        <thead>
            <tr><th>#</th><th>Client</th><th>Date</th><th>Montant</th><th>Statut</th></tr>
        </thead>
        <tbody>
        <?php
        $q = $conn->query("SELECT c.*, cl.nom, cl.prenom FROM commandes c
                           JOIN clients cl ON c.client_id=cl.id ORDER BY c.date_commande DESC LIMIT 5");
        while ($row = $q->fetch_assoc()):
            $badges = ['en_attente'=>'badge-warning','confirmee'=>'badge-info','livree'=>'badge-success','annulee'=>'badge-danger'];
            $labels = ['en_attente'=>'En attente','confirmee'=>'Confirmée','livree'=>'Livrée','annulee'=>'Annulée'];
        ?>
        <tr>
            <td><strong>#<?= $row['id'] ?></strong></td>
            <td><?= htmlspecialchars($row['prenom'].' '.$row['nom']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['date_commande'])) ?></td>
            <td><strong><?= number_format($row['montant_total']) ?> FCFA</strong></td>
            <td><span class="badge <?= $badges[$row['statut']] ?>"><?= $labels[$row['statut']] ?></span></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php else: ?>
<!-- ====== DASHBOARD CLIENT ====== -->
<div class="page-title">🏠 Tableau de bord – Espace Client</div>

<div style="background:linear-gradient(135deg,#0a2540,#1a3a5c);color:white;border-radius:14px;padding:28px;margin-bottom:24px;">
    <div style="font-size:1.3rem;font-weight:700;margin-bottom:6px;">
        Bonjour, <?= htmlspecialchars($_SESSION['prenom'].' '.$_SESSION['nom']) ?> ! 👋
    </div>
    <p style="opacity:0.8;font-size:0.9rem;">Bienvenue sur AMDY'S, votre boutique informatique de confiance.</p>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:28px;">
    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 4px 15px rgba(0,0,0,0.08);border-left:5px solid #0a2540;">
        <div style="font-size:2rem;margin-bottom:8px;">📦</div>
        <div style="font-size:1.8rem;font-weight:900;color:#0a2540;"><?= $stats['commandes'] ?></div>
        <div style="font-size:0.82rem;color:#666;font-weight:600;">Mes commandes</div>
    </div>
    <div style="background:white;border-radius:14px;padding:20px;box-shadow:0 4px 15px rgba(0,0,0,0.08);border-left:5px solid #f4a261;">
        <div style="font-size:2rem;margin-bottom:8px;">⏳</div>
        <div style="font-size:1.8rem;font-weight:900;color:#f4a261;"><?= $stats['en_attente'] ?></div>
        <div style="font-size:0.82rem;color:#666;font-weight:600;">En attente</div>
    </div>
</div>

<div style="background:white;border-radius:14px;padding:24px;box-shadow:0 4px 15px rgba(0,0,0,0.08);">
    <div style="font-size:1rem;font-weight:700;color:#0a2540;margin-bottom:18px;padding-bottom:10px;border-bottom:2px solid #f4a261;">
        ⚡ Que souhaitez-vous faire ?
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;">
        <a href="articles.php"        class="btn btn-primary">💻 Voir les articles</a>
        <a href="passer_commande.php"  class="btn btn-success">🛒 Passer une commande</a>
        <a href="mes_commandes.php"   class="btn btn-warning">📦 Mes commandes</a>
    </div>
</div>
<?php endif; ?>

</div>
<?php include 'footer.php'; ?>
