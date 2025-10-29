<?php
require_once APP_PATH . '/modeles/avis.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user'])) {
    $_SESSION['message'] = "Vous devez être connecté.";
    header("Location: ?page=connexion");
    exit;
}

$id_reservation = (int)($_POST['id_reservation'] ?? 0);
$note = (int)($_POST['note'] ?? 0);
$commentaire = trim($_POST['commentaire'] ?? '');

if ($id_reservation <= 0 || $note < 1 || $note > 5) {
    $_SESSION['message'] = "Données invalides.";
    header("Location: ?page=espace_utilisateur");
    exit;
}

$result = ajouterAvis($id_reservation, $note, $commentaire);
$_SESSION['message'] = $result['message'];
header("Location: ?page=espace_utilisateur");
exit;
