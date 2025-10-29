<?php
require_once ROOT_PATH . '/config/config.php';

/**
 * Ajouter un avis
 */
function ajouterAvis(int $id_reservation, int $note, string $commentaire): array {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE avis_covoiturage SET note=:note, commentaire=:commentaire WHERE id_reservation=:id_reservation");
        $stmt->execute([
            ':id_reservation'=>$id_reservation,
            ':note'=>$note,
            ':commentaire'=>$commentaire
        ]);
        return ['success'=>true, 'message'=>'Avis envoyé.'];
    } catch(Exception $e) {
        return ['success'=>false, 'message'=>'Erreur : '.$e->getMessage()];
    }
}

/**
 * Récupérer les avis pour un utilisateur
 */
function getAvisByReservation(int $id_reservation): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM avis_covoiturage WHERE id_reservation=:id_reservation");
    $stmt->execute([':id_reservation'=>$id_reservation]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
