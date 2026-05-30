<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireLogin();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    jsonError("L'identifiant de l'étudiant est manquant ou invalide.", 400);
}

$pdo = Database::getConnection();

$sql = "SELECT id_etudiant, num_etudiant, nom, prenom, email, filiere, niveau, date_inscription
        FROM etudiant
        WHERE id_etudiant = :id
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    jsonError('Étudiant introuvable.', 404);
}

jsonSuccess($etudiant, 'Étudiant trouvé.');
