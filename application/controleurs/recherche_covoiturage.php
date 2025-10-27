<?php
require_once APP_PATH . '/modeles/covoiturage.php';
require_once APP_PATH . '/modeles/utilisateur.php'; // pour getPreferencesByUtilisateur

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
        'ville_depart'  => $ville_depart,
        'ville_arrivee' => $ville_arrivee,
        'date_depart'   => $date_depart
    ];

    // Créer un tableau de critères à passer aux fonctions
    $criteres = [
        'ville_depart'  => $ville_depart,
        'ville_arrivee' => $ville_arrivee,
        'date_depart'   => $date_depart,
        'electrique'    => $_GET['electrique'] ?? null,
        'prix_max'      => $_GET['prix_max'] ?? null,
        'duree_max'     => $_GET['duree_max'] ?? null,
        'note_min'      => $_GET['note_min'] ?? null
    ];

    // Compter le nombre total de covoiturages correspondant aux critères
    $total_results = compterCovoiturages($criteres);
    $total_pages = ceil($total_results / $limit);

    // Récupérer les covoiturages pour cette page
    $covoiturages = rechercherCovoiturages($criteres, $limit, $offset);

    // 🔹 Récupérer les préférences du chauffeur pour chaque covoiturage
    foreach ($covoiturages as &$cov) {
        $id_chauffeur = $cov['id_utilisateur'] ?? 0;
        if ($id_chauffeur) {
            $prefs = getPreferencesByUtilisateur($id_chauffeur); // fonction du modèle utilisateur
            $cov['preferences'] = $prefs ?: ['fumeur'=>0,'animal'=>0,'remarques_particulieres'=>'-'];
        } else {
            $cov['preferences'] = ['fumeur'=>0,'animal'=>0,'remarques_particulieres'=>'-'];
        }
    }
    unset($cov); // briser la référence
}

// Récupérer message depuis session
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

// Inclure la vue
include APP_PATH . '/vues/recherche_covoiturage.php';
