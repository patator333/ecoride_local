<?php
require_once APP_PATH . '/modeles/reservation.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
 
// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user'])) {
    $_SESSION['message'] = "Vous devez être connecté pour participer.";
    header("Location: ?page=connexion"); // redirection vers la page réelle de connexion
    exit;
}

// Récupérer l'ID utilisateur depuis le tableau de session
$id_utilisateur = (int)($_SESSION['user']['id_utilisateur'] ?? 0);
if ($id_utilisateur <= 0) {
    $_SESSION['message'] = "Utilisateur invalide.";
    header("Location: ?page=connexion");
    exit;
}

// Récupérer l'ID du covoiturage depuis l'URL
$id_covoiturage = (int)($_GET['id'] ?? 0);
if ($id_covoiturage <= 0) {
    $_SESSION['message'] = "Covoiturage invalide.";
    header("Location: ?page=recherche_covoiturage");
    exit;
}

// Appeler la fonction du modèle pour réserver le covoiturage
$result = reserverCovoiturage($id_utilisateur, $id_covoiturage);

// Stocker le message et rediriger vers l'espace utilisateur
$_SESSION['message'] = $result['message'];
header("Location: ?page=espace_utilisateur");
exit;
