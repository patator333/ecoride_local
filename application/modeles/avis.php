<?php
require_once ROOT_PATH . '/config/config.php';

/**
 * Soumettre un avis pour un covoiturage
 */
function soumettreAvis(int $id_utilisateur, int $id_covoiturage, string $commentaire, int $note): bool {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO avis (id_utilisateur, id_covoiturage, commentaire, date_avis, statut_validation)
        VALUES (:uid, :cid, :commentaire, NOW(), 0)
    ");
    return $stmt->execute([
        ':uid' => $id_utilisateur,
        ':cid' => $id_covoiturage,
        ':commentaire' => $commentaire
    ]);
}

/**
 * Récupérer tous les avis pour un covoiturage
 */
function getAvisByCovoiturage(int $id_covoiturage): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT a.*, u.nom AS nom_utilisateur 
        FROM avis a
        JOIN compte u ON a.id_utilisateur = u.id_utilisateur
        WHERE a.id_covoiturage = :cid
        ORDER BY a.date_avis DESC
    ");
    $stmt->execute([':cid' => $id_covoiturage]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
