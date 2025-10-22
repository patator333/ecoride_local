<?php
require_once ROOT_PATH . '/config/config.php';

function enregistrerContact($nom, $email, $commentaire) {
    global $pdo; // utilisation de la variable $pdo qui existe à l'extérieur

    try {
        $sql = "INSERT INTO contact (nom, email, commentaire)
                VALUES (:nom, :email, :commentaire)";    // insertion des données avec les placeholders correspondants
        $stmt = $pdo->prepare($sql);  // préparation de la requête
        $stmt->execute([       // protection contre injection SQL
            ':nom' => $nom,
            ':email' => $email,                         
            ':commentaire' => $commentaire
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Erreur contact: " . $e->getMessage());
        return false;
    }
}
 