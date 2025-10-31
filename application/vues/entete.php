<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titrePage ?></title>
    <link href="<?= PUBLIC_URL ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= PUBLIC_URL ?>/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Ecoride</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
        aria-controls="navbarNav" aria-expanded="false" aria-label="Basculer la navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto"> 
        <li class="nav-item">
          <a class="nav-link" href="index.php">Page d'accueil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=recherche_covoiturage">Covoiturage</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=connexion">Connexion</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=creer_compte">Inscription</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=contact">Contact</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="<?= PUBLIC_URL ?>/js/bootstrap.bundle.min.js"></script>
