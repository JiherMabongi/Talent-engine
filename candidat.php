<?php
/**
 * Talent SKULLVI By JM — Fiche candidat (admin) + décision
 */
require_once __DIR__ . '/auth.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: dashboard.php'); exit; }

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_statut = $_POST['statut'] ?? '';
    $note_admin     = trim($_POST['note_admin'] ?? '');

    $statuts_valides = ['Prioritaire','À examiner','Faible priorité','Retenu','Rejeté'];

    if (in_array($nouveau_statut, $statuts_valides, true)) {
        $stmt = $pdo->prepare("UPDATE candidats SET statut = ?, note_admin = ? WHERE id = ?");
        $stmt->execute([$nouveau_statut, $note_admin, $id]);
        $success = "Décision enregistrée : statut = « $nouveau_statut ».";
    }
}

$stmt = $pdo->prepare("SELECT * FROM candidats WHERE id = ?");
$stmt->execute([$id]);
$c = $stmt->fetch();

if (!$c) { http_response_code(404); die("Candidat introuvable."); }

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
    <title>Fiche candidat — Admin SKULLVI</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header class="site-header admin-header">
    <div class="container">
        <h1 class="logo">Talent <span>SKULLVI</span> <small>Admin</small></h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="candidats.php">Tous les candidats</a>
            <a href="../actions/logout_admin.php" class="nav-logout">Déconnexion</a>
        </nav>
    </div>
</header>

<main class="container">

    <?php if ($success): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="fiche">

        <h2><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></h2>

        <p class="statut-line">
            Score : <strong><?= (int)$c['score'] ?> / 100</strong> —
            Statut actuel :
            <span class="badge badge-<?= statut_to_class($c['statut']) ?>">
                <?= htmlspecialchars($c['statut']) ?>
            </span>
        </p>

        <div class="infos">
            <p><strong>Email :</strong> <?= htmlspecialchars($c['email']) ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($c['telephone'] ?: '—') ?></p>
            <p><strong>Niveau d'étude :</strong> <?= htmlspecialchars(ucfirst($c['niveau_etude'])) ?></p>
            <p><strong>Expérience :</strong> <?= (int)$c['experience'] ?> an(s)</p>
            <p><strong>Compétences :</strong> <?= htmlspecialchars($c['competences']) ?></p>
            <p><strong>Motivation :</strong> <?= (int)$c['motivation'] ?> / 5</p>
            <p><strong>Disponibilité :</strong> <?= htmlspecialchars(str_replace('_', ' ', ucfirst($c['disponibilite']))) ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($c['date_candidature']) ?></p>
        </div>

        <h3>Détail du score</h3>
        <ul class="score-detail">
            <?php foreach ($points as $label => $val): ?>
                <li><span><?= htmlspecialchars($label) ?></span><strong><?= $val ?> pts</strong></li>
            <?php endforeach; ?>
        </ul>

        <h3>Décision administrative</h3>
        <form method="POST" class="form-decision">
            <div class="form-group">
                <label for="statut">Nouveau statut</label>
                <select name="statut" id="statut" required>
                    <option value="">— Choisir —</option>
                    <?php foreach (['Prioritaire','À examiner','Faible priorité','Retenu','Rejeté'] as $s): ?>
                        <option value="<?= $s ?>" <?= $c['statut'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="note_admin">Note interne (optionnel)</label>
                <textarea name="note_admin" id="note_admin"
                          placeholder="Commentaire sur ce candidat…"><?= htmlspecialchars($c['note_admin'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer la décision</button>
            <a href="dashboard.php" class="btn btn-secondary">← Retour</a>
        </form>

    </div>

</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Talent SKULLVI By JM — Espace Admin</p>
    </div>
</footer>

</body>
</html>