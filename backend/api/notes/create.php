<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole(['enseignant', 'administrateur']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$idInsc = (int) ($input['id_inscription'] ?? 0);
$valeur = $input['valeur'] ?? null;
$type   = trim($input['type_evaluation'] ?? '');
$coef   = (float) ($input['coefficient'] ?? 1.0);

if ($idInsc <= 0) {
    jsonError('Vous devez sélectionner une inscription (étudiant + cours).', 400);
}
if ($valeur === null || $valeur === '' || !is_numeric($valeur)) {
    jsonError('La note doit être un nombre.', 400);
}
$valeur = (float) $valeur;
if ($valeur < 0 || $valeur > 20) {
    jsonError('La note doit être comprise entre 0 et 20.', 400);
}

$typesValides = ['CC1', 'CC2', 'DS', 'Examen', 'Projet'];
if (!in_array($type, $typesValides, true)) {
    jsonError('Type d\'évaluation invalide (CC1, CC2, DS, Examen ou Projet).', 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT id_inscription FROM inscription WHERE id_inscription = :id");
$stmt->execute([':id' => $idInsc]);
if (!$stmt->fetch()) {
    jsonError('Inscription introuvable.', 404);
}

$sql = "INSERT INTO note (valeur, type_evaluation, coefficient, date_saisie, id_inscription)
        VALUES (:valeur, :type, :coef, CURDATE(), :idInsc)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':valeur' => $valeur, ':type' => $type, ':coef' => $coef, ':idInsc' => $idInsc]);
    jsonSuccess(['id_note' => $pdo->lastInsertId()], 'Note enregistrée avec succès.', 201);
} catch (PDOException $e) {
    jsonError('Erreur lors de la saisie : ' . $e->getMessage(), 500);
}
