<?php
// Fichier : /controleurs/espace_employe.php
require_once APP_PATH . '/modeles/employe.php';
require_once ROOT_PATH . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier que l'utilisateur est employé
if (empty($_SESSION['user']) || $_SESSION['user']['id_type_compte'] != 2) {
    header("Location: " . PUBLIC_URL . "/?page=connexion");
    exit;
}

// Récupérer les avis et covoiturages problématiques
$avis_non_valide = getAvisNonValide();
$covoiturages_probleme = getCovoituragesProbleme();

// Appeler la vue
include APP_PATH . '/vues/espace_employe.php';
 