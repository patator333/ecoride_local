<?php
require_once ROOT_PATH . '/config/config.php';

function reserverCovoiturage($id_utilisateur, $id_covoiturage) {
    global $pdo;

    try {
        $pdo->beginTransaction();

        // Déjà inscrit ?
        $check = $pdo->prepare("SELECT 1 FROM reservation WHERE id_utilisateur = :id_utilisateur AND id_covoiturage = :id_covoiturage");
        $check->execute([':id_utilisateur'=>$id_utilisateur, ':id_covoiturage'=>$id_covoiturage]);
        if ($check->fetch()) {
            $pdo->rollBack();
            return ['success'=>false,'message'=>'Vous êtes déjà inscrit.'];
        }

        // Vérifier places et prix
        $stmt = $pdo->prepare("SELECT nombre_places, prix_par_personne FROM covoiturage WHERE id_covoiturage = :id");
        $stmt->execute([':id'=>$id_covoiturage]);
        $cov = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cov || $cov['nombre_places'] <= 0) {
            $pdo->rollBack();
            return ['success'=>false,'message'=>'Aucune place disponible.'];
        }

        // Vérifier crédit
        $stmt = $pdo->prepare("SELECT credit FROM compte WHERE id_utilisateur = :id");
        $stmt->execute([':id'=>$id_utilisateur]);
        $credit = $stmt->fetchColumn();
        if ($credit < $cov['prix_par_personne']) {
            $pdo->rollBack();
            return ['success'=>false,'message'=>'Crédit insuffisant.'];
        }

        // Insérer réservation
        $stmt = $pdo->prepare("INSERT INTO reservation (id_utilisateur, id_covoiturage) VALUES (:id_utilisateur, :id_covoiturage)");
        $stmt->execute([':id_utilisateur'=>$id_utilisateur, ':id_covoiturage'=>$id_covoiturage]);

        // Débiter crédit
        $stmt = $pdo->prepare("UPDATE compte SET credit = credit - :prix WHERE id_utilisateur = :id");
        $stmt->execute([':prix'=>$cov['prix_par_personne'], ':id'=>$id_utilisateur]);

        // Réduire places
        $stmt = $pdo->prepare("UPDATE covoiturage SET nombre_places = nombre_places - 1 WHERE id_covoiturage = :id");
        $stmt->execute([':id'=>$id_covoiturage]);

        $pdo->commit();
        return ['success'=>true,'message'=>'Réservation effectuée avec succès !'];

    } catch(Exception $e) {
        $pdo->rollBack();
        return ['success'=>false,'message'=>'Erreur : '.$e->getMessage()];
    }
}

function getReservationsByUtilisateur($id_utilisateur) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT r.id_reservation, c.id_covoiturage, c.lieu_depart, c.lieu_arrivee, 
               c.date_depart, c.heure_depart, c.prix_par_personne, c.nombre_places,
               v.marque, v.modele, u.nom AS nom_chauffeur, u.photo AS photo_chauffeur
        FROM reservation r
        JOIN covoiturage c ON r.id_covoiturage = c.id_covoiturage
        JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE r.id_utilisateur = :id_utilisateur
        ORDER BY c.date_depart ASC
    ");
    $stmt->execute([':id_utilisateur' => $id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
