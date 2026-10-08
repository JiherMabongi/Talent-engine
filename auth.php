<?php
/**
 * Talent SKULLVI By JM
 * Middleware d'authentification admin
 */
require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}