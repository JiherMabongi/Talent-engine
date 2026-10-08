<?php
/**
 * Talent SKULLVI By JM — Détails d'un candidat (vue publique)
 */
require_once __DIR__ . '/config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: classement.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM candidats WHERE id = ?");
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) {
    http_response_code(404);
    die("Candidat introuvable.");
}

$points = [
    "Niveau d'étude" => match ($c['niveau_etude']) {
        'secondaire' => 10, 'licence' => 15, 'master' => 20, default => 0
    },
    'Expérience'    => min((int)$c['experience'] * 4, 20),
    'Compétences'   => min(count(array_filter(array_map('trim', explode(',', $c['competences'])))) * 6, 30),
    'Motivation'    => (int)$c['motivation'] * 4,
    'Disponibilité' => match ($c['disponibilite']) {
        'immediate' => 10, 'partielle' => 5, default => 0
    },
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?> — Talent SKULLVI By JM</title>
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

    <div class="fiche">

        <h2><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></h2>

        <p class="statut-line">
            Statut :
            <span class="badge badge-<?= statut_to_class($c['statut']) ?>">
                <?= htmlspecialchars($c['statut']) ?>
            </span>
            — Score : <strong><?= (int)$c['score'] ?> / 100</strong>
        </p>

        <div class="infos">
            <p><strong>Niveau d'étude :</strong> <?= htmlspecialchars(ucfirst($c['niveau_etude'])) ?></p>
            <p><strong>Expérience :</strong> <?= (int)$c['experience'] ?> an(s)</p>
            <p><strong>Motivation :</strong> <?= (int)$c['motivation'] ?> / 5</p>
            <p><strong>Disponibilité :</strong> <?= htmlspecialchars(str_replace('_', ' ', ucfirst($c['disponibilite']))) ?></p>
            <p><strong>Compétences :</strong> <?= htmlspecialchars($c['competences']) ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($c['date_candidature']) ?></p>
        </div>

        <h3>Détail du score</h3>
        <ul class="score-detail">
            <?php foreach ($points as $label => $val): ?>
                <li><span><?= htmlspecialchars($label) ?></span><strong><?= $val ?> pts</strong></li>
            <?php endforeach; ?>
        </ul>

        <a href="classement.php" class="btn btn-secondary">← Retour au classement</a>

    </div>

</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Talent SKULLVI By JM — Tous droits réservés.</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>