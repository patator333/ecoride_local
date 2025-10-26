<?php
// controleur/participer_covoiturage.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// charger les modèles (adapter les chemins si besoin)
require_once APP_PATH . '/modeles/reservation.php';
require_once APP_PATH . '/modeles/covoiturage.php';

// vérifier connexion
if (!isset($_SESSION['user'])) {
    header('Location: ?page=connexion');
    exit;
}

$id_utilisateur = (int) ($_SESSION['user']['id_utilisateur'] ?? 0);
$id_covoiturage = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_utilisateur <= 0) {
    $_SESSION['message'] = ['type' => 'danger', 'texte' => 'Utilisateur non authentifié.'];
    header('Location: ?page=recherche_covoiturage');
    exit;
}

if ($id_covoiturage <= 0) {
    $_SESSION['message'] = ['type' => 'danger', 'texte' => 'Covoiturage non spécifié.'];
    header('Location: ?page=recherche_covoiturage');
    exit;
}

// Récupérer covoiturage (vérification supplémentaire)
$cov = getCovoiturageById($id_covoiturage);
if (!$cov) {
    $_SESSION['message'] = ['type' => 'danger', 'texte' => 'Covoiturage introuvable.'];
    header('Location: ?page=recherche_covoiturage');
    exit;
}

// Appel du modèle pour réserver
$result = reserverCovoiturage($id_utilisateur, $id_covoiturage);

if ($result['success']) {
    // Mettre à jour le crédit en session si le prix est disponible
    $prix = isset($cov['prix_par_personne']) ? (float)$cov['prix_par_personne'] : 0;
    if ($prix > 0 && isset($_SESSION['user']['credit'])) {
        $_SESSION['user']['credit'] = max(0, $_SESSION['user']['credit'] - $prix);
    }
    $_SESSION['message'] = ['type' => 'success', 'texte' => $result['message']];
} else {
    $_SESSION['message'] = ['type' => 'danger', 'texte' => $result['message']];
}

// Rediriger vers l'espace utilisateur (contexte plus logique)
header('Location: ?page=espace_utilisateur');
exit;
