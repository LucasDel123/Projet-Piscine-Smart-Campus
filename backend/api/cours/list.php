<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireLogin();

$pdo = Database::getConnection();

$sql = "SELECT
            c.id_cours, c.code_cours, c.intitule, c.credits, c.coefficient,
            c.semestre, c.capacite_max, c.id_enseignant,
            CONCAT(e.prenom, ' ', e.nom) AS enseignant,
            (SELECT COUNT(*) FROM inscription i WHERE i.id_cours = c.id_cours) AS nb_inscrits,
            CONCAT((SELECT COUNT(*) FROM inscription i WHERE i.id_cours = c.id_cours), ' / ', c.capacite_max) AS remplissage
        FROM cours c
        LEFT JOIN enseignant e ON e.id_enseignant = c.id_enseignant
        ORDER BY c.code_cours";

$cours = $pdo->query($sql)->fetchAll();

jsonSuccess($cours, count($cours) . ' cours trouvé(s).');
