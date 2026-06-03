<?php
require_once 'config.php';
requireVendeur();
$pageTitle = "Clients";

$msg = '';

// Ajouter client
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='ajouter') {
    $nom    = sanitize($_POST['nom']);
    $prenom = sanitize($_POST['prenom']);
    $email  = sanitize($_POST['email']);
    $tel    = sanitize($_POST['telephone']);
    $adr    = sanitize($_POST['adresse']);
    $mdp    = password_hash('client123', PASSWORD_DEFAULT);
    $check  = $conn->query("SELECT id FROM clients WHERE email='$email'");
    if ($check->num_rows > 0) {
        $msg = '<div class="alert alert-danger">Cet email existe déjà.</div>';
    } else {
        $conn->query("INSERT INTO clients (nom,prenom,email,telephone,adresse,mot_de_passe) VALUES ('$nom','$prenom','$email','$tel','$adr','$mdp')");
        $msg = '<div class="alert alert-success">Client ajouté avec succès !</div>';
    }
}

// Supprimer
if (isset($_GET['suppr'])) {
    $id = (int)$_GET['suppr'];
    $conn->query("DELETE FROM clients WHERE id=$id");
    header('Location: clients.php?msg=supprime');
    exit();
}

include 'header.php';
?>
<div class="page-content">

<?php if (isset($_GET['msg']) && $_GET['msg']==='supprime'): ?>
<div class="alert alert-success">Client supprimé.</div>
<?php endif; ?>
<?= $msg ?>

<div class="page-title">👥 Liste des Clients</div>

<div class="table-container">
    <div class="table-header">
        <h3>Tous les clients enregistrés</h3>
        <div style="display:flex;gap:10px;">
            <button class="btn btn-success" onclick="document.getElementById('modal-ajouter').classList.add('active')">➕ Ajouter</button>
            <a href="accueil.php" class="btn btn-quitter">🚪 Quitter</a>
        </div>
    </div>
    <table>
        <thead>
            <tr><th>#</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Téléphone</th><th>Adresse</th><th>Inscription</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php
        $q = $conn->query("SELECT * FROM clients ORDER BY date_inscription DESC");
        while ($r = $q->fetch_assoc()):
        ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><strong><?= htmlspecialchars($r['nom']) ?></strong></td>
            <td><?= htmlspecialchars($r['prenom']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= htmlspecialchars($r['telephone']) ?></td>
            <td><?= htmlspecialchars($r['adresse']) ?></td>
            <td><?= date('d/m/Y', strtotime($r['date_inscription'])) ?></td>
            <td>
                <a href="clients.php?suppr=<?= $r['id'] ?>" class="btn btn-danger"
                   onclick="return confirm('Supprimer ce client ?')" style="padding:4px 10px;font-size:0.75rem;">🗑 Suppr</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>

<!-- MODAL AJOUTER -->
<div class="modal-overlay" id="modal-ajouter">
    <div class="modal">
        <div class="modal-title">➕ Ajouter un client</div>
        <form method="POST">
            <input type="hidden" name="action" value="ajouter">
            <div class="form-row">
                <div class="form-group"><label>Nom</label><input type="text" name="nom" required></div>
                <div class="form-group"><label>Prénom</label><input type="text" name="prenom" required></div>
            </div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Téléphone</label><input type="text" name="telephone"></div>
            <div class="form-group"><label>Adresse</label><input type="text" name="adresse"></div>
            <p style="font-size:0.78rem;color:#888;margin-bottom:10px;">Mot de passe par défaut : <strong>client123</strong></p>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-ajouter').classList.remove('active')">Annuler</button>
                <button type="submit" class="btn btn-success">✅ Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
