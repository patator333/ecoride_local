<?php
require_once APP_PATH . '/modeles/contact.php'; // inclus le fichier contact.php contenant la fonction enregistrerContact() qui effectue
// l'insertion en base de données
 
$message = ''; // initialisation en string de $message
// servira à transmettre un retour à la vue contact.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // vérifie si la requête http est une soumission POST donc s'exécutera à la soumission du formulaire
    $nom = trim($_POST['nom'] ?? '');  
    $email = trim($_POST['email'] ?? '');                   // par sécurité si php ne récupère pas de valeur pour nom mail et commentaires
                                                            // affectation d'une variable vide
    $commentaire = trim($_POST['commentaire'] ?? '');

                                                            // affecte à la variable $nom la valeur envoyée par POST en supprimant les espaces
                                                            // inutile en début et fin de chaîne

    if ($nom && $email && $commentaire) {                            // vérifie que tous les champs sont non vides
        $resultat = enregistrerContact($nom, $email, $commentaire);  // appel la fonction définit dans modeles/contact.php 
        $message = $resultat ? 
            "Votre message a bien été envoyé. Merci !" : 
            "Une erreur est survenue. Réessayez.";
    } else {
        $message = "Merci de remplir tous les champs.";
    }
}

include APP_PATH . '/vues/entete.php';            // inclus par défaut la vue entete.php dans toutes les pages
include APP_PATH . '/vues/contact.php';           // inclus la vue contact.php
include APP_PATH . '/vues/pied_de_page.php';      // iinclus par défaut la vue pied_de_page.php dans toutes les pages
 