<?php
require_once APP_PATH . '/modeles/avis.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Vérifier que c'est un employé
if (empty($_SESSION['user']) || $_SESSION['user']['id_type_compte'] != 2) {
    $_SESSION['message'] = "Accès réservé aux employés.";
    header("Location: ?page=connexion");
    exit;
}

$id_avis = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id_avis > 0 && in_array($action, ['publier', 'refuser'])) {
    $statut = $action === 'publier' ? 'validé' : 'rejeté';
    changerStatutAvis($id_avis, $statut);
}

header("Location: ?page=avis_en_attente");
exit;
