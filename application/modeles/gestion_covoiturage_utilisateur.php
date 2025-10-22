<?php
require_once ROOT_PATH . '/config/config.php';

// Créer un nouveau voyage avec normalisation des villes
function creerVoyage($id_utilisateur, $data) {
    global $pdo;

    // 🔹 Normaliser les noms de villes : supprimer espaces, mettre majuscule initiale
    $ville_depart = ucwords(strtolower(trim($data['ville_depart'])));
    $ville_arrivee = ucwords(strtolower(trim($data['ville_arrivee'])));

    // 🔹 Conversion types pour sécurité
    $id_vehicule = (int)$data['id_vehicule'];
    $prix_par_personne = (float)$data['prix'];
    $date_depart = $data['date_depart'];
    $heure_depart = $data['heure_depart'];
    $date_arrivee = $data['date_arrivee'];
    $heure_arrivee = $data['heure_arrivee'];

    // 🔹 Préparer et exécuter la requête
    $stmt = $pdo->prepare("
        INSERT INTO covoiturage 
        (id_utilisateur, id_vehicule, lieu_depart, lieu_arrivee, date_depart, heure_depart, date_arrivee, heure_arrivee, prix_par_personne)
        VALUES 
        (:id_utilisateur, :id_vehicule, :lieu_depart, :lieu_arrivee, :date_depart, :heure_depart, :date_arrivee, :heure_arrivee, :prix_par_personne)
    ");

    $ok = $stmt->execute([
        ':id_utilisateur' => $id_utilisateur,
        ':id_vehicule' => $id_vehicule,
        ':lieu_depart' => $ville_depart,
        ':lieu_arrivee' => $ville_arrivee,
        ':date_depart' => $date_depart,
        ':heure_depart' => $heure_depart,
        ':date_arrivee' => $date_arrivee,
        ':heure_arrivee' => $heure_arrivee,
        ':prix_par_personne' => $prix_par_personne
    ]);

    return $ok ? "Voyage créé avec succès." : "Erreur lors de la création du voyage.";
}

// Historique des covoiturages réalisés
function getHistoriqueCovoiturages($id_utilisateur) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT c.*, u.nom AS nom_chauffeur, v.marque, v.modele
        FROM covoiturage c
        JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        WHERE c.id_utilisateur = :id
        ORDER BY c.date_depart DESC
    ");
    $stmt->execute([':id' => $id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Covoiturages programmés (futurs)
function getCovoituragesProgrammes($id_utilisateur) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT c.*, u.nom AS nom_chauffeur, v.marque, v.modele
        FROM covoiturage c
        JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        WHERE c.id_utilisateur = :id AND c.date_depart >= CURDATE()
        ORDER BY c.date_depart ASC
    ");
    $stmt->execute([':id' => $id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
