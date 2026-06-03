<?php
require_once 'config.php';
if (!isClient()) { header('Location: index.php?error=acces_refuse'); exit(); }
$pageTitle = "Passer une commande";

$msg = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='commander') {
    $articles  = $_POST['articles']  ?? [];
    $qtes      = $_POST['quantites'] ?? [];
    $client_id = (int)$_SESSION['client_id'];
    $total = 0;
    $lignes_valides = [];
    foreach ($articles as $i => $art_id) {
        $art_id = (int)$art_id;
        $qte    = max(1,(int)($qtes[$i]??1));
        if ($art_id <= 0) continue;
        $art = $conn->query("SELECT * FROM articles WHERE id=$art_id AND stock>=$qte")->fetch_assoc();
        if ($art) {
            $lignes_valides[] = ['id'=>$art_id,'qte'=>$qte,'prix'=>$art['prix']];
            $total += $art['prix'] * $qte;
        }
    }
    if (empty($lignes_valides)) {
        $msg = '<div class="alert alert-danger">Aucun article valide ou stock insuffisant.</div>';
    } else {
        $conn->query("INSERT INTO commandes (client_id,statut,montant_total) VALUES ($client_id,'en_attente',$total)");
        $cmd_id = $conn->insert_id;
        foreach ($lignes_valides as $l) {
            $conn->query("INSERT INTO contenir (commande_id,article_id,quantite,prix_unitaire) VALUES ($cmd_id,{$l['id']},{$l['qte']},{$l['prix']})");
            $conn->query("UPDATE articles SET stock=stock-{$l['qte']} WHERE id={$l['id']}");
        }
        $msg = "<div class='alert alert-success'>✅ Commande #$cmd_id passée avec succès ! Total : ".number_format($total)." FCFA
                <a href='detail_commande.php?id=$cmd_id' class='btn btn-primary' style='margin-left:10px;padding:4px 12px;font-size:0.8rem;'>Voir détail</a></div>";
    }
}

// Pré-sélection
$preart = isset($_GET['article']) ? (int)$_GET['article'] : 0;
$articles_list = $conn->query("SELECT * FROM articles WHERE stock>0 ORDER BY categorie,nom")->fetch_all(MYSQLI_ASSOC);
include 'header.php';
?>
<div class="page-content">
<div class="page-title">🛒 Passer une commande</div>
<?= $msg ?>
<div style="background:white;border-radius:14px;padding:28px;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
    <form method="POST">
        <input type="hidden" name="action" value="commander">
        <div style="font-weight:700;color:#0a2540;font-size:1rem;margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #f4a261;">
            💻 Choisissez vos articles
        </div>
        <div id="lignes-articles">
            <div class="ligne-article" style="display:flex;gap:12px;align-items:flex-end;margin-bottom:10px;">
                <div class="form-group" style="flex:3;margin:0;">
                    <label>Article</label>
                    <select name="articles[]" onchange="majPrix(this)" required>
                        <option value="">-- Choisir --</option>
                        <?php foreach ($articles_list as $a): ?>
                        <option value="<?= $a['id'] ?>" data-prix="<?= $a['prix'] ?>" <?= $preart==$a['id']?'selected':'' ?>>
                            <?= htmlspecialchars($a['nom']) ?> | <?= number_format($a['prix']) ?> FCFA | Stock: <?= $a['stock'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex:1;margin:0;">
                    <label>Qté</label>
                    <input type="number" name="quantites[]" value="1" min="1" class="qte-input" onchange="calcTotal()">
                </div>
                <span class="prix-ligne" style="font-weight:700;color:#e63946;white-space:nowrap;margin-bottom:4px;">0 FCFA</span>
            </div>
        </div>
        <button type="button" onclick="ajouterLigne()" class="btn btn-secondary">➕ Ajouter un article</button>
        <div style="background:#f8f9fa;border-radius:10px;padding:16px;margin:20px 0;border:2px solid #f4a261;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-weight:700;">💵 Total :</span>
            <span id="total-display" style="font-size:1.4rem;font-weight:900;color:#e63946;">0 FCFA</span>
        </div>
        <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-success" style="padding:12px 28px;">✅ Confirmer la commande</button>
            <a href="articles.php" class="btn btn-secondary">← Voir les articles</a>
            <a href="accueil.php" class="btn btn-quitter">🚪 Quitter</a>
        </div>
    </form>
</div>
</div>
<script>
function majPrix(sel) {
    const prix = sel.options[sel.selectedIndex]?.dataset?.prix || 0;
    const ligne = sel.closest('.ligne-article');
    const qte = ligne.querySelector('.qte-input').value;
    ligne.querySelector('.prix-ligne').textContent = new Intl.NumberFormat('fr-FR').format(prix * qte) + ' FCFA';
    calcTotal();
}
function calcTotal() {
    let t = 0;
    document.querySelectorAll('.ligne-article').forEach(l => {
        const prix = parseFloat(l.querySelector('select').options[l.querySelector('select').selectedIndex]?.dataset?.prix || 0);
        const qte = parseInt(l.querySelector('.qte-input').value || 1);
        l.querySelector('.prix-ligne').textContent = new Intl.NumberFormat('fr-FR').format(prix*qte) + ' FCFA';
        t += prix * qte;
    });
    document.getElementById('total-display').textContent = new Intl.NumberFormat('fr-FR').format(t) + ' FCFA';
}
function ajouterLigne() {
    const m = document.querySelector('.ligne-article').cloneNode(true);
    m.querySelector('select').value = '';
    m.querySelector('.qte-input').value = 1;
    m.querySelector('.prix-ligne').textContent = '0 FCFA';
    m.querySelector('select').onchange = function(){ majPrix(this); };
    m.querySelector('.qte-input').onchange = calcTotal;
    document.getElementById('lignes-articles').appendChild(m);
}
<?php if ($preart): ?>window.onload = () => { document.querySelector('select').dispatchEvent(new Event('change')); };<?php endif; ?>
</script>
<?php include 'footer.php'; ?>
