<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireLogin();

$pdo = Database::getConnection();

$sql = "SELECT
            s.id_seance, s.date_seance, s.salle, s.id_cours,
            SUBSTRING(s.heure_debut,1,5) AS heure_debut,
            SUBSTRING(s.heure_fin,1,5)   AS heure_fin,
            c.code_cours, c.intitule AS cours,
            CONCAT(e.prenom, ' ', e.nom) AS enseignant,
            CONCAT(SUBSTRING(s.heure_debut,1,5), ' - ', SUBSTRING(s.heure_fin,1,5)) AS horaire
        FROM seance s
        JOIN cours c ON c.id_cours = s.id_cours
        LEFT JOIN enseignant e ON e.id_enseignant = c.id_enseignant
        ORDER BY s.date_seance, s.heure_debut";

$seances = $pdo->query($sql)->fetchAll();

jsonSuccess($seances, count($seances) . ' séance(s) trouvée(s).');
