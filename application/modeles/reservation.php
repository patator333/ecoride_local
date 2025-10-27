<?php
require_once ROOT_PATH . '/config/config.php';

/**
 * Crée un nouveau covoiturage et ajoute automatiquement
 * une réservation pour le chauffeur
 */
function creerVoyage(int $id_utilisateur, array $data): string {
    global $pdo;

    try {
        // Vérifier tous les champs
        $required = ['ville_depart','ville_arrivee','date_depart','heure_depart','date_arrivee','heure_arrivee','prix','id_vehicule'];
        foreach($required as $f) {
            if (!isset($data[$f]) || $data[$f] === '') {
                return "Erreur : le champ '$f' est obligatoire.";
            }
        }

        // Normalisation
        $ville_depart = ucwords(strtolower(trim($data['ville_depart'])));
        $ville_arrivee = ucwords(strtolower(trim($data['ville_arrivee'])));
        $id_vehicule = (int)$data['id_vehicule'];
        $prix_par_personne = (float)$data['prix'];
        $date_depart = $data['date_depart'];
        $heure_depart = $data['heure_depart'];
        $date_arrivee = $data['date_arrivee'];
        $heure_arrivee = $data['heure_arrivee'];

        // Nombre de places depuis véhicule
        $stmtVeh = $pdo->prepare("SELECT places_disponibles FROM vehicule WHERE id_vehicule = :vid AND id_utilisateur = :uid");
        $stmtVeh->execute([':vid'=>$id_vehicule, ':uid'=>$id_utilisateur]);
        $vehicule = $stmtVeh->fetch(PDO::FETCH_ASSOC);
        if (!$vehicule) return "Erreur : véhicule introuvable ou non attribué à l'utilisateur.";
        $nombre_places = (int)$vehicule['places_disponibles'];

        // INSERT covoiturage
        $stmt = $pdo->prepare("
            INSERT INTO covoiturage
            (id_utilisateur, id_vehicule, lieu_depart, lieu_arrivee, date_depart, heure_depart, date_arrivee, heure_arrivee, prix_par_personne, nombre_places)
            VALUES
            (:id_utilisateur, :id_vehicule, :lieu_depart, :lieu_arrivee, :date_depart, :heure_depart, :date_arrivee, :heure_arrivee, :prix_par_personne, :nombre_places)
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
            ':prix_par_personne' => $prix_par_personne,
            ':nombre_places' => $nombre_places
        ]);

        if (!$ok) {
            $err = $stmt->errorInfo();
            return "Erreur SQL covoiturage : " . implode(" | ", $err);
        }

        $id_covoiturage = $pdo->lastInsertId();

        // Ajouter réservation pour le chauffeur
        $stmt2 = $pdo->prepare("INSERT INTO reservation (id_utilisateur, id_covoiturage) VALUES (:uid, :cid)");
        $ok2 = $stmt2->execute([':uid'=>$id_utilisateur, ':cid'=>$id_covoiturage]);
        if (!$ok2) {
            $err2 = $stmt2->errorInfo();
            return "Erreur SQL réservation chauffeur : " . implode(" | ", $err2);
        }

        return "Covoiturage créé avec succès !";

    } catch (Exception $e) {
        return "Exception : " . $e->getMessage();
    }
}


/**
 * Récupère covoiturages programmés pour un utilisateur
 *//*
function getCovoituragesPourUtilisateur(int $id_utilisateur): array {
    global $pdo;

    // 1️ Covoiturages créés par l'utilisateur
    $stmt1 = $pdo->prepare("
        SELECT c.*, 
               COALESCE(s.statut, 'prévu') AS statut,
               v.marque, v.modele,
               u.nom AS nom_chauffeur,
               'Créateur' AS role
        FROM covoiturage c
        LEFT JOIN statut_covoiturage s ON c.id_covoiturage = s.id_covoiturage
        LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        LEFT JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE c.id_utilisateur = :id_utilisateur
          AND c.date_depart >= CURDATE()
    ");
    $stmt1->execute([':id_utilisateur' => $id_utilisateur]);
    $crees = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // 2️ Covoiturages auxquels l'utilisateur participe (pas créés par lui)
    $stmt2 = $pdo->prepare("
        SELECT c.*, 
               v.marque, v.modele,
               u.nom AS nom_chauffeur,
               'Participant' AS role,
               'prévu' AS statut
        FROM reservation r
        JOIN covoiturage c ON r.id_covoiturage = c.id_covoiturage
        LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        LEFT JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE r.id_utilisateur = :id_utilisateur
          AND c.id_utilisateur != :id_utilisateur
          AND c.date_depart >= CURDATE()
    ");
    $stmt2->execute([':id_utilisateur' => $id_utilisateur]);
    $participe = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $all = array_merge($crees, $participe);
    usort($all, function($a, $b){
        return strcmp(
            $a['date_depart'] . ' ' . ($a['heure_depart'] ?? '00:00:00'),
            $b['date_depart'] . ' ' . ($b['heure_depart'] ?? '00:00:00')
        );
    });

    return $all;
}*/

function getCovoituragesPourUtilisateur(int $id_utilisateur): array {
    global $pdo;

    // 1️ Covoiturages créés par l'utilisateur
    $stmt1 = $pdo->prepare("
        SELECT c.*, 
               COALESCE(s.statut, 'prévu') AS statut,
               v.marque, v.modele,
               u.nom AS nom_chauffeur,
               'Créateur' AS role
        FROM covoiturage c
        LEFT JOIN statut_covoiturage s ON c.id_covoiturage = s.id_covoiturage
        LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        LEFT JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        WHERE c.id_utilisateur = :id_utilisateur
    ");
    $stmt1->execute([':id_utilisateur' => $id_utilisateur]);
    $crees = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // 2️ Covoiturages auxquels l'utilisateur participe (y compris créés par lui)
    $stmt2 = $pdo->prepare("
        SELECT c.*, 
               v.marque, v.modele,
               u.nom AS nom_chauffeur,
               'Participant' AS role,
               COALESCE(s.statut, 'prévu') AS statut
        FROM reservation r
        JOIN covoiturage c ON r.id_covoiturage = c.id_covoiturage
        LEFT JOIN vehicule v ON c.id_vehicule = v.id_vehicule
        LEFT JOIN compte u ON c.id_utilisateur = u.id_utilisateur
        LEFT JOIN statut_covoiturage s ON c.id_covoiturage = s.id_covoiturage
        WHERE r.id_utilisateur = :id_utilisateur
    ");
    $stmt2->execute([':id_utilisateur' => $id_utilisateur]);
    $participe = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Fusionner et trier par date/heure
    $all = array_merge($crees, $participe);

    // Supprimer doublons : un covoiturage ne doit pas apparaître deux fois si l'utilisateur est créateur et participant
    $unique = [];
    foreach ($all as $covoit) {
        $unique[$covoit['id_covoiturage']] = $covoit;
    }

    // Trier par date/heure
    usort($unique, function($a, $b){
        return strcmp(
            $a['date_depart'] . ' ' . ($a['heure_depart'] ?? '00:00:00'),
            $b['date_depart'] . ' ' . ($b['heure_depart'] ?? '00:00:00')
        );
    });

    return array_values($unique);
}


/**
 * Historique des covoiturages passés
 */
function getHistoriqueReservationsByUtilisateur(int $id_utilisateur): array {
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
function reserverCovoiturage(int $id_utilisateur, int $id_covoiturage): array {
    global $pdo;

    try {
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
