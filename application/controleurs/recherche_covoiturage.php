<?php
require_once APP_PATH . '/modeles/covoiturage.php'; // contient fonction compterCovoiturages() et rechercherCovoiturages()

$page_num = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1; // récupère le numéro de page dans l'url
$limit = 5;
$offset = ($page_num - 1) * $limit;

$covoiturages = [];
$total_pages = 0;

$ville_depart  = trim($_GET['ville_depart'] ?? '');
$ville_arrivee = trim($_GET['ville_arrivee'] ?? '');
$date_depart   = $_GET['date_depart'] ?? '';           // récupération des champs saisis par l'utilisateur

if (!empty($ville_depart) && !empty($ville_arrivee) && !empty($date_depart)) { // s'assurer que tous les champs sont remplis
    $filtres = [
        'electrique' => $_GET['electrique'] ?? null,
        'prix_max'   => $_GET['prix_max'] ?? null,
        'duree_max'  => $_GET['duree_max'] ?? null,
        'note_min'   => $_GET['note_min'] ?? null
    ];

    $total_results = compterCovoiturages($ville_depart, $ville_arrivee, $date_depart, $filtres);
    $total_pages = ceil($total_results / $limit);

    $covoiturages = rechercherCovoiturages($ville_depart, $ville_arrivee, $date_depart, $filtres, $limit, $offset);

    // Récupérer les préférences pour chaque covoiturage
    global $pdo;
    foreach ($covoiturages as &$cov) { // parcour tous les covoiturages retournés par rechercherCoiturages
        $stmt = $pdo->prepare("SELECT fumeur, animal, remarques_particulieres 
                               FROM preference 
                               WHERE id_utilisateur = :id_utilisateur");
        $stmt->execute([':id_utilisateur' => $cov['id_utilisateur']]);
        $prefs = $stmt->fetch(PDO::FETCH_ASSOC);
        $cov['preferences'] = $prefs ?: null;
    }
}

include APP_PATH . '/vues/recherche_covoiturage.php';  
