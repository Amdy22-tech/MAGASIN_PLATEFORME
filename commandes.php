<?php
require_once 'config.php';
requireVendeur();
$pageTitle = "Commandes";

// Changer statut
if (isset($_GET['statut']) && isset($_GET['id'])) {
    $id  = (int)$_GET['id'];
    $st  = sanitize($_GET['statut']);
    $allowed = ['en_attente','confirmee','livree','annulee'];
    if (in_array($st, $allowed)) {
        $conn->query("UPDATE commandes SET statut='$st' WHERE id=$id");
    }
    header('Location: commandes.php');
    exit();
}

include 'header.php';
?>
<div class="page-content">
<div class="page-title">📦 Liste des Commandes</div>

<!-- Filtre statut -->
<?php $filtre = $_GET['f'] ?? 'tous'; ?>
<div style="display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap;">
    <?php foreach (['tous'=>'Toutes','en_attente'=>'En attente','confirmee'=>'Confirmées','livree'=>'Livrées','annulee'=>'Annulées'] as $k=>$v): ?>
    <a href="?f=<?= $k ?>" class="btn <?= $filtre===$k?'btn-primary':'btn-secondary' ?>" style="font-size:0.8rem;padding:6px 14px;"><?= $v ?></a>
    <?php endforeach; ?>
</div>

<div class="table-container">
    <div class="table-header">
        <h3>Commandes des clients</h3>
        <a href="accueil.php" class="btn btn-quitter">🚪 Quitter</a>
    </div>
    <table>
        <thead>
            <tr><th>#</th><th>Client</th><th>Date</th><th>Articles</th><th>Total</th><th>Statut</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php
        $where = $filtre !== 'tous' ? "WHERE c.statut='$filtre'" : '';
        $q = $conn->query("
            SELECT c.*, cl.nom, cl.prenom,
                   (SELECT COUNT(*) FROM contenir ct WHERE ct.commande_id=c.id) nb_articles
            FROM commandes c
            JOIN clients cl ON c.client_id=cl.id
            $where
            ORDER BY c.date_commande DESC
        ");
        $badges = ['en_attente'=>'badge-warning','confirmee'=>'badge-info','livree'=>'badge-success','annulee'=>'badge-danger'];
        $labels = ['en_attente'=>'⏳ En attente','confirmee'=>'✅ Confirmée','livree'=>'🚚 Livrée','annulee'=>'❌ Annulée'];
        while ($r = $q->fetch_assoc()):
        ?>
        <tr>
            <td><strong>#<?= $r['id'] ?></strong></td>
            <td><?= htmlspecialchars($r['prenom'].' '.$r['nom']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($r['date_commande'])) ?></td>
            <td><?= $r['nb_articles'] ?> article(s)</td>
            <td><strong><?= number_format($r['montant_total']) ?> FCFA</strong></td>
            <td><span class="badge <?= $badges[$r['statut']] ?>"><?= $labels[$r['statut']] ?></span></td>
            <td style="display:flex;gap:4px;flex-wrap:wrap;">
                <a href="detail_commande.php?id=<?= $r['id'] ?>" class="btn btn-primary" style="padding:4px 8px;font-size:0.72rem;">👁</a>
                <?php if ($r['statut']==='en_attente'): ?>
                <a href="?id=<?= $r['id'] ?>&statut=confirmee" class="btn btn-success" style="padding:4px 8px;font-size:0.72rem;">✅</a>
                <a href="?id=<?= $r['id'] ?>&statut=annulee"  class="btn btn-danger"  style="padding:4px 8px;font-size:0.72rem;">❌</a>
                <?php elseif ($r['statut']==='confirmee'): ?>
                <a href="?id=<?= $r['id'] ?>&statut=livree"   class="btn btn-warning" style="padding:4px 8px;font-size:0.72rem;">🚚</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
<?php include 'footer.php'; ?>
