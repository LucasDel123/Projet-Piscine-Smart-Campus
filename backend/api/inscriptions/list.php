<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireLogin();

$pdo = Database::getConnection();

$sql = "SELECT
            i.id_inscription, i.id_etudiant, i.id_cours, i.date_inscription,
            CONCAT(e.prenom, ' ', e.nom) AS etudiant,
            c.code_cours, c.intitule AS cours
        FROM inscription i
        JOIN etudiant e ON e.id_etudiant = i.id_etudiant
        JOIN cours c ON c.id_cours = i.id_cours
        ORDER BY i.id_inscription DESC";

$inscriptions = $pdo->query($sql)->fetchAll();

jsonSuccess($inscriptions, count($inscriptions) . ' inscription(s) trouvée(s).');
