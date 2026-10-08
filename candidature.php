<?php
/**
 * Talent SKULLVI By JM — Formulaire de candidature
 */
require_once __DIR__ . '/config/database.php';

// Génération du token CSRF (une seule fois par session)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Récupération des erreurs éventuelles
$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talent SKULLVI By JM — Candidature</title>
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

    <h2>Formulaire de candidature</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="actions/enregistrer_candidat.php"
          method="POST"
          id="formCandidat"
          novalidate>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

        <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" maxlength="100"
                   value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" maxlength="100"
                   value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" maxlength="150"
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="text" id="telephone" name="telephone" maxlength="30"
                   value="<?= htmlspecialchars($old['telephone'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="niveau_etude">Niveau d'étude *</label>
            <select id="niveau_etude" name="niveau_etude" required>
                <option value="">Sélectionnez</option>
                <option value="secondaire" <?= (($old['niveau_etude'] ?? '') === 'secondaire') ? 'selected' : '' ?>>Secondaire</option>
                <option value="licence"    <?= (($old['niveau_etude'] ?? '') === 'licence')    ? 'selected' : '' ?>>Licence</option>
                <option value="master"     <?= (($old['niveau_etude'] ?? '') === 'master')     ? 'selected' : '' ?>>Master</option>
            </select>
        </div>

        <div class="form-group">
            <label for="experience">Années d'expérience *</label>
            <input type="number" id="experience" name="experience"
                   min="0" max="50"
                   value="<?= htmlspecialchars($old['experience'] ?? '0') ?>" required>
        </div>

        <div class="form-group">
            <label for="competences">Compétences *</label>
            <textarea id="competences" name="competences" required
                      placeholder="Ex : PHP, JavaScript, Excel, Communication"><?= htmlspecialchars($old['competences'] ?? '') ?></textarea>
            <small>Séparez chaque compétence par une virgule.</small>
        </div>

        <div class="form-group">
            <label for="motivation">Niveau de motivation *</label>
            <select id="motivation" name="motivation" required>
                <option value="">Sélectionnez</option>
                <option value="1" <?= (($old['motivation'] ?? '') == '1') ? 'selected' : '' ?>>1 — Faible</option>
                <option value="2" <?= (($old['motivation'] ?? '') == '2') ? 'selected' : '' ?>>2 — Moyenne</option>
                <option value="3" <?= (($old['motivation'] ?? '') == '3') ? 'selected' : '' ?>>3 — Bonne</option>
                <option value="4" <?= (($old['motivation'] ?? '') == '4') ? 'selected' : '' ?>>4 — Très bonne</option>
                <option value="5" <?= (($old['motivation'] ?? '') == '5') ? 'selected' : '' ?>>5 — Excellente</option>
            </select>
        </div>

        <div class="form-group">
            <label for="disponibilite">Disponibilité *</label>
            <select id="disponibilite" name="disponibilite" required>
                <option value="">Sélectionnez</option>
                <option value="immediate"      <?= (($old['disponibilite'] ?? '') === 'immediate')      ? 'selected' : '' ?>>Immédiate</option>
                <option value="partielle"      <?= (($old['disponibilite'] ?? '') === 'partielle')      ? 'selected' : '' ?>>Partielle</option>
                <option value="non_disponible" <?= (($old['disponibilite'] ?? '') === 'non_disponible') ? 'selected' : '' ?>>Non disponible</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer ma candidature</button>

    </form>

</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Talent SKULLVI By JM — Tous droits réservés.</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>