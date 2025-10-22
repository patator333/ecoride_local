<?php
require_once APP_PATH . '/modeles/creer_compte.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // récupération du formulaire rempli pour la création de compte
    $nom = trim($_POST['nom'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if ($nom && $mail && $mot_de_passe) {  // si tous les champs de formulaire sont rempli -> appel de la fonction créer compte dans le modèle
        $result = creerCompte($nom, $mail, $mot_de_passe);
        $message = $result['message'];

        if ($result['success']) {
            header("Location: index.php?page=connexion&message=compte_cree"); // redirection de l'utilisateur sur la page créer compte si succès
            exit;
        }
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}

include APP_PATH . '/vues/creer_compte.php';
