<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once APP_PATH . '/modeles/utilisateur.php';
require_once APP_PATH . '/modeles/covoiturage.php';
require_once APP_PATH . '/modeles/avis.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php?page=connexion');
    exit;
}

$user_id = (int) ($_SESSION['user']['id_utilisateur'] ?? 0);
$id_covoiturage = (int) ($_GET['id_covoiturage'] ?? 0);

if ($id_covoiturage <= 0 || !isCovoiturageTermine($id_covoiturage)) {
    header('Location: index.php?page=espace_utilisateur');
    exit;
}

// POST : soumission avis
$message_avis = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = (int) ($_POST['note'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($note >= 1 && $note <= 5 && $message !== '') {
        $ok = soumettreAvis($user_id, $id_covoiturage, $message, $note);
        $message_avis = $ok ? "Avis ajouté avec succès !" : "Erreur lors de l'ajout de l'avis.";
    } else {
        $message_avis = "Veuillez saisir un message et une note valide (1-5).";
    }
}

// Récupérer les avis existants pour affichage
$avis_list = getAvisByCovoiturage($id_covoiturage);

// Vue
include APP_PATH . '/vues/espace_avis.php';
