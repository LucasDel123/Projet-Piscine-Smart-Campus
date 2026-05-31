<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireLogin();

$pdo = Database::getConnection();

$sql = "SELECT id_etudiant, num_etudiant, nom, prenom, email, niveau, groupe, date_inscription
        FROM etudiant
        ORDER BY nom, prenom";

$etudiants = $pdo->query($sql)->fetchAll();

jsonSuccess($etudiants, count($etudiants) . ' étudiant(s) trouvé(s).');
