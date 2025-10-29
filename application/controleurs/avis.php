<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Inclusion de la configuration PDO
require_once ROOT_PATH . '/config/config.php';

require_once APP_PATH . '/modeles/utilisateur.php';
require_once APP_PATH . '/modeles/covoiturage.php';
require_once APP_PATH . '/modeles/avis.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php?page=connexion');
    exit;
}

$user_id = (int) ($_SESSION['user']['id_utilisateur'] ?? 0);
$id_covoiturage = (int) ($_GET['id_covoiturage'] ?? 0);

// Message d'information
$message_avis = '';

// Vérifier si le covoiturage existe
$stmt = $pdo->prepare("SELECT * FROM covoiturage WHERE id_covoiturage = :id");
$stmt->execute(['id' => $id_covoiturage]);
$covoiturage = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$covoiturage) {
    $message_avis = "Covoiturage invalide.";
    $avis_list = [];
} else {
    // Vérifier si terminé
    if (!isCovoiturageTermine($id_covoiturage)) {
        $message_avis = "Ce covoiturage n'est pas encore terminé. L'ajout d'avis sera possible après la fin.";
    }

    // POST : soumission avis
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

    // Récupérer les avis existants
    $avis_list = getAvisByCovoiturage($id_covoiturage);
}

// Fonction pour vérifier si un covoiturage est terminé
function isCovoiturageTermine($id_covoiturage) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT date_depart, date_arrivee, heure_depart, heure_arrivee 
        FROM covoiturage 
        WHERE id_covoiturage = :id
    ");
    $stmt->execute(['id' => $id_covoiturage]);
    $covoiturage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$covoiturage) return false;

    $date_heure_fin = $covoiturage['date_arrivee'] . ' ' . ($covoiturage['heure_arrivee'] ?? '23:59:59');
    return time() >= strtotime($date_heure_fin);
}

// Vue
include APP_PATH . '/vues/espace_avis.php';
