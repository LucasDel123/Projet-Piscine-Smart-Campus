<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole(['enseignant', 'administrateur']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$id     = (int) ($input['id_note'] ?? 0);
$valeur = $input['valeur'] ?? null;
$type   = trim($input['type_evaluation'] ?? '');
$coef   = (float) ($input['coefficient'] ?? 1.0);

if ($id <= 0) {
    jsonError("L'identifiant de la note est manquant ou invalide.", 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT validee FROM note WHERE id_note = :id");
$stmt->execute([':id' => $id]);
$note = $stmt->fetch();
if (!$note) {
    jsonError('Note introuvable.', 404);
}

if ((int) $note['validee'] === 1) {
    jsonError('Cette note a été validée : elle ne peut plus être modifiée.', 403);
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
    jsonError('Type d\'évaluation invalide.', 400);
}

$sql = "UPDATE note SET valeur = :valeur, type_evaluation = :type, coefficient = :coef WHERE id_note = :id";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':valeur' => $valeur, ':type' => $type, ':coef' => $coef, ':id' => $id]);
    jsonSuccess(['id_note' => $id], 'Note modifiée avec succès.');
} catch (PDOException $e) {
    jsonError('Erreur lors de la modification : ' . $e->getMessage(), 500);
}
