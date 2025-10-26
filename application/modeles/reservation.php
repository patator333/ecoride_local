<?php
require_once ROOT_PATH . '/config/config.php'; // $pdo doit être défini

/**
 * Crée un nouveau voyage (covoiturage) et ajoute automatiquement
 * une réservation pour le chauffeur.
 */
/*function creerVoyage($id_utilisateur, $data) {
    global $pdo;

    try { 
        // Normaliser noms des villes
        $ville_depart = ucwords(strtolower(trim($data['ville_depart'])));
        $ville_arrivee = ucwords(strtolower(trim($data['ville_arrivee'])));

        // Conversion types
        $id_vehicule = (int)$data['id_vehicule'];
        $prix_par_personne = (float)$data['prix'];
        $date_depart = $data['date_depart'];
        $heure_depart = $data['heure_depart'];
        $date_arrivee = $data['date_arrivee'];
        $heure_arrivee = $data['heure_arrivee'];

        // INSERT covoiturage
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

        if (!$ok) return "Erreur lors de la création du voyage.";

        // ID du covoiturage créé
        $id_covoiturage = $pdo->lastInsertId();

        // Ajouter réservation pour le chauffeur
        $stmt2 = $pdo->prepare("INSERT INTO reservation (id_utilisateur, id_covoiturage) VALUES (:uid, :cid)");
        $stmt2->execute([':uid' => $id_utilisateur, ':cid' => $id_covoiturage]);

        return "Voyage créé avec succès !";

    } catch (Exception $e) {
        return "Erreur lors de la création du voyage : " . $e->getMessage();
    }
}
*/
/**
 * Récupère covoiturages programmés créés ou réservés par un utilisateur
 */
function getReservationsByUtilisateur($id_utilisateur) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT DISTINCT 
            c.id_covoiturage,
            c.lieu_depart,
            c.lieu_arrivee,
            c.date_depart,
            c.heure_depart,
            c.prix_par_personne,
            v.marque,
            v.modele,
            u.nom AS nom_chauffeur,
            u.photo AS photo_chauffeur
        FROM covoiturage c
        JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        LEFT JOIN reservation r ON c.id_covoiturage = r.id_covoiturage
        WHERE (r.id_utilisateur = :id_utilisateur OR c.id_utilisateur = :id_utilisateur)
          AND c.date_depart >= CURDATE()
        ORDER BY c.date_depart ASC
    ");
    $stmt->execute([':id_utilisateur' => $id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Historique des covoiturages passés
 */
function getHistoriqueReservationsByUtilisateur($id_utilisateur) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT r.id_reservation, c.id_covoiturage, c.lieu_depart, c.lieu_arrivee,
               c.date_depart, c.heure_depart, c.date_arrivee, c.heure_arrivee,
               c.prix_par_personne, v.marque, v.modele,
               u.nom AS nom_chauffeur, u.photo AS photo_chauffeur
        FROM reservation r
        JOIN covoiturage c ON r.id_covoiturage = c.id_covoiturage
        JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE r.id_utilisateur = :uid
          AND c.date_depart < CURDATE()
        ORDER BY c.date_depart DESC, c.heure_depart DESC
    ");
    $stmt->execute([':uid' => $id_utilisateur]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Réserver un covoiturage (passager)
 */
function reserverCovoiturage($id_utilisateur, $id_covoiturage) {
    global $pdo;

    try {
        $id_utilisateur = (int)$id_utilisateur;
        $id_covoiturage = (int)$id_covoiturage;
        if ($id_utilisateur <= 0 || $id_covoiturage <= 0) {
            return ['success' => false, 'message' => 'Paramètres invalides.'];
        }

        $pdo->beginTransaction();

        // Vérifier si déjà réservé
        $check = $pdo->prepare("SELECT 1 FROM reservation WHERE id_utilisateur = :uid AND id_covoiturage = :cid LIMIT 1");
        $check->execute([':uid' => $id_utilisateur, ':cid' => $id_covoiturage]);
        if ($check->fetch()) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Vous êtes déjà inscrit à ce covoiturage.'];
        }

        // Vérifier places et crédit
        $stmt = $pdo->prepare("SELECT nombre_places, prix_par_personne FROM covoiturage WHERE id_covoiturage = :cid FOR UPDATE");
        $stmt->execute([':cid' => $id_covoiturage]);
        $cov = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cov) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Covoiturage introuvable.'];
        }
        if ((int)$cov['nombre_places'] <= 0) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Plus de places disponibles.'];
        }

        $stmt = $pdo->prepare("SELECT credit FROM compte WHERE id_utilisateur = :uid FOR UPDATE");
        $stmt->execute([':uid' => $id_utilisateur]);
        $credit = $stmt->fetchColumn();
        $prix = (float)$cov['prix_par_personne'];
        if ($credit === false || (float)$credit < $prix) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Crédit insuffisant.'];
        }

        // Insérer réservation
        $ins = $pdo->prepare("INSERT INTO reservation (id_utilisateur, id_covoiturage) VALUES (:uid, :cid)");
        $ins->execute([':uid' => $id_utilisateur, ':cid' => $id_covoiturage]);

        // Débiter crédit et décrémenter places
        $pdo->prepare("UPDATE compte SET credit = credit - :prix WHERE id_utilisateur = :uid")
            ->execute([':prix' => $prix, ':uid' => $id_utilisateur]);
        $pdo->prepare("UPDATE covoiturage SET nombre_places = nombre_places - 1 WHERE id_covoiturage = :cid")
            ->execute([':cid' => $id_covoiturage]);

        $pdo->commit();
        return ['success' => true, 'message' => 'Réservation effectuée avec succès !'];

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return ['success' => false, 'message' => 'Erreur lors de la réservation : ' . $e->getMessage()];
    }
}
