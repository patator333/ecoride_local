<?php

require_once APP_PATH . '/modeles/employe.php';
require_once ROOT_PATH . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier que l'utilisateur est bien un employé
if (empty($_SESSION['user']) || $_SESSION['user']['id_type_compte'] != 2) {
    header("Location: " . PUBLIC_URL . "/?page=connexion");
    exit;
}

// Si on valide ou refuse un avis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_avis'], $_POST['action'])) {
    $id_avis = (int) $_POST['id_avis'];
    $action = $_POST['action'] === 'valider' ? 'publier' : 'refuser';
    
    if (traiterAvis($id_avis, $action)) {
        $_SESSION['message'] = $action === 'publier' 
            ? "Avis publié avec succès."
            : "Avis refusé.";
    } else {
        $_SESSION['message'] = "Une erreur est survenue lors de la mise à jour.";
    }

    // Redirection propre pour éviter un double envoi de requête
    header("Location: " . PUBLIC_URL . "/?page=espace_employe");
    exit;
}

// Récupérer les avis et covoiturages problématiques
$avisNonValides = getAvisNonValide();
$covoituragesProbleme = getCovoituragesProbleme();

// Appeler la vue
include APP_PATH . '/vues/espace_employe.php';
