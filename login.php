<?php
/**
 * Talent SKULLVI By JM — Connexion admin
 */
require_once __DIR__ . '/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['nom_complet'] ?: $admin['username'];

            header('Location: admin/dashboard.php');
            exit;
        } else {
            $error = "Identifiants incorrects.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin — Talent SKULLVI By JM</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">

<div class="login-box">

    <h1 class="logo center">Talent <span>SKULLVI</span> <small>By JM</small></h1>
    <h2 class="center">Espace Administrateur</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary full">Se connecter</button>
    </form>

    <p class="center mt-20">
        <a href="index.php">← Retour au site</a>
    </p>

</div>

</body>
</html>