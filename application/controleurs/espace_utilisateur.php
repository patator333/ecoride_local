<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once APP_PATH . '/modeles/utilisateur.php';
require_once APP_PATH . '/modeles/vehicule.php';
require_once APP_PATH . '/modeles/reservation.php';
require_once APP_PATH . '/modeles/covoiturage.php';

// Redirection si non connecté
if (!isset($_SESSION['user'])) {
    header('Location: index.php?page=connexion');
    exit;
}

$user_id = (int) ($_SESSION['user']['id_utilisateur'] ?? 0);
if ($user_id <= 0) {
    header('Location: index.php?page=connexion');
    exit;
}

// Récupérer données utilisateur
$user = getUtilisateurById($user_id);
$vehicules = getVehiculesByUtilisateur($user_id);
$preferences = getPreferencesByUtilisateur($user_id);

// Messages
$message = $vehicule_message = $voyage_message = $pref_message = '';

// POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['role'])) {
        $role = intval($_POST['role']);
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
}

// Historique
$historique = getHistoriqueReservationsByUtilisateur($user_id);

// Covoiturages programmés
$covoiturages_programmes = getCovoituragesPourUtilisateur($user_id);

// Tri par date/heure
usort($covoiturages_programmes, function($a, $b) {
    $da = $a['date_depart'] . ' ' . ($a['heure_depart'] ?? '00:00:00');
    $db = $b['date_depart'] . ' ' . ($b['heure_depart'] ?? '00:00:00');
    return strcmp($da, $db);
});

// Vue
include APP_PATH . '/vues/espace_utilisateur.php';
