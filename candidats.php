<?php
/**
 * Talent SKULLVI By JM — Liste complète (admin)
 */
require_once __DIR__ . '/auth.php';

$q        = trim($_GET['q'] ?? '');
$f_statut = trim($_GET['statut'] ?? '');
$f_niveau = trim($_GET['niveau'] ?? '');

$where  = [];
$params = [];

if ($q !== '') {
    $where[] = "(nom LIKE :q OR prenom LIKE :q OR email LIKE :q OR competences LIKE :q)";
    $params[':q'] = "%$q%";
}
if ($f_statut !== '') {
    $where[] = "statut = :statut";
    $params[':statut'] = $f_statut;
}
if ($f_niveau !== '') {
    $where[] = "niveau_etude = :niveau";
    $params[':niveau'] = $f_niveau;
}

$sql = "SELECT id, nom, prenom, email, niveau_etude, experience, score, statut, date_candidature
        FROM candidats";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY score DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$candidats = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les candidats — Admin SKULLVI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="site-header admin-header">
    <div class="container">
        <h1 class="logo">Talent <span>SKULLVI</span> <small>Admin</small></h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="candidats.php" class="active">Tous les candidats</a>
            <a href="export_csv.php">Export CSV</a>
            <a href="../actions/logout_admin.php" class="nav-logout">Déconnexion</a>
        </nav>
    </div>
</header>

<main class="container">

    <h2>Tous les candidats (<?= count($candidats) ?>)</h2>

    <form method="GET" class="filters">
        <input type="text" name="q" placeholder="Rechercher…" value="<?= htmlspecialchars($q) ?>">
        <select name="statut">
            <option value="">Tous statuts</option>
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
        <button type="submit" class="btn btn-primary btn-small">Filtrer</button>
        <a href="candidats.php" class="btn btn-secondary btn-small">Réinitialiser</a>
    </form>

    <?php if (empty($candidats)): ?>
        <p class="empty">Aucun candidat trouvé.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Niveau</th>
                    <th>Exp.</th>
                    <th>Score</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidats as $c): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></strong></td>
                        <td><small><?= htmlspecialchars($c['email']) ?></small></td>
                        <td><?= htmlspecialchars(ucfirst($c['niveau_etude'])) ?></td>
                        <td><?= (int)$c['experience'] ?> an(s)</td>
                        <td><strong><?= (int)$c['score'] ?></strong>/100</td>
                        <td>
                            <span class="badge badge-<?= statut_to_class($c['statut']) ?>">
                                <?= htmlspecialchars($c['statut']) ?>
                            </span>
                        </td>
                        <td><a href="candidat.php?id=<?= (int)$c['id'] ?>" class="btn btn-small">Examiner</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Talent SKULLVI By JM — Espace Admin</p>
    </div>
</footer>

</body>
</html>