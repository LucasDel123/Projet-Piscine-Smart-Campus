<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id_seance'] ?? 0);

if ($id <= 0) {
    jsonError("L'identifiant de la séance est manquant ou invalide.", 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT id_seance FROM seance WHERE id_seance = :id");
$stmt->execute([':id' => $id]);
if (!$stmt->fetch()) {
    jsonError('Séance introuvable.', 404);
}

try {
    $stmt = $pdo->prepare("DELETE FROM seance WHERE id_seance = :id");
    $stmt->execute([':id' => $id]);
    jsonSuccess(['id_seance' => $id], 'Séance supprimée de l\'emploi du temps.');
} catch (PDOException $e) {
    jsonError('Erreur lors de la suppression : ' . $e->getMessage(), 500);
}
