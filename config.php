<?php
// config.php - Configuration de la base de données AMDY'S
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'amdys_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die("<div style='font-family:sans-serif;color:red;padding:20px;'>
        <h2>Erreur de connexion à la base de données</h2>
        <p>" . $conn->connect_error . "</p>
        <p>Vérifiez que MySQL est démarré et que la base <strong>amdys_db</strong> existe.</p>
    </div>");
}

// Démarrer la session si pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonctions utilitaires
function isLoggedIn() {
    return isset($_SESSION['user_id']) || isset($_SESSION['client_id']);
}

function isVendeur() {
    return isset($_SESSION['user_id']) && $_SESSION['role'] !== 'client';
}

function isClient() {
    return isset($_SESSION['client_id']);
}

function requireVendeur() {
    if (!isVendeur()) {
        header('Location: index.php?error=acces_refuse');
        exit();
    }
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?error=non_connecte');
        exit();
    }
}

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(htmlspecialchars(trim($data)));
}
?>
