<?php
require_once ROOT_PATH . '/config/config.php';

/**
 * Récupérer tous les véhicules d'un utilisateur
 */
function getVehiculesByUtilisateur($id_utilisateur) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT v.*, tm.nom_type AS motorisation
        FROM vehicule v
        JOIN type_motorisation tm ON v.id_type_motorisation = tm.id_type_motorisation
        WHERE v.id_utilisateur = :id_utilisateur
    ");
    $stmt->execute([':id_utilisateur' => $id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Ajouter un véhicule pour un utilisateur
 */
function ajouterVehicule($id_utilisateur, $data) {
    global $pdo;

    // Champs obligatoires
    $required = ['immatriculation', 'date_de_premiere_immatriculation', 'marque', 'modele', 'places_disponibles', 'id_type_motorisation'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return "Le champ '$field' est obligatoire.";
        }
    }

    // Normaliser la date pour MySQL (YYYY-MM-DD)
    $date = date('Y-m-d', strtotime($data['date_de_premiere_immatriculation']));
    if (!$date) {
        return "Format de date invalide pour 'date_de_premiere_immatriculation'.";
    }
 
    try {
        $stmt = $pdo->prepare("
            INSERT INTO vehicule
            (immatriculation, date_de_premiere_immatriculation, marque, modele, couleur, places_disponibles, id_utilisateur, id_type_motorisation)
            VALUES
            (:immatriculation, :date_de_premiere_immatriculation, :marque, :modele, :couleur, :places_disponibles, :id_utilisateur, :id_type_motorisation)
        ");

        $stmt->execute([
            ':immatriculation' => strtoupper(trim($data['immatriculation'])),
            ':date_de_premiere_immatriculation' => $date,
            ':marque' => trim($data['marque']),
            ':modele' => trim($data['modele']),
            ':couleur' => trim($data['couleur'] ?? ''),
            ':places_disponibles' => (int)$data['places_disponibles'],
            ':id_utilisateur' => $id_utilisateur,
            ':id_type_motorisation' => (int)$data['id_type_motorisation']
        ]);

        return "Véhicule ajouté avec succès !";

    } catch (Exception $e) {
        return "Erreur lors de l'ajout du véhicule : " . $e->getMessage();
    }
}
