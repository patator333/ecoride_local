<?php
require_once ROOT_PATH . '/config/config.php';

// Vérifier les identifiants (anciens mots de passe en clair et nouveaux hashés)
function verifierConnexion($email, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM compte WHERE mail = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Mot de passe hashé
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        // Mot de passe en clair (ancien compte)
        if ($user['password'] === $password) {
            // Mettre à jour le mot de passe avec hash pour sécuriser
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE compte SET password = :hash WHERE id_utilisateur = :id");
            $update->execute([':hash' => $newHash, ':id' => $user['id_utilisateur']]);
            return $user;
        }
    }
    return false;
}

// Récupérer un utilisateur par ID
function getUtilisateurById($id_utilisateur) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM compte WHERE id_utilisateur = :id");
    $stmt->execute([':id' => $id_utilisateur]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Mettre à jour le rôle
function updateRoleUtilisateur($id_utilisateur, $role) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE compte SET id_role = :role WHERE id_utilisateur = :id");
    $ok = $stmt->execute([':role' => $role, ':id' => $id_utilisateur]);
    return $ok ? "Rôle mis à jour avec succès." : "Erreur lors de la mise à jour du rôle.";
}

// Récupérer les préférences
function getPreferencesByUtilisateur($id_utilisateur) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM preference WHERE id_utilisateur = :id");
    $stmt->execute([':id' => $id_utilisateur]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Mettre à jour les préférences
function updatePreferences($id_utilisateur, $data) {
    global $pdo;
    $fumeur = isset($data['fumeur']) ? 1 : 0;
    $animal = isset($data['animal']) ? 1 : 0;
    $remarques = $data['remarques_particulieres'] ?? '';

    // Vérifier si l'utilisateur a déjà des préférences
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM preference WHERE id_utilisateur = :id");
    $stmt->execute([':id' => $id_utilisateur]);

    if ($stmt->fetchColumn() > 0) {
        $stmt = $pdo->prepare("UPDATE preference 
                               SET fumeur=:fumeur, animal=:animal, remarques_particulieres=:remarques 
                               WHERE id_utilisateur=:id");
    } else {
        $stmt = $pdo->prepare("INSERT INTO preference (id_utilisateur, fumeur, animal, remarques_particulieres) 
                               VALUES (:id, :fumeur, :animal, :remarques)");
    }

    $ok = $stmt->execute([
        ':id' => $id_utilisateur,
        ':fumeur' => $fumeur,
        ':animal' => $animal,
        ':remarques' => $remarques
    ]);

    return $ok ? "Préférences mises à jour." : "Erreur lors de la mise à jour des préférences.";
}
?>
