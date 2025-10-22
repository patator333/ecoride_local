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
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="index.php">Ecoride</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto"> 
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
  