<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id_inscription'] ?? 0);

if ($id <= 0) {
    jsonError("L'identifiant de l'inscription est manquant ou invalide.", 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT id_inscription FROM inscription WHERE id_inscription = :id");
$stmt->execute([':id' => $id]);
if (!$stmt->fetch()) {
    jsonError('Inscription introuvable.', 404);
}

try {
    $stmt = $pdo->prepare("DELETE FROM inscription WHERE id_inscription = :id");
    $stmt->execute([':id' => $id]);
    jsonSuccess(['id_inscription' => $id], 'Étudiant désinscrit du cours avec succès.');
} catch (PDOException $e) {
    jsonError('Erreur lors de la désinscription : ' . $e->getMessage(), 500);
}
