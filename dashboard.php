<?php
/**
 * Talent SKULLVI By JM — Dashboard admin
 */
require_once __DIR__ . '/auth.php';

$stats = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(statut = 'Prioritaire')     AS prioritaires,
        SUM(statut = 'À examiner')      AS a_examiner,
        SUM(statut = 'Faible priorité') AS faibles,
        SUM(statut = 'Retenu')          AS retenus,
        SUM(statut = 'Rejeté')          AS rejetes,
        AVG(score)                      AS score_moyen,
        MAX(score)                      AS score_max
    FROM candidats
")->fetch();

$prioritaires = $pdo->query("
    SELECT id, nom, prenom, email, niveau_etude, experience, score, date_candidature
    FROM candidats
    WHERE statut = 'Prioritaire'
    ORDER BY score DESC, date_candidature ASC
    LIMIT 10
")->fetchAll();

$recentes = $pdo->query("
    SELECT id, nom, prenom, niveau_etude, score, statut, date_candidature
    FROM candidats
    ORDER BY date_candidature DESC
    LIMIT 8
")->fetchAll();

$par_niveau = $pdo->query("
    SELECT niveau_etude, COUNT(*) AS nb
    FROM candidats
    GROUP BY niveau_etude
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Talent SKULLVI By JM</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="site-header admin-header">
    <div class="container">
        <h1 class="logo">Talent <span>SKULLVI</span> <small>Admin</small></h1>
        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="candidats.php">Tous les candidats</a>
            <a href="export_csv.php">Export CSV</a>
            <span class="admin-name">👤 <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
            <a href="../actions/logout_admin.php" class="nav-logout">Déconnexion</a>
        </nav>
    </div>
</header>

<main class="container">

    <h2>Tableau de bord</h2>

    <div class="stats">
        <div class="stat"><strong><?= (int)$stats['total'] ?></strong><span>Candidats</span></div>
        <div class="stat stat-green"><strong><?= (int)$stats['prioritaires'] ?></strong><span>Prioritaires</span></div>
        <div class="stat stat-yellow"><strong><?= (int)$stats['a_examiner'] ?></strong><span>À examiner</span></div>
        <div class="stat stat-red"><strong><?= (int)$stats['faibles'] ?></strong><span>Faible priorité</span></div>
        <div class="stat stat-blue"><strong><?= (int)$stats['retenus'] ?></strong><span>Retenus</span></div>
        <div class="stat"><strong><?= (int)$stats['rejetes'] ?></strong><span>Rejetés</span></div>
        <div class="stat"><strong><?= round((float)$stats['score_moyen'], 1) ?></strong><span>Score moyen</span></div>
        <div class="stat"><strong><?= (int)$stats['score_max'] ?></strong><span>Meilleur score</span></div>
    </div>

    <section class="card">
        <div class="card-header">
            <h3>🎯 Profils à examiner en priorité</h3>
            <small>Candidats avec statut "Prioritaire" (score ≥ 80)</small>
        </div>

        <?php if (empty($prioritaires)): ?>
            <p class="empty">Aucun profil prioritaire en attente.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Niveau</th>
                        <th>Exp.</th>
                        <th>Score</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prioritaires as $c): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></strong><br>
                                <small><?= htmlspecialchars($c['email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars(ucfirst($c['niveau_etude'])) ?></td>
                            <td><?= (int)$c['experience'] ?> an(s)</td>
                            <td><strong class="score-high"><?= (int)$c['score'] ?></strong>/100</td>
                            <td><small><?= htmlspecialchars(date('d/m/Y', strtotime($c['date_candidature']))) ?></small></td>
                            <td><a href="candidat.php?id=<?= (int)$c['id'] ?>" class="btn btn-small">Examiner</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section class="card">
        <div class="card-header">
            <h3>📊 Répartition par niveau d'étude</h3>
        </div>
        <ul class="niveau-list">
            <?php foreach ($par_niveau as $n): ?>
                <li>
                    <span><?= htmlspecialchars(ucfirst($n['niveau_etude'])) ?></span>
                    <strong><?= (int)$n['nb'] ?> candidat(s)</strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="card">
        <div class="card-header">
            <h3>🕐 Dernières candidatures</h3>
        </div>
        <?php if (empty($recentes)): ?>
            <p class="empty">Aucune candidature.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Niveau</th>
                        <th>Score</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentes as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($c['niveau_etude'])) ?></td>
                            <td><strong><?= (int)$c['score'] ?></strong>/100</td>
                            <td>
                                <span class="badge badge-<?= statut_to_class($c['statut']) ?>">
                                    <?= htmlspecialchars($c['statut']) ?>
                                </span>
                            </td>
                            <td><small><?= htmlspecialchars(date('d/m/Y', strtotime($c['date_candidature']))) ?></small></td>
                            <td><a href="candidat.php?id=<?= (int)$c['id'] ?>" class="btn btn-small">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Talent SKULLVI By JM — Espace Admin</p>
    </div>
</footer>

</body>
</html>