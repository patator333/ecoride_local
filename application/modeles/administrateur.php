<?php

require_once ROOT_PATH . '/config/config.php';

// Créer un compte employé
function creerCompteEmploye($nom, $email, $password) {
    global $pdo;

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO compte (nom, mail, password, date_creation, id_type_compte, id_role) 
                           VALUES (:nom, :mail, :password, NOW(), 2, 1)");
    return $stmt->execute([
        ':nom' => $nom,
        ':mail' => $email,
        ':password' => $hash
    ]);
}

// Récupérer le nombre de covoiturages par jour sur les 7 derniers jours
function getCovoituragesDerniers7Jours() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT DATE(date_depart) AS jour, COUNT(*) AS nb_covoiturages
        FROM covoiturage
        WHERE date_depart >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(date_depart)
        ORDER BY DATE(date_depart)
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer le total des crédits gagnés par jour sur les 7 derniers jours
function getCreditsParJour() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT DATE(c.date_depart) AS jour, SUM(s.credit_gagne_chauffeur) AS total_credits
        FROM covoiturage c
        JOIN statistique s ON c.id_covoiturage = s.id_covoiturage
        WHERE c.date_depart >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(c.date_depart)
        ORDER BY DATE(c.date_depart)
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer tous les comptes
function getComptes() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT id_utilisateur, nom, mail, date_creation, id_type_compte, actif
        FROM compte
        ORDER BY date_creation DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Activer / suspendre un compte
function modifierStatutCompte($id_utilisateur, $actif) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE compte SET actif = :actif WHERE id_utilisateur = :id");
    return $stmt->execute([':actif' => $actif, ':id' => $id_utilisateur]);
}
