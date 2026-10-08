<?php
/**
 * Talent SKULLVI By JM
 * Connexion PDO + démarrage de session centralisé
 */

// Démarre la session UNE SEULE FOIS pour tout le projet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host     = "localhost";
$dbname   = "talent_engine";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

/**
 * Fonction utilitaire : transforme un statut en classe CSS valide
 * Ex : "À examiner" → "a-examiner"
 */
function statut_to_class(string $statut): string
{
    $s = strtolower($statut);
    $s = str_replace(
        ['à','â','ä','é','è','ê','ë','î','ï','ô','ö','ù','û','ü','ç',' '],
        ['a','a','a','e','e','e','e','i','i','o','o','u','u','u','c','-'],
        $s
    );
    return preg_replace('/[^a-z0-9\-]/', '', $s);
}