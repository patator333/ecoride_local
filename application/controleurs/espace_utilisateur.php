<?php
session_start();
require_once APP_PATH . '/modeles/utilisateur.php';
require_once APP_PATH . '/modeles/vehicule.php';
require_once APP_PATH . '/modeles/reservation.php';
require_once APP_PATH . '/modeles/gestion_covoiturage_utilisateur.php';

if (!isset($_SESSION['user'])) { 
    header("Location: index.php?page=connexion");
    exit;
}

$user_id = $_SESSION['user']['id_utilisateur'];
$user = getUtilisateurById($user_id);
$vehicules = ($user['id_role'] != 'passager') ? getVehiculesByUtilisateur($user_id) : [];
$preferences = getPreferencesByUtilisateur($user_id);

$message = $vehicule_message = $voyage_message = $pref_message = '';
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message']['texte'];
    unset($_SESSION['message']);
}

// Formulaires
if (isset($_POST['role'])) {
    $role = $_POST['role'];
    $message = updateRoleUtilisateur($user_id, $role);
    $user['id_role'] = $role;
}
if (isset($_POST['ajouter_vehicule'])) {
    $vehicule_message = ajouterVehicule($user_id, $_POST);
    $vehicules = getVehiculesByUtilisateur($user_id);
}
if (isset($_POST['valider_preferences'])) {
    $pref_message = updatePreferences($user_id, $_POST);
    $preferences = getPreferencesByUtilisateur($user_id);
}
if (isset($_POST['creer_voyage'])) {
    $voyage_message = creerVoyage($user_id, $_POST);
}

// Historique
$historique = getHistoriqueCovoiturages($user_id);

// Covoiturages programmés (réservés et créés)
$covoiturages_programmes = array_merge(
    getReservationsByUtilisateur($user_id),
    getCovoituragesProgrammes($user_id) // fonction qui récupère les covoiturages créés par l'utilisateur
);

// Supprimer doublons
$covoiturages_programmes = array_unique($covoiturages_programmes, SORT_REGULAR);

include APP_PATH . '/vues/espace_utilisateur.php';
