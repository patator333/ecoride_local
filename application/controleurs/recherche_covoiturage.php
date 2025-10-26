<?php
require_once APP_PATH . '/modeles/covoiturage.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les filtres depuis GET
$ville_depart  = trim($_GET['ville_depart'] ?? '');
$ville_arrivee = trim($_GET['ville_arrivee'] ?? '');
$date_depart   = $_GET['date_depart'] ?? '';
$page_num      = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
$limit         = 5;
$offset        = ($page_num - 1) * $limit;
$total_pages   = 0;
$covoiturages  = [];

// Sauvegarder les derniers filtres dans la session
if ($ville_depart && $ville_arrivee && $date_depart) {
    $_SESSION['dernieres_recherches'] = [
        'ville_depart' => $ville_depart,
        'ville_arrivee' => $ville_arrivee,
        'date_depart' => $date_depart
    ];

    $filtres = [
        'electrique' => $_GET['electrique'] ?? null,
        'prix_max'   => $_GET['prix_max'] ?? null,
        'duree_max'  => $_GET['duree_max'] ?? null,
        'note_min'   => $_GET['note_min'] ?? null
    ];

    $total_results = compterCovoiturages($ville_depart, $ville_arrivee, $date_depart, $filtres);
    $total_pages = ceil($total_results / $limit);
    $covoiturages = rechercherCovoiturages($ville_depart, $ville_arrivee, $date_depart, $filtres, $limit, $offset);
}

// Récupérer message depuis session
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

include APP_PATH . '/vues/recherche_covoiturage.php';
