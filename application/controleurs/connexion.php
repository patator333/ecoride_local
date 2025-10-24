<?php
require_once APP_PATH . '/modeles/utilisateur.php';
require_once ROOT_PATH . '/config/config.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$email = '';

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        // Vérifier les identifiants
        $utilisateur = verifierConnexion($email, $password);

        if ($utilisateur) {
            // Stocker les infos de l'utilisateur en session
            $_SESSION['user'] = $utilisateur;

            // Redirection selon le type de compte
            switch ($utilisateur['id_type_compte']) {
                case 1: // Utilisateur classique
                    header("Location: index.php?page=espace_utilisateur");
                    break;
                case 2: // Employé
                    header("Location: index.php?page=espace_employe");
                    break;
                case 3: // Administrateur
                    header("Location: index.php?page=espace_administrateur");
                    break;
                default:
                    session_destroy();
                    header("Location: index.php?page=connexion");
                    break;
            }
            exit;
        } else {
            $message = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } else {
        $message = "Merci de remplir tous les champs.";
    }
}

// Inclure la vue de connexion
include APP_PATH . '/vues/connexion.php';
