<?php
require_once 'config.php';
requireVendeur();
$pageTitle = "Effectuer une vente";

$msg = '';

// Traitement de la vente
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='vente') {
    $client_id = (int)$_POST['client_id'];
    $articles  = $_POST['articles']  ?? [];
    $qtes      = $_POST['quantites'] ?? [];

    if (!$client_id || empty($articles)) {
        $msg = '<div class="alert alert-danger">Veuillez sélectionner un client et au moins un article.</div>';
    } else {
        $total = 0;
        $lignes_valides = [];
        foreach ($articles as $i => $art_id) {
            $art_id = (int)$art_id;
            $qte    = max(1, (int)($qtes[$i] ?? 1));
            if ($art_id <= 0) continue;
            $art = $conn->query("SELECT * FROM articles WHERE id=$art_id")->fetch_assoc();
            if ($art && $art['stock'] >= $qte) {
                $lignes_valides[] = ['id'=>$art_id,'qte'=>$qte,'prix'=>$art['prix'],'nom'=>$art['nom'],'stock'=>$art['stock']];
                $total += $art['prix'] * $qte;
            }
        }

        if (empty($lignes_valides)) {
            $msg = '<div class="alert alert-danger">Aucun article valide ou stock insuffisant.</div>';
        } else {
            // Créer commande
            $conn->query("INSERT INTO commandes (client_id, statut, montant_total) VALUES ($client_id,'confirmee',$total)");
            $commande_id = $conn->insert_id;

            // Lignes contenir
            foreach ($lignes_valides as $l) {
                $conn->query("INSERT INTO contenir (commande_id,article_id,quantite,prix_unitaire)
                              VALUES ($commande_id,{$l['id']},{$l['qte']},{$l['prix']})");
                // Décrémenter stock
                $conn->query("UPDATE articles SET stock=stock-{$l['qte']} WHERE id={$l['id']}");
            }

            // Enregistrer vente
            $uid = (int)$_SESSION['user_id'];
            $conn->query("INSERT INTO ventes (commande_id,user_id,montant) VALUES ($commande_id,$uid,$total)");

            $msg = "<div class='alert alert-success'>✅ Vente enregistrée avec succès ! Commande #$commande_id — Total : ".number_format($total)." FCFA
                    <a href='detail_commande.php?id=$commande_id' class='btn btn-primary' style='margin-left:10px;padding:4px 12px;font-size:0.8rem;'>Voir détail</a></div>";
        }
    }
}

// Données
$clients  = $conn->query("SELECT id, CONCAT(prenom,' ',nom) n FROM clients ORDER BY nom")->fetch_all(MYSQLI_ASSOC);
$articles_list = $conn->query("SELECT * FROM articles WHERE stock>0 ORDER BY categorie,nom")->fetch_all(MYSQLI_ASSOC);

include 'header.php';
?>
<div class="page-content">
<div class="page-title">🛒 Effectuer une Vente</div>

<?= $msg ?>

<div style="background:white;border-radius:14px;padding:28px;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
    <form method="POST" id="form-vente">
        <input type="hidden" name="action" value="vente">

        <!-- Sélection client -->
        <div style="margin-bottom:24px;">
            <div style="font-weight:700;color:#0a2540;font-size:1rem;margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #f4a261;">
                👤 Étape 1 : Sélectionner le client
            </div>
            <div class="form-group" style="max-width:400px;">
                <label>Client</label>
                <select name="client_id" required>
                    <option value="">-- Choisir un client --</option>
                    <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['n']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Articles -->
        <div style="margin-bottom:24px;">
            <div style="font-weight:700;color:#0a2540;font-size:1rem;margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #f4a261;">
                💻 Étape 2 : Ajouter des articles
            </div>
            <div id="lignes-articles">
                <div class="ligne-article" style="display:flex;gap:12px;align-items:flex-end;margin-bottom:10px;">
                    <div class="form-group" style="flex:3;margin:0;">
                        <label>Article</label>
                        <select name="articles[]" onchange="majPrix(this)" required>
                            <option value="">-- Choisir un article --</option>
                            <?php foreach ($articles_list as $a): ?>
                            <option value="<?= $a['id'] ?>" data-prix="<?= $a['prix'] ?>" data-stock="<?= $a['stock'] ?>">
                                <?= htmlspecialchars($a['nom']) ?> | <?= number_format($a['prix']) ?> FCFA | Stock: <?= $a['stock'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;margin:0;">
                        <label>Qté</label>
                        <input type="number" name="quantites[]" value="1" min="1" class="qte-input" onchange="calcTotal()">
                    </div>
                    <div style="margin-bottom:2px;">
                        <span class="prix-ligne" style="font-weight:700;color:#e63946;white-space:nowrap;">0 FCFA</span>
                    </div>
                    <div style="margin-bottom:2px;">
                        <button type="button" onclick="supprimerLigne(this)" class="btn btn-danger" style="padding:8px 12px;">🗑</button>
                    </div>
                </div>
            </div>
            <button type="button" onclick="ajouterLigne()" class="btn btn-secondary" style="margin-top:8px;">➕ Ajouter un article</button>
        </div>

        <!-- Total -->
        <div style="background:#f8f9fa;border-radius:10px;padding:18px;margin-bottom:20px;border:2px solid #f4a261;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-weight:700;font-size:1.1rem;color:#0a2540;">💵 Total de la vente :</span>
                <span id="total-display" style="font-size:1.5rem;font-weight:900;color:#e63946;">0 FCFA</span>
            </div>
        </div>

        <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-success" style="padding:12px 28px;font-size:1rem;">✅ Enregistrer la vente</button>
            <a href="accueil.php" class="btn btn-quitter">🚪 Quitter</a>
        </div>
    </form>
</div>

<!-- Table Lignes (Commandes en attente) -->
<div style="margin-top:28px;">
<div class="page-title" style="font-size:1.1rem;">📋 Clients ayant passé des commandes (Table LIGNE)</div>
<div class="table-container">
    <div class="table-header"><h3>Commandes récentes à traiter</h3></div>
    <table>
        <thead><tr><th>#Cmd</th><th>Client</th><th>Date</th><th>Nb articles</th><th>Total</th><th>Statut</th><th>Action</th></tr></thead>
        <tbody>
        <?php
        $q = $conn->query("
            SELECT c.*, cl.nom, cl.prenom,
                   (SELECT COUNT(*) FROM contenir ct WHERE ct.commande_id=c.id) nb
            FROM commandes c JOIN clients cl ON c.client_id=cl.id
            WHERE c.statut IN ('en_attente','confirmee')
            ORDER BY c.date_commande DESC LIMIT 10
        ");
        $badges = ['en_attente'=>'badge-warning','confirmee'=>'badge-info'];
        $labels = ['en_attente'=>'⏳ En attente','confirmee'=>'✅ Confirmée'];
        while ($r = $q->fetch_assoc()):
        ?>
        <tr>
            <td><strong>#<?= $r['id'] ?></strong></td>
            <td><?= htmlspecialchars($r['prenom'].' '.$r['nom']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($r['date_commande'])) ?></td>
            <td><?= $r['nb'] ?></td>
            <td><?= number_format($r['montant_total']) ?> FCFA</td>
            <td><span class="badge <?= $badges[$r['statut']] ?>"><?= $labels[$r['statut']] ?></span></td>
            <td><a href="detail_commande.php?id=<?= $r['id'] ?>" class="btn btn-primary" style="padding:4px 10px;font-size:0.75rem;">👁 Détail</a></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>

</div>

<script>
const articlesData = <?= json_encode(array_column($articles_list, null, 'id')) ?>;

function majPrix(sel) {
    const opt = sel.options[sel.selectedIndex];
    const prix = opt.dataset.prix || 0;
    const ligne = sel.closest('.ligne-article');
    const qte = ligne.querySelector('.qte-input').value;
    ligne.querySelector('.prix-ligne').textContent = formatFCFA(prix * qte);
    calcTotal();
}

function calcTotal() {
    let total = 0;
    document.querySelectorAll('.ligne-article').forEach(l => {
        const sel = l.querySelector('select');
        const opt = sel.options[sel.selectedIndex];
        const prix = parseFloat(opt?.dataset?.prix || 0);
        const qte  = parseInt(l.querySelector('.qte-input').value || 1);
        const sous = prix * qte;
        l.querySelector('.prix-ligne').textContent = formatFCFA(sous);
        total += sous;
    });
    document.getElementById('total-display').textContent = formatFCFA(total);
}

function formatFCFA(n) { return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA'; }

function ajouterLigne() {
    const modele = document.querySelector('.ligne-article').cloneNode(true);
    modele.querySelector('select').value = '';
    modele.querySelector('.qte-input').value = 1;
    modele.querySelector('.prix-ligne').textContent = '0 FCFA';
    modele.querySelector('select').onchange = function() { majPrix(this); };
    modele.querySelector('.qte-input').onchange = calcTotal;
    document.getElementById('lignes-articles').appendChild(modele);
}

function supprimerLigne(btn) {
    const lignes = document.querySelectorAll('.ligne-article');
    if (lignes.length > 1) { btn.closest('.ligne-article').remove(); calcTotal(); }
}
</script>
<?php include 'footer.php'; ?>
