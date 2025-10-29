<?php
require_once APP_PATH . '/modeles/avis.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user'])) {
    $_SESSION['message'] = "Vous devez être connecté.";
    header("Location: ?page=connexion");
    exit;
}

$id_reservation = (int)($_GET['id_reservation'] ?? 0);
if ($id_reservation <= 0) {
    $_SESSION['message'] = "Réservation invalide.";
    header("Location: ?page=espace_utilisateur");
    exit;
}

include APP_PATH . '/vues/form_avis.php';
