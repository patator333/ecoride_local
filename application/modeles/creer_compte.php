<?php
require_once ROOT_PATH . '/config/config.php'; // Connexion BDD

function creerCompte($nom, $mail, $mot_de_passe) {
    global $pdo;

    // Vérifier si le mail existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM compte WHERE mail = :mail");
    $stmt->execute([':mail' => $mail]);
    if ($stmt->fetchColumn() > 0) {
        return ['success' => false, 'message' => "Ce mail est déjà utilisé."];
    }

    // Hachage du mot de passe
    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Insertion du compte
    $stmt = $pdo->prepare("INSERT INTO compte (nom, mail, password, credit, date_creation) 
                           VALUES (:nom, :mail, :password, 20, NOW())");
    $ok = $stmt->execute([
        ':nom' => $nom,
        ':mail' => $mail,
        ':password' => $hash
    ]);

    if ($ok) {
        return ['success' => true, 'message' => "Compte créé avec succès !"]; // si l'insertion en bdd est validée
    } else {
        return ['success' => false, 'message' => "Erreur lors de la création du compte."];
    }
}
