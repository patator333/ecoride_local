<?php  // Controleur principale pour l'espace utilisateur

session_start();
require_once APP_PATH . '/modeles/utilisateur.php'; // récupérer créer les utilisateurs et mettre à jour les roles
require_once APP_PATH . '/modeles/vehicule.php'; // récupérer ajouter véhicules
require_once APP_PATH . '/modeles/gestion_covoiturage_utilisateur.php'; // récupérer créer des voyages, historique et covoiturage programmés

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=connexion"); // si non connecté renvoie vers la page de connexion
    exit;
}

$user_id = $_SESSION['user']['id_utilisateur']; // récupération de l'id utilisateur pour l'utiliser dans toutes les requetes

// Initialisation des messages
$message = '';
$vehicule_message = '';
$voyage_message = '';
$pref_message = '';

// Récupérer les informations de l'utilisateur
$user = getUtilisateurById($user_id); // récupère toutes les informations de l'utilisateur

$vehicules = ($user['id_role'] != 'passager') ? getVehiculesByUtilisateur($user_id) : [];// si l'utilisateur peut conduire on récupère ses véhicules

$preferences = getPreferencesByUtilisateur($user_id);  // récupération de ses préférences

// 🔹 Gestion du formulaire rôle
if (isset($_POST['role'])) {
    $role = $_POST['role'];
    $message = updateRoleUtilisateur($user_id, $role); // si l'utilisateur a soumis le formulaire de role, mise à jour de la BDD
    $user['id_role'] = $role; // mise à jour de la vue
}

// 🔹 Gestion du formulaire nouveau véhicule
if (isset($_POST['ajouter_vehicule'])) {
    $vehicule_message = ajouterVehicule($user_id, $_POST); // si l'utlisateur a soumis le formulaire d'ajout de véhicule on appel la fonction d'ajout de véhicule
    $vehicules = getVehiculesByUtilisateur($user_id); // chargement de la vue
}

// 🔹 Gestion du formulaire préférences
if (isset($_POST['valider_preferences'])) {
    $pref_message = updatePreferences($user_id, $_POST); 
    $preferences = getPreferencesByUtilisateur($user_id); // traitement du formulaire de préférence et chargement des données pour la vue
}

// 🔹 Gestion du formulaire nouveau voyage
if (isset($_POST['creer_voyage'])) {
    $voyage_message = creerVoyage($user_id, $_POST); // idem mais pour le voyage
}

// Récupérer l'historique des covoiturages réalisés
$historique = getHistoriqueCovoiturages($user_id);

// Récupérer les covoiturages programmés
$covoiturages_programmes = getCovoituragesProgrammes($user_id);

// Inclure la vue
include APP_PATH . '/vues/espace_utilisateur.php';
