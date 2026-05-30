<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id_cours'] ?? 0);

if ($id <= 0) {
    jsonError("L'identifiant du cours est manquant ou invalide.", 400);
}

$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT intitule FROM cours WHERE id_cours = :id");
$stmt->execute([':id' => $id]);
$cours = $stmt->fetch();

if (!$cours) {
    jsonError('Cours introuvable.', 404);
}

try {
    $stmt = $pdo->prepare("DELETE FROM cours WHERE id_cours = :id");
    $stmt->execute([':id' => $id]);

    jsonSuccess(['id_cours' => $id], 'Cours « ' . $cours['intitule'] . ' » supprimé avec succès.');
} catch (PDOException $e) {
    jsonError('Erreur lors de la suppression : ' . $e->getMessage(), 500);
}
