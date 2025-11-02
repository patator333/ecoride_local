<?php
require_once ROOT_PATH . '/config/config.php';
require_once APP_PATH . '/modeles/covoiturage.php';
require_once APP_PATH . '/modeles/reservation.php';
require_once APP_PATH . '/modeles/mail.php'; 

if (session_status() === PHP_SESSION_NONE) session_start();

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['user'])) {
    $_SESSION['message'] = "Vous devez être connecté.";
    header("Location: index.php?page=connexion");
    exit;
}

$id_utilisateur = (int)($_SESSION['user']['id_utilisateur'] ?? 0);
$id_covoiturage = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id_utilisateur <= 0 || $id_covoiturage <= 0) {
    $_SESSION['message'] = "Paramètres invalides.";
    header("Location: index.php?page=espace_utilisateur");
    exit;
}

// Vérifier que l'utilisateur est le chauffeur du covoiturage
$cov = getCovoiturageById($id_covoiturage);
if (!$cov || $cov['id_utilisateur'] != $id_utilisateur) {
    $_SESSION['message'] = "Vous n'avez pas le droit d'effectuer cette action.";
    header("Location: index.php?page=espace_utilisateur");
    exit;
} 

switch ($action) {
    case 'demarrer':
        changerStatutCovoiturage($id_covoiturage, 'en_cours');
        $_SESSION['message'] = "Covoiturage démarré.";
        break;

    case 'terminer':
        changerStatutCovoiturage($id_covoiturage, 'terminé');

        // Envoyer mail à tous les participants
        $participants = getParticipants($id_covoiturage);
        foreach ($participants as $p) {
            envoyerMail($p['mail'], $p['nom']); // utilise PHPMailer et le mail générique
        }

        $_SESSION['message'] = "Covoiturage terminé, mails envoyés (ou tentés).";
        break;

    case 'annuler':
        changerStatutCovoiturage($id_covoiturage, 'annulé');
        $_SESSION['message'] = "Covoiturage annulé.";
        break;

    default:
        $_SESSION['message'] = "Action inconnue.";
        break;
}

header("Location: index.php?page=espace_utilisateur");
exit;
