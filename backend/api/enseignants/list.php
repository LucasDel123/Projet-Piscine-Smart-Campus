<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireLogin();

$pdo = Database::getConnection();

$sql = "SELECT id_enseignant, nom, prenom, email, departement, grade
        FROM enseignant
        ORDER BY nom, prenom";

$enseignants = $pdo->query($sql)->fetchAll();

jsonSuccess($enseignants, count($enseignants) . ' enseignant(s) trouvé(s).');
