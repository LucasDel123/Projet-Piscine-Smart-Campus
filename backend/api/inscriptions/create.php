<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$idEtu   = (int) ($input['id_etudiant'] ?? 0);
$idCours = (int) ($input['id_cours'] ?? 0);

if ($idEtu <= 0 || $idCours <= 0) {
    jsonError('Vous devez sélectionner un étudiant et un cours.', 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT intitule, capacite_max FROM cours WHERE id_cours = :id");
$stmt->execute([':id' => $idCours]);
$cours = $stmt->fetch();
if (!$cours) {
    jsonError('Cours introuvable.', 404);
}

$stmt = $pdo->prepare("SELECT COUNT(*) AS nb FROM inscription WHERE id_cours = :id");
$stmt->execute([':id' => $idCours]);
$nbInscrits = (int) $stmt->fetch()['nb'];

if ($nbInscrits >= (int) $cours['capacite_max']) {
    jsonError('Le cours « ' . $cours['intitule'] . ' » est complet (capacité maximale atteinte).', 409);
}

$sql = "INSERT INTO inscription (id_etudiant, id_cours, date_inscription)
        VALUES (:idEtu, :idCours, CURDATE())";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':idEtu' => $idEtu, ':idCours' => $idCours]);

    jsonSuccess(
        ['id_inscription' => $pdo->lastInsertId()],
        'Inscription enregistrée avec succès.',
        201
    );
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        jsonError('Cet étudiant est déjà inscrit à ce cours.', 409);
    }
    jsonError('Erreur lors de l\'inscription : ' . $e->getMessage(), 500);
}
