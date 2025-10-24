<?php
session_start();          // Démarre la session
session_unset();          // Supprime toutes les variables de session
session_destroy();        // Détruit la session

// Redirige vers la page d'accueil ou de connexion
header("Location: index.php?page=connexion");
exit;
