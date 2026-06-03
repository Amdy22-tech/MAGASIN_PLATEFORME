<?php
require_once 'config.php';
requireLogin();
$pageTitle = "Articles";

$msg = '';

// Ajouter article (vendeur seulement)
if (isVendeur() && $_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='ajouter') {
    $nom   = sanitize($_POST['nom']);
    $desc  = sanitize($_POST['description']);
    $prix  = (float)$_POST['prix'];
    $stock = (int)$_POST['stock'];
    $cat   = sanitize($_POST['categorie']);
    $img   = sanitize($_POST['image_url']);
    $conn->query("INSERT INTO articles (nom,description,prix,stock,categorie,image_url)
                  VALUES ('$nom','$desc',$prix,$stock,'$cat','$img')");
    $msg = '<div class="alert alert-success">Article ajouté avec succès !</div>';
}

if (isVendeur() && isset($_GET['suppr'])) {
    $id = (int)$_GET['suppr'];
    $conn->query("DELETE FROM articles WHERE id=$id");
    header('Location: articles.php');
    exit();
}

// Vue
$vue = $_GET['vue'] ?? (isVendeur() ? 'tableau' : 'grille');

include 'header.php';
?>
<div class="page-content">
<?= $msg ?>

<div class="page-title">💻 Catalogue des Articles</div>

<!-- Barre d'outils -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
    <div style="display:flex;gap:8px;">
        <a href="?vue=grille" class="btn <?= $vue==='grille'?'btn-primary':'btn-secondary' ?>">🔲 Grille</a>
        <a href="?vue=tableau" class="btn <?= $vue==='tableau'?'btn-primary':'btn-secondary' ?>">📋 Tableau</a>
    </div>
    <div style="display:flex;gap:8px;">
        <?php if (isVendeur()): ?>
        <button class="btn btn-success" onclick="document.getElementById('modal-add').classList.add('active')">➕ Ajouter</button>
        <?php else: ?>
        <a href="passer_commande.php" class="btn btn-success">🛒 Commander</a>
        <?php endif; ?>
        <a href="accueil.php" class="btn btn-quitter">🚪 Quitter</a>
    </div>
</div>

<?php
$q = $conn->query("SELECT * FROM articles ORDER BY categorie, nom");
$articles = [];
while ($r = $q->fetch_assoc()) $articles[] = $r;
?>

<?php if ($vue === 'grille'): ?>
<!-- VUE GRILLE avec images -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px;">
    <?php foreach ($articles as $a): ?>
    <div style="background:white;border-radius:14px;box-shadow:0 4px 15px rgba(0,0,0,0.08);overflow:hidden;transition:transform 0.2s;"
         onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
        <div style="height:180px;overflow:hidden;background:#f0f2f5;position:relative;">
            <img src="<?= htmlspecialchars($a['image_url']) ?>" alt="<?= htmlspecialchars($a['nom']) ?>"
                 style="width:100%;height:100%;object-fit:cover;"
                 onerror="this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=400'">
            <div style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,0.6);color:white;
                        font-size:0.7rem;padding:3px 8px;border-radius:10px;"><?= htmlspecialchars($a['categorie']) ?></div>
            <?php if ($a['stock'] <= 3): ?>
            <div style="position:absolute;top:8px;left:8px;background:#e63946;color:white;
                        font-size:0.7rem;padding:3px 8px;border-radius:10px;">⚠ Stock bas</div>
            <?php endif; ?>
        </div>
        <div style="padding:14px;">
            <div style="font-weight:700;font-size:0.9rem;color:#0a2540;margin-bottom:4px;line-height:1.3;">
                <?= htmlspecialchars($a['nom']) ?>
            </div>
            <div style="font-size:0.75rem;color:#888;margin-bottom:10px;line-height:1.4;">
                <?= htmlspecialchars(substr($a['description'],0,80)) ?>...
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div style="font-size:1.1rem;font-weight:900;color:#e63946;">
                    <?= number_format($a['prix']) ?> FCFA
                </div>
                <div style="font-size:0.75rem;color:#555;">
                    Stock : <strong><?= $a['stock'] ?></strong>
                </div>
            </div>
            <?php if (!isVendeur()): ?>
            <a href="passer_commande.php?article=<?= $a['id'] ?>" class="btn btn-primary"
               style="width:100%;text-align:center;margin-top:10px;display:block;">🛒 Commander</a>
            <?php else: ?>
            <div style="display:flex;gap:6px;margin-top:10px;">
                <button class="btn btn-warning" style="flex:1;font-size:0.75rem;padding:6px;"
                        onclick="editArticle(<?= htmlspecialchars(json_encode($a)) ?>)">✏ Modifier</button>
                <a href="articles.php?suppr=<?= $a['id'] ?>" class="btn btn-danger"
                   onclick="return confirm('Supprimer ?')" style="flex:1;font-size:0.75rem;padding:6px;text-align:center;">🗑</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<!-- VUE TABLEAU -->
<div class="table-container">
    <div class="table-header"><h3>Liste des articles</h3></div>
    <table>
        <thead>
            <tr><th>#</th><th>Image</th><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($articles as $a): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><img src="<?= htmlspecialchars($a['image_url']) ?>" alt="" style="width:50px;height:40px;object-fit:cover;border-radius:6px;"
                     onerror="this.src='https://images.unsplash.com/photo-1518770660439-4636190af475?w=400'"></td>
            <td><strong><?= htmlspecialchars($a['nom']) ?></strong><br>
                <span style="font-size:0.75rem;color:#888;"><?= htmlspecialchars(substr($a['description'],0,50)) ?>...</span></td>
            <td><span class="badge badge-info"><?= htmlspecialchars($a['categorie']) ?></span></td>
            <td><strong style="color:#e63946;"><?= number_format($a['prix']) ?> FCFA</strong></td>
            <td><span class="badge <?= $a['stock']<=3?'badge-danger':($a['stock']<=10?'badge-warning':'badge-success') ?>">
                <?= $a['stock'] ?></span></td>
            <td>
                <?php if (isVendeur()): ?>
                <button class="btn btn-warning" style="padding:4px 8px;font-size:0.75rem;"
                        onclick="editArticle(<?= htmlspecialchars(json_encode($a)) ?>)">✏</button>
                <a href="articles.php?suppr=<?= $a['id'] ?>" class="btn btn-danger"
                   onclick="return confirm('Supprimer ?')" style="padding:4px 8px;font-size:0.75rem;">🗑</a>
                <?php else: ?>
                <a href="passer_commande.php?article=<?= $a['id'] ?>" class="btn btn-primary" style="padding:4px 10px;font-size:0.75rem;">🛒</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
</div>

<!-- MODAL AJOUTER -->
<div class="modal-overlay" id="modal-add">
    <div class="modal" style="max-width:600px;">
        <div class="modal-title" id="modal-title">➕ Ajouter un article</div>
        <form method="POST" id="form-article">
            <input type="hidden" name="action" value="ajouter">
            <input type="hidden" name="article_id" id="article_id">
            <div class="form-group"><label>Nom de l'article</label><input type="text" name="nom" id="f-nom" required></div>
            <div class="form-group"><label>Description</label>
                <textarea name="description" id="f-desc" rows="3" style="width:100%;padding:9px;border:1.5px solid #ddd;border-radius:7px;"></textarea></div>
            <div class="form-row">
                <div class="form-group"><label>Prix (FCFA)</label><input type="number" name="prix" id="f-prix" required min="0"></div>
                <div class="form-group"><label>Stock</label><input type="number" name="stock" id="f-stock" required min="0"></div>
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorie" id="f-cat">
                    <option>Ordinateurs portables</option><option>Smartphones</option><option>Tablettes</option>
                    <option>Écrans</option><option>Périphériques</option><option>Audio</option>
                    <option>Stockage</option><option>Réseau</option><option>Imprimantes</option>
                    <option>Composants</option><option>Alimentation</option><option>Accessoires</option>
                </select>
            </div>
            <div class="form-group"><label>URL Image</label><input type="url" name="image_url" id="f-img" placeholder="https://..."></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-add').classList.remove('active')">Annuler</button>
                <button type="submit" class="btn btn-success">✅ Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function editArticle(a) {
    document.getElementById('modal-title').textContent = '✏ Modifier l\'article';
    document.getElementById('form-article').action = 'articles_edit.php';
    document.getElementById('article_id').value = a.id;
    document.getElementById('f-nom').value   = a.nom;
    document.getElementById('f-desc').value  = a.description;
    document.getElementById('f-prix').value  = a.prix;
    document.getElementById('f-stock').value = a.stock;
    document.getElementById('f-cat').value   = a.categorie;
    document.getElementById('f-img').value   = a.image_url;
    document.querySelector('[name="action"]').value = 'modifier';
    document.getElementById('modal-add').classList.add('active');
}
</script>
<?php include 'footer.php'; ?>
