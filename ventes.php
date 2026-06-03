<?php
require_once 'config.php';
requireVendeur();
$pageTitle = "Ventes";

include 'header.php';
?>
<div class="page-content">
<div class="page-title">💰 Liste des Ventes</div>

<!-- Résumé -->
<?php
$total = $conn->query("SELECT SUM(montant) s, COUNT(*) c FROM ventes")->fetch_assoc();
?>
<div style="display:flex;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
    <div style="background:white;border-radius:12px;padding:18px 24px;box-shadow:0 4px 15px rgba(0,0,0,0.08);border-left:5px solid #2d6a4f;">
        <div style="font-size:1.8rem;font-weight:900;color:#2d6a4f;"><?= number_format($total['c']) ?></div>
        <div style="font-size:0.82rem;color:#666;">Ventes enregistrées</div>
    </div>
    <div style="background:white;border-radius:12px;padding:18px 24px;box-shadow:0 4px 15px rgba(0,0,0,0.08);border-left:5px solid #8e44ad;">
        <div style="font-size:1.8rem;font-weight:900;color:#8e44ad;"><?= number_format($total['s']) ?> FCFA</div>
        <div style="font-size:0.82rem;color:#666;">Chiffre d'affaires total</div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3>Historique des ventes</h3>
        <div style="display:flex;gap:10px;">
            <a href="effectuer_vente.php" class="btn btn-success">➕ Nouvelle vente</a>
            <a href="accueil.php" class="btn btn-quitter">🚪 Quitter</a>
        </div>
    </div>
    <table>
        <thead>
            <tr><th>#Vente</th><th>#Commande</th><th>Client</th><th>Vendeur</th><th>Date</th><th>Montant</th><th>Détail</th></tr>
        </thead>
        <tbody>
        <?php
        $q = $conn->query("
            SELECT v.*, u.nom u_nom, u.prenom u_prenom,
                   cl.nom cl_nom, cl.prenom cl_prenom
            FROM ventes v
            JOIN users u ON v.user_id=u.id
            JOIN commandes c ON v.commande_id=c.id
            JOIN clients cl ON c.client_id=cl.id
            ORDER BY v.date_vente DESC
        ");
        while ($r = $q->fetch_assoc()):
        ?>
        <tr>
            <td><strong>#<?= $r['id'] ?></strong></td>
            <td>#<?= $r['commande_id'] ?></td>
            <td><?= htmlspecialchars($r['cl_prenom'].' '.$r['cl_nom']) ?></td>
            <td><?= htmlspecialchars($r['u_prenom'].' '.$r['u_nom']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($r['date_vente'])) ?></td>
            <td><strong style="color:#2d6a4f;"><?= number_format($r['montant']) ?> FCFA</strong></td>
            <td><a href="detail_commande.php?id=<?= $r['commande_id'] ?>" class="btn btn-primary" style="padding:4px 10px;font-size:0.75rem;">👁 Voir</a></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>
<?php include 'footer.php'; ?>
