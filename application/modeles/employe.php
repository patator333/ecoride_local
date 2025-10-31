<?php
require_once ROOT_PATH . '/config/config.php';

// Avis passager non validés
function getAvisNonValide() {
    global $pdo;

    $sql = "
        SELECT 
            a.id_avis,
            a.date_avis,
            a.commentaire,
            a.id_covoiturage,
            c.lieu_depart,
            c.lieu_arrivee,
            c.date_depart,
            u.nom AS auteur_avis,
            n.valeur1, n.valeur2, n.valeur3, n.valeur4, n.valeur5
        FROM avis a
        INNER JOIN compte u ON a.id_utilisateur = u.id_utilisateur
        INNER JOIN covoiturage c ON a.id_covoiturage = c.id_covoiturage
        LEFT JOIN note n 
            ON n.id_covoiturage = a.id_covoiturage 
            AND n.id_utilisateur = a.id_utilisateur
        WHERE a.statut_validation = 0
        ORDER BY a.date_avis DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Covoiturages mal passés (moyenne note < 3 ou avis négatif non validé)
function getCovoituragesProbleme() {
    global $pdo;

    $sql = "
        SELECT DISTINCT c.id_covoiturage,
               u.nom AS nom_chauffeur,
               u.mail AS email_chauffeur,
               c.lieu_depart,
               c.lieu_arrivee,
               c.date_depart,
               c.heure_depart,
               c.date_arrivee,
               c.heure_arrivee
        FROM covoiturage c
        JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        JOIN avis a ON a.id_covoiturage = c.id_covoiturage
        LEFT JOIN note n ON n.id_covoiturage = c.id_covoiturage AND n.id_utilisateur = a.id_utilisateur
        WHERE (a.commentaire IS NOT NULL AND a.statut_validation = 0)
           OR ((COALESCE(n.valeur1,0) + COALESCE(n.valeur2,0) + COALESCE(n.valeur3,0) + COALESCE(n.valeur4,0) + COALESCE(n.valeur5,0))/5) < 3
    ";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Publier ou refuser un avis
function traiterAvis($id_avis, $action) {
    global $pdo;
    $valide = $action === 'publier' ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE avis SET statut_validation = :valide WHERE id_avis = :id");
    return $stmt->execute([':valide' => $valide, ':id' => $id_avis]);
}
