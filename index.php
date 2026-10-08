<?php
/**
 * Talent SKULLVI By JM — Accueil
 */
require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent SKULLVI By JM — Accueil</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container">
        <h1 class="logo">Talent <span>SKULLVI</span> <small>By JM</small></h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="candidature.php">Candidature</a>
            <a href="classement.php">Classement</a>
            <a href="login.php" class="nav-admin">Admin</a>
        </nav>
    </div>
</header>

<main class="container">

    <section class="hero">
        <h2>Révélez votre talent</h2>
        <p>
            Talent SKULLVI By JM évalue automatiquement chaque candidature
            selon 5 critères objectifs : niveau d'étude, expérience,
            compétences, motivation et disponibilité.
        </p>
        <a href="candidature.php" class="btn btn-primary">Postuler maintenant</a>
        <a href="classement.php" class="btn btn-secondary">Voir le classement</a>
    </section>

    <section class="criteres">
        <h3>Comment ça marche ?</h3>
        <ul class="grid">
            <li><strong>Niveau d'étude</strong> — 20 pts</li>
            <li><strong>Expérience</strong> — 20 pts</li>
            <li><strong>Compétences</strong> — 30 pts</li>
            <li><strong>Motivation</strong> — 20 pts</li>
            <li><strong>Disponibilité</strong> — 10 pts</li>
        </ul>
        <p class="note">Score total sur 100 — classement automatique en temps réel.</p>
    </section>

</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Talent SKULLVI By JM — Tous droits réservés.</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>