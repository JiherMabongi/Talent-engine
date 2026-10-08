<?php
/**
 * Talent SKULLVI By JM — Classement public
 */
require_once __DIR__ . '/config/database.php';

$q        = trim($_GET['q'] ?? '');
$f_statut = trim($_GET['statut'] ?? '');
$f_niveau = trim($_GET['niveau'] ?? '');
$f_dispo  = trim($_GET['dispo'] ?? '');
$tri      = $_GET['tri'] ?? 'score';

$where  = [];
$params = [];

if ($q !== '') {
    $where[] = "(nom LIKE :q OR prenom LIKE :q OR email LIKE :q OR competences LIKE :q)";
    $params[':q'] = "%$q%";
}
if (in_array($f_statut, ['Prioritaire','À examiner','Faible priorité','Retenu','Rejeté'], true)) {
    $where[] = "statut = :statut";
    $params[':statut'] = $f_statut;
}
if (in_array($f_niveau, ['secondaire','licence','master'], true)) {
    $where[] = "niveau_etude = :niveau";
    $params[':niveau'] = $f_niveau;
}
if (in_array($f_dispo, ['immediate','partielle','non_disponible'], true)) {
    $where[] = "disponibilite = :dispo";
    $params[':dispo'] = $f_dispo;
}

$order = match ($tri) {
    'date'  => 'date_candidature DESC',
    'nom'   => 'nom ASC, prenom ASC',
    default => 'score DESC, date_candidature ASC',
};

$sql = "SELECT id, nom, prenom, email, niveau_etude, experience,
               motivation, disponibilite, score, statut, date_candidature
        FROM candidats";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$candidats = $stmt->fetchAll();

$stats = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(statut = 'Prioritaire')      AS prioritaires,
        SUM(statut = 'À examiner')       AS a_examiner,
        SUM(statut = 'Faible priorité')  AS faibles,
        SUM(statut = 'Retenu')           AS retenus,
        SUM(statut = 'Rejeté')           AS rejetes
    FROM candidats
")->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent SKULLVI By JM — Classement</title>
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

    <h2>Classement des candidats</h2>

    <div class="stats">
        <div class="stat"><strong><?= (int)$stats['total'] ?></strong><span>Total</span></div>
        <div class="stat stat-green"><strong><?= (int)$stats['prioritaires'] ?></strong><span>Prioritaires</span></div>
        <div class="stat stat-yellow"><strong><?= (int)$stats['a_examiner'] ?></strong><span>À examiner</span></div>
        <div class="stat stat-red"><strong><?= (int)$stats['faibles'] ?></strong><span>Faible priorité</span></div>
        <div class="stat stat-blue"><strong><?= (int)$stats['retenus'] ?></strong><span>Retenus</span></div>
        <div class="stat"><strong><?= (int)$stats['rejetes'] ?></strong><span>Rejetés</span></div>
    </div>

    <form method="GET" class="filters">
        <input type="text" name="q" placeholder="Rechercher nom, email, compétence…"
               value="<?= htmlspecialchars($q) ?>">

        <select name="statut">
            <option value="">Tous les statuts</option>
            <?php foreach (['Prioritaire','À examiner','Faible priorité','Retenu','Rejeté'] as $s): ?>
                <option value="<?= $s ?>" <?= $f_statut === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>

        <select name="niveau">
            <option value="">Tous niveaux</option>
            <?php foreach (['secondaire','licence','master'] as $n): ?>
                <option value="<?= $n ?>" <?= $f_niveau === $n ? 'selected' : '' ?>><?= ucfirst($n) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="dispo">
            <option value="">Toutes dispo.</option>
            <?php foreach (['immediate','partielle','non_disponible'] as $d): ?>
                <option value="<?= $d ?>" <?= $f_dispo === $d ? 'selected' : '' ?>>
                    <?= str_replace('_', ' ', ucfirst($d)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tri">
            <option value="score" <?= $tri === 'score' ? 'selected' : '' ?>>Tri : Score</option>
            <option value="date"  <?= $tri === 'date'  ? 'selected' : '' ?>>Tri : Date</option>
            <option value="nom"   <?= $tri === 'nom'   ? 'selected' : '' ?>>Tri : Nom</option>
        </select>

        <button type="submit" class="btn btn-primary btn-small">Filtrer</button>
        <a href="classement.php" class="btn btn-secondary btn-small">Réinitialiser</a>
    </form>

    <?php if (empty($candidats)): ?>
        <p class="empty">Aucun candidat ne correspond à ces critères.</p>
    <?php else: ?>
        <p class="result-count"><?= count($candidats) ?> résultat(s)</p>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Niveau</th>
                    <th>Exp.</th>
                    <th>Score</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidats as $i => $c): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($c['niveau_etude'])) ?></td>
                        <td><?= (int) $c['experience'] ?> an(s)</td>
                        <td><strong><?= (int) $c['score'] ?></strong> / 100</td>
                        <td>
                            <span class="badge badge-<?= statut_to_class($c['statut']) ?>">
                                <?= htmlspecialchars($c['statut']) ?>
                            </span>
                        </td>
                        <td><a href="details.php?id=<?= (int) $c['id'] ?>" class="btn btn-small">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Talent SKULLVI By JM — Tous droits réservés.</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>