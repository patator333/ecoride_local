<?php
require_once ROOT_PATH . '/config/config.php';

// Récupérer tous les véhicules d'un utilisateur avec le nom de la motorisation
function getVehiculesByUtilisateur($id_utilisateur) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT v.*, tm.nom_type AS motorisation
        FROM vehicule v
        LEFT JOIN type_motorisation tm ON v.id_type_motorisation = tm.id_type_motorisation
        WHERE v.id_utilisateur = :id
    ");
    $stmt->execute([':id' => $id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ajouter un nouveau véhicule avec normalisation des données
function ajouterVehicule($id_utilisateur, $data) {
    global $pdo;

    // Normalisation des données
    $immatriculation = strtoupper(trim($data['immatriculation']));
    $date_premiere_immatriculation = $data['date_premiere_immatriculation']; // format YYYY-MM-DD
    $marque = ucfirst(strtolower(trim($data['marque'])));
    $modele = ucfirst(strtolower(trim($data['modele'])));
    $couleur = ucfirst(strtolower(trim($data['couleur'])));
    $places_disponibles = (int)$data['places']; // convertir en entier
    $id_type_motorisation = (int)$data['motorisation']; // ID du type de motorisation

    // Préparer l'insertion
    $stmt = $pdo->prepare("
        INSERT INTO vehicule 
        (id_utilisateur, immatriculation, date_de_premiere_immatriculation, marque, modele, couleur, places_disponibles, id_type_motorisation) 
        VALUES 
        (:id_utilisateur, :immatriculation, :date_de_premiere_immatriculation, :marque, :modele, :couleur, :places_disponibles, :id_type_motorisation)
    ");

    $ok = $stmt->execute([
        ':id_utilisateur' => $id_utilisateur,
        ':immatriculation' => $immatriculation,
        ':date_de_premiere_immatriculation' => $date_premiere_immatriculation,
        ':marque' => $marque,
        ':modele' => $modele,
        ':couleur' => $couleur,
        ':places_disponibles' => $places_disponibles,
        ':id_type_motorisation' => $id_type_motorisation
    ]);

    return $ok ? "Véhicule ajouté avec succès." : "Erreur lors de l'ajout du véhicule.";
}
