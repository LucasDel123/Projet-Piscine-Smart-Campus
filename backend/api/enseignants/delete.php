<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$id = (int) ($input['id_enseignant'] ?? 0);

if ($id <= 0) {
    jsonError("L'identifiant de l'enseignant est manquant ou invalide.", 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT nom, prenom FROM enseignant WHERE id_enseignant = :id");
$stmt->execute([':id' => $id]);
$ens = $stmt->fetch();

if (!$ens) {
    jsonError('Enseignant introuvable.', 404);
}

try {
    $stmt = $pdo->prepare("DELETE FROM enseignant WHERE id_enseignant = :id");
    $stmt->execute([':id' => $id]);

    jsonSuccess(
        ['id_enseignant' => $id],
        'Enseignant ' . $ens['prenom'] . ' ' . $ens['nom'] . ' supprimé avec succès.'
    );
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        jsonError('Impossible de supprimer : cet enseignant est associé à des cours.', 409);
    }
    jsonError('Erreur lors de la suppression : ' . $e->getMessage(), 500);
}
