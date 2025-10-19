<?php
require_once APP_PATH . '/modeles/connexion.php'; // inclu la fonction verifierConnexion()

session_start(); // pour gérer la session utilisateur

$message = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        $utilisateur = verifierConnexion($email, $password);  // $utilisateur contient le résultat de la fonction

        if ($utilisateur) {              // Si la fonction retourne un résultat cela signifie que les identifiants sont corrects
           
            $_SESSION['user'] = $utilisateur;  // Super variable globale qui perdure entre les pages tant que la session de l'utilisateur est active

                                        // ici stocke sur le serveur les identifiants de connexion
                                        // le serveur renvoi par la suite un cookie au navigateur

            header('Location: index.php?page=accueil'); // redirige l'utilisateur connecté à cette page

            exit;
        } else {
            $message = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } else {
        $message = "Merci de remplir tous les champs.";
    }
}

include APP_PATH . '/vues/connexion.php';
?>
