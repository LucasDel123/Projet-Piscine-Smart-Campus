<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireLogin();

$pdo = Database::getConnection();

$sql = "SELECT
            n.id_note, n.valeur, n.type_evaluation, n.coefficient, n.validee,
            i.id_inscription,
            CONCAT(e.prenom, ' ', e.nom) AS etudiant,
            c.code_cours, c.intitule AS cours,
            CASE WHEN n.validee = 1 THEN 'Validée' ELSE 'En attente' END AS statut
        FROM note n
        JOIN inscription i ON i.id_inscription = n.id_inscription
        JOIN etudiant e ON e.id_etudiant = i.id_etudiant
        JOIN cours c ON c.id_cours = i.id_cours
        ORDER BY n.id_note DESC";

$notes = $pdo->query($sql)->fetchAll();

jsonSuccess($notes, count($notes) . ' note(s) trouvée(s).');
