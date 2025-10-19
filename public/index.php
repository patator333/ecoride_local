<?php

require_once __DIR__ . '/../config/config.php'; // charge le fichier de configuration général

// __DIR__ constante magique qui renvoie le chemin absolu du dossier où se trouve le fichier en cours public/
// équivalent à écrire require_once /ecoride/config/config.php

// Page demandée
$page = $_GET['page'] ?? 'accueil';

// $_GET['page']    récupère la valeur du paramètre page dans l'url
// ?? si non défini affecte à $page la valeur accueil

// Route simple
switch ($page) {
    case 'accueil':
        include APP_PATH . '/controleurs/accueil.php'; // constante définie dans config.php qui indique le chemin /application
        break;

    case 'contact':
        include APP_PATH . '/controleurs/contact.php'; // si l'url contient contact renvoie vers le controlleur contact.php
        break;

    default:
        echo "Erreur 404 - Page non trouvée";
        break;
}
