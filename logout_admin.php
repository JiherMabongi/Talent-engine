<?php
/**
 * Talent SKULLVI By JM — Déconnexion admin
 */
require_once __DIR__ . '/../config/database.php';

$_SESSION = [];
session_destroy();

header('Location: ../index.php');
exit;