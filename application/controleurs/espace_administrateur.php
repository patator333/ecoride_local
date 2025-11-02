<?php

require_once APP_PATH . '/modeles/administrateur.php';
require_once ROOT_PATH . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier que l'utilisateur est administrateur
if (empty($_SESSION['user']) || $_SESSION['user']['id_type_compte'] != 3) {
    header("Location: " . PUBLIC_URL . "/?page=connexion");
    exit;
}

// Création compte employé
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_employe'])) {
    $nom = trim($_POST['nom'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($nom && $mail && $password) {
        $ok = creerCompteEmploye($nom, $mail, $password);
        $message = $ok ? "Compte employé créé." : "Erreur lors de la création.";
    } else {
        $message = "Merci de remplir tous les champs.";
    }
}

// Modifier le statut d'un compte
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'suspendre') {
        modifierStatutCompte($id, 0);
    } elseif ($_GET['action'] === 'activer') {
        modifierStatutCompte($id, 1);
    }
}

// Données pour les graphiques
$covoiturages_semaine = getCovoituragesDerniers7Jours();
$credits_semaine = getCreditsParJour();

// Liste des comptes
$comptes = getComptes();

// Inclure la vue
include APP_PATH . '/vues/espace_administrateur.php';
