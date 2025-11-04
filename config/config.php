<?php

define('ROOT_PATH', dirname(__DIR__));        // __DIR__ chemin absolu du dossier contenant le fichier courant C:\wamp64\www\ecoride\config
                                              // dirname équivalent à .. (remonte d'un cran)

define('APP_PATH', ROOT_PATH . '/application'); // équivalent à c:\wamp64\www\ecoride/application

define('PUBLIC_PATH', ROOT_PATH . '/public');   // équivalent à C:\wamp64\www\ecoride/public

define('PUBLIC_URL', '/Ecoride/public');      // redirige le localhost vers le dossier public pour qu'il tape dans index.php


try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=ecoride;charset=utf8mb4',
        'root',
        'feat981BUFF898!'           
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la BDD : " . $e->getMessage()); 
}
 
