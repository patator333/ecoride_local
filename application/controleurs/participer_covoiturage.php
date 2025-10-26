<?php
require_once APP_PATH . '/modeles/reservation.php';
require_once APP_PATH . '/modeles/covoiturage.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: ?page=connexion');
    exit;
}

$id_utilisateur = $_SESSION['user']['id_utilisateur'];
$id_covoiturage = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_covoiturage) {
    $_SESSION['message'] = ['type'=>'danger', 'texte'=>"Covoiturage non spécifié."];
    header('Location: ?page=recherche_covoiturage');
    exit;
}

// Récupérer les informations du covoiturage
$cov = getCovoiturageById($id_covoiturage);
if (!$cov) {
    $_SESSION['message'] = ['type'=>'danger', 'texte'=>"Covoiturage introuvable."];
    header('Location: ?page=recherche_covoiturage');
    exit;
}

// Vérifier places disponibles
if (($cov['nombre_places'] ?? 0) <= 0) {
    $_SESSION['message'] = ['type'=>'warning', 'texte'=>"Plus de places disponibles."];
    header('Location: ?page=recherche_covoiturage');
    exit;
}

// Vérifier crédit utilisateur
$prix = $cov['prix_par_personne'] ?? 0;
if (($_SESSION['user']['credit'] ?? 0) < $prix) {
    $_SESSION['message'] = ['type'=>'warning', 'texte'=>"Crédit insuffisant."];
    header('Location: ?page=recherche_covoiturage');
    exit;
}

// Appel du modèle pour créer la réservation
$result = reserverCovoiturage($id_utilisateur, $id_covoiturage);

// Débiter le crédit si succès
if ($result['success']) {
    $_SESSION['user']['credit'] -= $prix;
    $_SESSION['message'] = ['type'=>'success', 'texte'=>$result['message'] ?? "Réservation réussie."];
} else {
    $_SESSION['message'] = ['type'=>'danger', 'texte'=>$result['message'] ?? "Erreur lors de la réservation."];
}

// Redirection vers l'espace utilisateur
header("Location: ?page=espace_utilisateur");
exit;
