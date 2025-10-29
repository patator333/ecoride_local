<?php
if (session_status() === PHP_SESSION_NONE) session_start();
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

$message_avis = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = isset($_POST['note']) && is_numeric($_POST['note']) ? (int) $_POST['note'] : 0;
    $commentaire = trim($_POST['commentaire'] ?? '');
    $bien_passe = isset($_POST['bien_passe']) ? 1 : 0;

    if ($note >= 1 && $note <= 5 && !empty($commentaire)) {
        // Insérer dans avis
        $stmt = $pdo->prepare("
            INSERT INTO avis (date_avis, statut_validation, commentaire, id_covoiturage, id_utilisateur)
            VALUES (NOW(), :statut, :commentaire, :cid, :uid)
        ");
        $ok = $stmt->execute([
            ':statut' => $bien_passe,
            ':commentaire' => $commentaire,
            ':cid' => $id_covoiturage,
            ':uid' => $user_id
        ]);

        // Optionnel : Insérer dans note
        $stmt2 = $pdo->prepare("
            INSERT INTO note (valeur1, id_covoiturage, id_utilisateur)
            VALUES (:note, :cid, :uid)
        ");
        $stmt2->execute([
            ':note' => $note,
            ':cid' => $id_covoiturage,
            ':uid' => $user_id
        ]);

        $message_avis = $ok ? "Avis ajouté avec succès !" : "Erreur lors de l'ajout de l'avis.";
    } else {
        $message_avis = "Veuillez saisir un commentaire et une note valide (1-5).";
    }
}

// Vue
include APP_PATH . '/vues/espace_avis.php';
