<?php
/**
 * Talent SKULLVI By JM
 * Traitement de la candidature
 */
require_once __DIR__ . '/../config/database.php';

/* 1. POST obligatoire */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../candidature.php');
    exit;
}

/* 2. CSRF */
$token = $_POST['csrf_token'] ?? '';
if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $_SESSION['errors'] = ["Session expirée. Veuillez renvoyer le formulaire."];
    header('Location: ../candidature.php');
    exit;
}

/* 3. Récupération */
$nom           = trim($_POST['nom']           ?? '');
$prenom        = trim($_POST['prenom']        ?? '');
$email         = trim($_POST['email']         ?? '');
$telephone     = trim($_POST['telephone']     ?? '');
$niveau_etude  = trim($_POST['niveau_etude']  ?? '');
$experience    = (int) ($_POST['experience']  ?? 0);
$competences   = trim($_POST['competences']   ?? '');
$motivation    = (int) ($_POST['motivation']  ?? 0);
$disponibilite = trim($_POST['disponibilite'] ?? '');

/* 4. Validation */
$errors = [];

if ($nom === '' || mb_strlen($nom) > 100) {
    $errors[] = "Le nom est obligatoire (max 100 caractères).";
}
if ($prenom === '' || mb_strlen($prenom) > 100) {
    $errors[] = "Le prénom est obligatoire (max 100 caractères).";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Adresse email invalide.";
}
if (!in_array($niveau_etude, ['secondaire','licence','master'], true)) {
    $errors[] = "Niveau d'étude invalide.";
}
if ($experience < 0 || $experience > 50) {
    $errors[] = "L'expérience doit être comprise entre 0 et 50 ans.";
}
if ($competences === '') {
    $errors[] = "Veuillez renseigner au moins une compétence.";
}
if ($motivation < 1 || $motivation > 5) {
    $errors[] = "La motivation doit être comprise entre 1 et 5.";
}
if (!in_array($disponibilite, ['immediate','partielle','non_disponible'], true)) {
    $errors[] = "Disponibilité invalide.";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = $_POST;
    header('Location: ../candidature.php');
    exit;
}

/* 5. Score */
$points_niveau = match ($niveau_etude) {
    'secondaire' => 10,
    'licence'    => 15,
    'master'     => 20,
    default      => 0,
};

$points_experience = min($experience * 4, 20);

$liste_competences = array_filter(
    array_map('trim', explode(',', $competences)),
    fn($c) => $c !== ''
);
$points_competences = min(count($liste_competences) * 6, 30);

$points_motivation = $motivation * 4;

$points_dispo = match ($disponibilite) {
    'immediate'      => 10,
    'partielle'      => 5,
    'non_disponible' => 0,
    default          => 0,
};

$score = $points_niveau + $points_experience + $points_competences
       + $points_motivation + $points_dispo;

/* 6. Statut */
if ($score >= 80) {
    $statut = 'Prioritaire';
} elseif ($score >= 60) {
    $statut = 'À examiner';
} else {
    $statut = 'Faible priorité';
}

/* 7. Insertion */
try {
    $check = $pdo->prepare("SELECT id FROM candidats WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        $_SESSION['errors'] = ["Cet email est déjà utilisé pour une candidature."];
        $_SESSION['old']    = $_POST;
        header('Location: ../candidature.php');
        exit;
    }

    $sql = "INSERT INTO candidats
            (nom, prenom, email, telephone, niveau_etude,
             experience, competences, motivation, disponibilite,
             score, statut)
            VALUES
            (:nom, :prenom, :email, :telephone, :niveau_etude,
             :experience, :competences, :motivation, :disponibilite,
             :score, :statut)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'            => $nom,
        ':prenom'         => $prenom,
        ':email'          => $email,
        ':telephone'      => $telephone,
        ':niveau_etude'   => $niveau_etude,
        ':experience'     => $experience,
        ':competences'    => $competences,
        ':motivation'     => $motivation,
        ':disponibilite'  => $disponibilite,
        ':score'          => $score,
        ':statut'         => $statut,
    ]);

    $candidat_id = (int) $pdo->lastInsertId();

    unset($_SESSION['csrf_token']);

    header("Location: ../confirmation.php?id=$candidat_id");
    exit;

} catch (PDOException $e) {
    $_SESSION['errors'] = ["Erreur lors de l'enregistrement : " . $e->getMessage()];
    $_SESSION['old']    = $_POST;
    header('Location: ../candidature.php');
    exit;
}