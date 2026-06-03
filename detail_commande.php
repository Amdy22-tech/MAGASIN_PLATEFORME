<?php
require_once 'config.php';
requireLogin();
$pageTitle = "Détail commande";

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: accueil.php'); exit(); }

// Vérifier accès
if (isClient()) {
    $check = $conn->query("SELECT id FROM commandes WHERE id=$id AND client_id=".(int)$_SESSION['client_id']);
    if ($check->num_rows === 0) { header('Location: accueil.php'); exit(); }
}

$commande = $conn->query("
    SELECT c.*, cl.nom, cl.prenom, cl.email, cl.telephone, cl.adresse
    FROM commandes c JOIN clients cl ON c.client_id=cl.id
    WHERE c.id=$id
")->fetch_assoc();

if (!$commande) { header('Location: accueil.php'); exit(); }

$lignes = $conn->query("
    SELECT ct.*, a.nom a_nom, a.image_url, a.categorie
    FROM contenir ct JOIN articles a ON ct.article_id=a.id
    WHERE ct.commande_id=$id
");

include 'header.php';
?>
<div class="page-content">
<div class="page-title">📋 Détail de la Commande #<?= $id ?></div>

<!-- Infos commande -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px;">
    <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 4px 15px rgba(0,0,0,0.08);">
        <div style="font-weight:700;color:#0a2540;margin-bottom:12px;font-size:0.95rem;">👤 Informations client</div>
        <p><strong>Nom :</strong> <?= htmlspecialchars($commande['prenom'].' '.$commande['nom']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($commande['email']) ?></p>
        <p><strong>Tél :</strong> <?= htmlspecialchars($commande['telephone']) ?></p>
        <p><strong>Adresse :</strong> <?= htmlspecialchars($commande['adresse']) ?></p>
    </div>
    <div style="background:white;border-radius:12px;padding:20px;box-shadow:0 4px 15px rgba(0,0,0,0.08);">
        <div style="font-weight:700;color:#0a2540;margin-bottom:12px;font-size:0.95rem;">📦 Informations commande</div>
        <?php
        $badges = ['en_attente'=>'badge-warning','confirmee'=>'badge-info','livree'=>'badge-success','annulee'=>'badge-danger'];
        $labels = ['en_attente'=>'⏳ En attente','confirmee'=>'✅ Confirmée','livree'=>'🚚 Livrée','annulee'=>'❌ Annulée'];
        ?>
        <p><strong>Numéro :</strong> #<?= $commande['id'] ?></p>
        <p><strong>Date :</strong> <?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?></p>
        <p><strong>Statut :</strong> <span class="badge <?= $badges[$commande['statut']] ?>"><?= $labels[$commande['statut']] ?></span></p>
        <p><strong>Total :</strong> <span style="color:#e63946;font-size:1.1rem;font-weight:900;"><?= number_format($commande['montant_total']) ?> FCFA</span></p>
    </div>
</div>

<!-- Table contenir (lignes) -->
<div class="table-container">
    <div class="table-header"><h3>🛒 Articles commandés (Table CONTENIR)</h3></div>
    <table>
        <thead>
            <tr><th>#</th><th>Image</th><th>Article</th><th>Catégorie</th><th>Prix unitaire</th><th>Quantité</th><th>Sous-total</th></tr>
        </thead>
        <tbody>
        <?php $total = 0; while ($l = $lignes->fetch_assoc()): $sous = $l['prix_unitaire'] * $l['quantite']; $total += $sous; ?>
        <tr>
            <td><?= $l['id'] ?></td>
            <td><img src="<?= htmlspecialchars($l['image_url']) ?>" alt="" style="width:50px;height:40px;object-fit:cover;border-radius:6px;"
                     onerror="this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=400'"></td>
            <td><strong><?= htmlspecialchars($l['a_nom']) ?></strong></td>
            <td><span class="badge badge-info"><?= htmlspecialchars($l['categorie']) ?></span></td>
            <td><?= number_format($l['prix_unitaire']) ?> FCFA</td>
            <td><strong><?= $l['quantite'] ?></strong></td>
            <td><strong style="color:#2d6a4f;"><?= number_format($sous) ?> FCFA</strong></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr style="background:#f8f9fa;">
                <td colspan="6" style="text-align:right;font-weight:700;padding:12px 15px;">TOTAL :</td>
                <td style="font-weight:900;font-size:1.1rem;color:#e63946;padding:12px 15px;"><?= number_format($total) ?> FCFA</td>
            </tr>
        </tfoot>
    </table>
</div>

<div style="margin-top:16px;display:flex;gap:10px;">
    <?php if (isVendeur()): ?>
    <a href="commandes.php" class="btn btn-secondary">← Retour commandes</a>
    <a href="ventes.php"    class="btn btn-primary">💰 Voir ventes</a>
    <?php else: ?>
    <a href="mes_commandes.php" class="btn btn-secondary">← Mes commandes</a>
    <?php endif; ?>
    <a href="accueil.php" class="btn btn-quitter">🚪 Accueil</a>
</div>
</div>
<?php include 'footer.php'; ?>
