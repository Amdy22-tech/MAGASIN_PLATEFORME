<?php
require_once 'config.php';
if (!isClient()) { header('Location: index.php?error=acces_refuse'); exit(); }
$pageTitle = "Mes Commandes";
$cid = (int)$_SESSION['client_id'];
include 'header.php';
?>
<div class="page-content">
<div class="page-title">📦 Mes Commandes</div>
<div class="table-container">
    <div class="table-header">
        <h3>Historique de mes commandes</h3>
        <div style="display:flex;gap:10px;">
            <a href="passer_commande.php" class="btn btn-success">➕ Nouvelle commande</a>
            <a href="accueil.php" class="btn btn-quitter">🚪 Quitter</a>
        </div>
    </div>
    <table>
        <thead><tr><th>#</th><th>Date</th><th>Articles</th><th>Total</th><th>Statut</th><th>Détail</th></tr></thead>
        <tbody>
        <?php
        $q = $conn->query("
            SELECT c.*, (SELECT COUNT(*) FROM contenir ct WHERE ct.commande_id=c.id) nb
            FROM commandes c WHERE c.client_id=$cid ORDER BY c.date_commande DESC
        ");
        $badges = ['en_attente'=>'badge-warning','confirmee'=>'badge-info','livree'=>'badge-success','annulee'=>'badge-danger'];
        $labels = ['en_attente'=>'⏳ En attente','confirmee'=>'✅ Confirmée','livree'=>'🚚 Livrée','annulee'=>'❌ Annulée'];
        while ($r = $q->fetch_assoc()):
        ?>
        <tr>
            <td><strong>#<?= $r['id'] ?></strong></td>
            <td><?= date('d/m/Y H:i', strtotime($r['date_commande'])) ?></td>
            <td><?= $r['nb'] ?> article(s)</td>
            <td><strong><?= number_format($r['montant_total']) ?> FCFA</strong></td>
            <td><span class="badge <?= $badges[$r['statut']] ?>"><?= $labels[$r['statut']] ?></span></td>
            <td><a href="detail_commande.php?id=<?= $r['id'] ?>" class="btn btn-primary" style="padding:4px 10px;font-size:0.75rem;">👁 Voir</a></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
<?php include 'footer.php'; ?>
