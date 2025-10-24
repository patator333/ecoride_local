<?php

// Vérifier si l'utilisateur est connecté
$est_connecte = !empty($_SESSION['user']);

require_once APP_PATH . '/modeles/gestion_covoiturage_utilisateur.php';
require_once ROOT_PATH . '/config/config.php';

// La session est déjà démarrée dans index.php
if (empty($_SESSION['user'])) {
    header("Location: " . PUBLIC_URL . "/?page=connexion");
    exit;
}

$id_utilisateur = $_SESSION['user']['id_utilisateur'] ?? 0;
$id_covoiturage = (int)($_GET['id'] ?? 0);

if (!$id_covoiturage) {
    die("Covoiturage invalide.");
}

$cov = getCovoiturageById($id_covoiturage);
if (!$cov) {
    die("Covoiturage introuvable.");
}

$places_ok = ($cov['nombre_places'] ?? 0) > 0;
$credit_ok = ($_SESSION['user']['credit'] ?? 0) >= ($cov['prix_par_personne'] ?? 0);

if (!$places_ok) {
    die("Plus de places disponibles.");
}
if (!$credit_ok) {
    die("Crédit insuffisant.");
}

$result = participerCovoiturage($id_utilisateur, $id_covoiturage, $cov['prix_par_personne']);
if ($result === true) {
    $_SESSION['user']['credit'] -= $cov['prix_par_personne'];
    header("Location: " . PUBLIC_URL . "/?page=recherche_covoiturage&msg=réservé");
    exit;
} else {
    die($result);
}
