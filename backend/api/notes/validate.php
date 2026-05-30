<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole(['enseignant', 'administrateur']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id_note'] ?? 0);

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
    jsonError('Cette note est déjà validée.', 409);
}

try {
    $stmt = $pdo->prepare("UPDATE note SET validee = 1 WHERE id_note = :id");
    $stmt->execute([':id' => $id]);
    jsonSuccess(['id_note' => $id], 'Note validée et verrouillée.');
} catch (PDOException $e) {
    jsonError('Erreur lors de la validation : ' . $e->getMessage(), 500);
}
