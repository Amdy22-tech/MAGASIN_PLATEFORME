<?php
require_once 'config.php';
requireVendeur();

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='modifier') {
    $id    = (int)$_POST['article_id'];
    $nom   = sanitize($_POST['nom']);
    $desc  = sanitize($_POST['description']);
    $prix  = (float)$_POST['prix'];
    $stock = (int)$_POST['stock'];
    $cat   = sanitize($_POST['categorie']);
    $img   = sanitize($_POST['image_url']);
    $conn->query("UPDATE articles SET nom='$nom',description='$desc',prix=$prix,stock=$stock,categorie='$cat',image_url='$img' WHERE id=$id");
}
header('Location: articles.php');
exit();
