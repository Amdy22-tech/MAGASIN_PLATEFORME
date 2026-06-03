<?php
require_once 'config.php';

// Si déjà connecté, rediriger
if (isLoggedIn()) {
    header('Location: accueil.php');
    exit();
}

$error   = '';
$success = '';
$tab     = $_GET['tab'] ?? 'connexion'; // connexion | inscription

// ===== TRAITEMENT CONNEXION =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'connexion') {
        $email = sanitize($_POST['email']);
        $mdp   = $_POST['mot_de_passe'];
        $type  = sanitize($_POST['type_compte']); // vendeur | client

        if ($type === 'vendeur') {
            $res = $conn->query("SELECT * FROM users WHERE email='$email'");
            $user = $res->fetch_assoc();
            if ($user && password_verify($mdp, $user['mot_de_passe'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nom']     = $user['nom'];
                $_SESSION['prenom']  = $user['prenom'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];
                header('Location: accueil.php');
                exit();
            } else {
                $error = "Email ou mot de passe incorrect pour un vendeur.";
            }
        } else {
            $res = $conn->query("SELECT * FROM clients WHERE email='$email'");
            $client = $res->fetch_assoc();
            if ($client && password_verify($mdp, $client['mot_de_passe'])) {
                $_SESSION['client_id'] = $client['id'];
                $_SESSION['nom']       = $client['nom'];
                $_SESSION['prenom']    = $client['prenom'];
                $_SESSION['email']     = $client['email'];
                $_SESSION['role']      = 'client';
                header('Location: accueil.php');
                exit();
            } else {
                $error = "Email ou mot de passe incorrect pour un client.";
            }
        }
    }

    if ($_POST['action'] === 'inscription') {
        $nom       = sanitize($_POST['nom']);
        $prenom    = sanitize($_POST['prenom']);
        $email     = sanitize($_POST['email']);
        $tel       = sanitize($_POST['telephone'] ?? '');
        $adresse   = sanitize($_POST['adresse'] ?? '');
        $mdp       = $_POST['mot_de_passe'];
        $mdp2      = $_POST['confirmer_mdp'];
        $type_insc = sanitize($_POST['type_inscription']); // vendeur | client

        if ($mdp !== $mdp2) {
            $error = "Les mots de passe ne correspondent pas.";
            $tab = 'inscription';
        } elseif (strlen($mdp) < 6) {
            $error = "Le mot de passe doit contenir au moins 6 caractères.";
            $tab = 'inscription';
        } else {
            if ($type_insc === 'vendeur') {
                // Vérifier si le nom complet est dans la liste des vendeurs
                $check = $conn->query("SELECT * FROM users WHERE nom='$nom' AND prenom='$prenom'");
                if ($check->num_rows === 0) {
                    $error = "Vous n'êtes pas enregistré comme vendeur. Veuillez vous inscrire comme client.";
                    $tab = 'inscription';
                } else {
                    $user = $check->fetch_assoc();
                    // Mettre à jour le mot de passe et l'email si vide
                    $hash = password_hash($mdp, PASSWORD_DEFAULT);
                    $conn->query("UPDATE users SET email='$email', mot_de_passe='$hash' WHERE id={$user['id']}");
                    $success = "Compte vendeur activé avec succès ! Vous pouvez vous connecter.";
                    $tab = 'connexion';
                }
            } else {
                // Nouveau client
                $check = $conn->query("SELECT id FROM clients WHERE email='$email'");
                if ($check->num_rows > 0) {
                    $error = "Cet email est déjà utilisé.";
                    $tab = 'inscription';
                } else {
                    $hash = password_hash($mdp, PASSWORD_DEFAULT);
                    $conn->query("INSERT INTO clients (nom, prenom, email, telephone, adresse, mot_de_passe)
                                  VALUES ('$nom','$prenom','$email','$tel','$adresse','$hash')");
                    $success = "Inscription réussie ! Vous pouvez maintenant vous connecter en tant que client.";
                    $tab = 'connexion';
                }
            }
        }
    }
}

$err_param = $_GET['error'] ?? '';
if ($err_param === 'acces_refuse') $error = "⛔ Accès refusé. Cette section est réservée aux vendeurs.";
if ($err_param === 'non_connecte') $error = "🔐 Veuillez vous connecter pour accéder à cette page.";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMDY'S – Connexion / Inscription</title>
    <style>
        :root { --primary: #0a2540; --accent: #e63946; --gold: #f4a261; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0a2540 0%, #1a3a5c 50%, #0d1b2a 100%);
            display: flex; flex-direction: column;
        }

        /* HEADER */
        .site-header {
            padding: 20px 40px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 2px solid rgba(244,162,97,0.3);
        }
        .logo-box { display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .logo-circle {
            width: 75px; height: 75px; border-radius: 50%;
            border: 3px solid var(--gold);
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 10px; text-align: center;
            background: rgba(255,255,255,0.08); color: var(--gold); line-height: 1.3; padding: 6px;
        }
        .logo-label { font-size: 9px; color: rgba(255,255,255,0.6); text-align: center; max-width: 90px; }
        .site-title { text-align: center; }
        .site-title h1 {
            font-size: 3.5rem; font-weight: 900; letter-spacing: 6px;
            background: linear-gradient(90deg, #f4a261, #fff, #f4a261);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .site-title p { color: rgba(255,255,255,0.65); font-size: 0.85rem; letter-spacing: 3px; margin-top: 4px; }

        /* MAIN */
        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .auth-card {
            background: white; border-radius: 20px; width: 100%; max-width: 480px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4); overflow: hidden;
        }

        /* TABS */
        .tabs { display: flex; background: #f0f2f5; }
        .tab-btn {
            flex: 1; padding: 16px; border: none; background: transparent;
            font-size: 0.95rem; font-weight: 700; cursor: pointer;
            color: #666; transition: all 0.2s; letter-spacing: 0.5px;
        }
        .tab-btn.active { background: white; color: var(--primary); border-bottom: 3px solid var(--gold); }

        /* FORM */
        .form-panel { padding: 30px; display: none; }
        .form-panel.active { display: block; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 0.82rem; font-weight: 700; color: #444; margin-bottom: 6px; }
        .form-group input, .form-group select {
            width: 100%; padding: 10px 14px; border: 1.5px solid #ddd; border-radius: 8px;
            font-size: 0.9rem; transition: border-color 0.2s; color: #333;
        }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .type-selector {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;
        }
        .type-option {
            border: 2px solid #ddd; border-radius: 10px; padding: 12px;
            text-align: center; cursor: pointer; transition: all 0.2s;
        }
        .type-option input[type="radio"] { display: none; }
        .type-option.selected { border-color: var(--primary); background: #f0f4ff; }
        .type-option .type-icon { font-size: 1.8rem; display: block; margin-bottom: 4px; }
        .type-option .type-label { font-size: 0.8rem; font-weight: 700; color: #444; }
        .btn-submit {
            width: 100%; padding: 13px; background: var(--primary); color: white;
            border: none; border-radius: 10px; font-size: 1rem; font-weight: 700;
            cursor: pointer; transition: all 0.2s; margin-top: 8px; letter-spacing: 0.5px;
        }
        .btn-submit:hover { background: #1a3a5c; transform: translateY(-1px); }
        .alert { padding: 11px 14px; border-radius: 8px; margin-bottom: 16px; font-size: 0.85rem; }
        .alert-danger  { background: #fde8ea; color: #c0392b; border-left: 4px solid #e74c3c; }
        .alert-success { background: #eafaf1; color: #1e8449; border-left: 4px solid #2ecc71; }
        .info-box {
            background: #fff8f0; border: 1px solid #f4a261; border-radius: 8px;
            padding: 10px 14px; font-size: 0.78rem; color: #7a4a10; margin-top: 16px;
        }

        /* FOOTER */
        footer { text-align: center; padding: 16px; color: rgba(255,255,255,0.4); font-size: 0.78rem; }
        footer strong { color: var(--gold); }
    </style>
</head>
<body>

<header class="site-header">
    <div class="logo-box">
        <a><img src="logo_uac.jpg" height="50" alt="UAC"></a>
        <div class="logo-label">Université d'Abomey-Calavi</div>
    </div>
    <div class="site-title">
        <h1>AMDY'S</h1>
        <p>⚡ PLATEFORME DE VENTE INFORMATIQUE ⚡</p>
    </div>
    <div class="logo-box">
        <a><img src="logo_eneam.jpg" height="50" alt="ENEAM"></a>
        <div class="logo-label">École Nationale d'Éco. Appliquée et de Management</div>
    </div>
</header>

<main>
    <div class="auth-card">
        <div class="tabs">
            <button class="tab-btn <?= $tab==='connexion'?'active':'' ?>" onclick="showTab('connexion')">🔑 Connexion</button>
            <button class="tab-btn <?= $tab==='inscription'?'active':'' ?>" onclick="showTab('inscription')">📝 Inscription</button>
        </div>

        <!-- CONNEXION -->
        <div class="form-panel <?= $tab==='connexion'?'active':'' ?>" id="tab-connexion">
            <?php if ($error && $tab==='connexion'): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <p style="font-size:0.85rem;color:#666;margin-bottom:18px;">Bienvenue ! Connectez-vous à votre compte.</p>

            <form method="POST">
                <input type="hidden" name="action" value="connexion">
                <div class="form-group">
                    <label>Je suis :</label>
                    <div class="type-selector">
                        <label class="type-option" id="opt-client-cx">
                            <input type="radio" name="type_compte" value="client" checked onchange="toggleType('cx','client')">
                            <span class="type-icon">🙋</span>
                            <span class="type-label">Client</span>
                        </label>
                        <label class="type-option" id="opt-vendeur-cx">
                            <input type="radio" name="type_compte" value="vendeur" onchange="toggleType('cx','vendeur')">
                            <span class="type-icon">🏪</span>
                            <span class="type-label">Vendeur</span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Adresse email</label>
                    <input type="email" name="email" required placeholder="exemple@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-submit">🔓 Se connecter</button>
            </form>
            <div class="info-box">
                💡 <strong>Accès démo :</strong> Vendeurs : mot de passe <code>password</code> | Clients : mot de passe <code>password</code>
            </div>
        </div>

        <!-- INSCRIPTION -->
        <div class="form-panel <?= $tab==='inscription'?'active':'' ?>" id="tab-inscription">
            <?php if ($error && $tab==='inscription'): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <p style="font-size:0.85rem;color:#666;margin-bottom:18px;">Créez votre compte AMDY'S.</p>

            <form method="POST">
                <input type="hidden" name="action" value="inscription">
                <div class="form-group">
                    <label>Je souhaite m'inscrire en tant que :</label>
                    <div class="type-selector">
                        <label class="type-option selected" id="opt-client-ins">
                            <input type="radio" name="type_inscription" value="client" checked onchange="toggleType('ins','client')">
                            <span class="type-icon">🙋</span>
                            <span class="type-label">Nouveau Client</span>
                        </label>
                        <label class="type-option" id="opt-vendeur-ins">
                            <input type="radio" name="type_inscription" value="vendeur" onchange="toggleType('ins','vendeur')">
                            <span class="type-icon">🏪</span>
                            <span class="type-label">Vendeur existant</span>
                        </label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" required placeholder="KOSSOU">
                    </div>
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" required placeholder="Jean">
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="jean@email.com">
                </div>
                <div class="form-group client-only">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" placeholder="97xxxxxx">
                </div>
                <div class="form-group client-only">
                    <label>Adresse</label>
                    <input type="text" name="adresse" placeholder="Cotonou, Akpakpa">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="mot_de_passe" required placeholder="Min. 6 caractères">
                    </div>
                    <div class="form-group">
                        <label>Confirmer</label>
                        <input type="password" name="confirmer_mdp" required placeholder="Répéter">
                    </div>
                </div>
                <button type="submit" class="btn-submit">✅ S'inscrire</button>
            </form>
            <div class="info-box" id="vendeur-info" style="display:none;">
                ⚠️ <strong>Vendeurs :</strong> Votre nom et prénom doivent déjà être enregistrés dans le système par un administrateur.
            </div>
        </div>
    </div>
</main>

<footer>
    &copy; <?= date('Y') ?> <strong>AMDY'S</strong> – Projet <strong>ENEAM / UAC</strong>
</footer>

<script>
function showTab(t) {
    document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + t).classList.add('active');
    event.target.classList.add('active');
}
function toggleType(ctx, val) {
    // Mise en évidence
    document.querySelectorAll('[id^="opt-"][id$="-'+ctx+'"]').forEach(el => el.classList.remove('selected'));
    document.getElementById('opt-'+val+'-'+ctx).classList.add('selected');
    // Champs spéciaux inscription
    if (ctx === 'ins') {
        const clientOnly = document.querySelectorAll('.client-only');
        const info = document.getElementById('vendeur-info');
        clientOnly.forEach(el => el.style.display = val === 'client' ? 'block' : 'none');
        info.style.display = val === 'vendeur' ? 'block' : 'none';
    }
}
// Init: marquer client sélectionné
document.getElementById('opt-client-cx').classList.add('selected');
</script>
</body>
</html>
