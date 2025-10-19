<?php

require_once __DIR__ . '/../config/config.php';

// Page demandée
$page = $_GET['page'] ?? 'accueil';

// Route simple
switch ($page) {
    case 'accueil':
        include APP_PATH . '/controleurs/accueil.php';
        break;

    default:
        echo "Erreur 404 - Page non trouvée";
        break;
}
