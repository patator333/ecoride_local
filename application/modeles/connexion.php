<?php
require_once ROOT_PATH . '/config/config.php';  // renvoie vers la connexion à la bdd

function verifierConnexion($email, $password) {
    global $pdo;  // rend la variable $pdo accessible dans la fonction

    $sql = "SELECT * FROM compte WHERE mail = :email LIMIT 1";  // préparation de la requête pour récupérer l'utilisateur en base de données
                                                                  // LIMIT 1 récupère qu'un seul utilisateur
    $stmt = $pdo->prepare($sql);

    $stmt->execute([':email' => $email]); // exécution de la requête préparée en remplaçant :usernam par la valeur réelle saisie dans le formulaire

    $user = $stmt->fetch(PDO::FETCH_ASSOC); // récupère une seule ligne du résultat de la requête
                                            // PDO::FETCH_ASSOC la ligne récupérée sera un tableau associatif

    if ($user && isset($user['password']) && $user['password'] === $password) { // si l'utilisateur existe et que le mot de passe concorde en bdd

        return $user;  // résultat de la fonction qui est transmise au controleur
    }

    return false;
}
 