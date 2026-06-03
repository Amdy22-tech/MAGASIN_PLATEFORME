<?php
require_once 'config.php';
requireVendeur();
$pageTitle = "Utilisateurs";

$msg = '';

// Ajouter utilisateur
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='ajouter') {
    $nom    = sanitize($_POST['nom']);
    $prenom = sanitize($_POST['prenom']);
    $email  = sanitize($_POST['email']);
    $role   = sanitize($_POST['role']);
    $mdp    = password_hash('vendeur123', PASSWORD_DEFAULT);
    $check  = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $msg = '<div class="alert alert-danger">Cet email existe déjà.</div>';
    } else {
        $conn->query("INSERT INTO users (nom,prenom,email,mot_de_passe,role) VALUES ('$nom','$prenom','$email','$mdp','$role')");
        $msg = '<div class="alert alert-success">Utilisateur ajouté !</div>';
    }
}

if (isset($_GET['suppr'])) {
    $id = (int)$_GET['suppr'];
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id=$id");
    }
    header('Location: users.php');
    exit();
}

include 'header.php';
?>
<div class="page-content">
<?= $msg ?>
<div class="page-title">👤 Liste des Utilisateurs (Vendeurs)</div>

<div class="table-container">
    <div class="table-header">
        <h3>Personnel de vente</h3>
        <div style="display:flex;gap:10px;">
            <button class="btn btn-success" onclick="document.getElementById('modal-add').classList.add('active')">➕ Ajouter</button>
            <a href="accueil.php" class="btn btn-quitter">🚪 Quitter</a>
        </div>
    </div>
    <table>
        <thead><tr><th>#</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Rôle</th><th>Créé le</th><th>Actions</th></tr></thead>
        <tbody>
        <?php
        $q = $conn->query("SELECT * FROM users ORDER BY id");
        while ($r = $q->fetch_assoc()):
        ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><strong><?= htmlspecialchars($r['nom']) ?></strong></td>
            <td><?= htmlspecialchars($r['prenom']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><span class="badge <?= $r['role']==='admin'?'badge-danger':'badge-warning' ?>"><?= ucfirst($r['role']) ?></span></td>
            <td><?= date('d/m/Y', strtotime($r['date_creation'])) ?></td>
            <td>
                <?php if ($r['id'] != $_SESSION['user_id']): ?>
                <a href="users.php?suppr=<?= $r['id'] ?>" class="btn btn-danger"
                   onclick="return confirm('Supprimer ?')" style="padding:4px 10px;font-size:0.75rem;">🗑</a>
                <?php else: ?>
                <span style="font-size:0.75rem;color:#888;">Vous</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div>

<div class="modal-overlay" id="modal-add">
    <div class="modal">
        <div class="modal-title">➕ Ajouter un utilisateur</div>
        <form method="POST">
            <input type="hidden" name="action" value="ajouter">
            <div class="form-row">
                <div class="form-group"><label>Nom</label><input type="text" name="nom" required></div>
                <div class="form-group"><label>Prénom</label><input type="text" name="prenom" required></div>
            </div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group">
                <label>Rôle</label>
                <select name="role">
                    <option value="vendeur">Vendeur</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <p style="font-size:0.78rem;color:#888;">Mot de passe par défaut : <strong>vendeur123</strong></p>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-add').classList.remove('active')">Annuler</button>
                <button type="submit" class="btn btn-success">✅ Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
