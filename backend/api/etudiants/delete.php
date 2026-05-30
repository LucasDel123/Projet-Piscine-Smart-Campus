<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$id = (int) ($input['id_etudiant'] ?? 0);

if ($id <= 0) {
    jsonError("L'identifiant de l'étudiant est manquant ou invalide.", 400);
}

$pdo = Database::getConnection();

$stmt = $pdo->prepare("SELECT nom, prenom FROM etudiant WHERE id_etudiant = :id");
$stmt->execute([':id' => $id]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    jsonError('Étudiant introuvable.', 404);
}

try {
    $stmt = $pdo->prepare("DELETE FROM etudiant WHERE id_etudiant = :id");
    $stmt->execute([':id' => $id]);

    jsonSuccess(
        ['id_etudiant' => $id],
        'Étudiant ' . $etudiant['prenom'] . ' ' . $etudiant['nom'] . ' supprimé avec succès.'
    );

} catch (PDOException $e) {
    jsonError('Erreur lors de la suppression : ' . $e->getMessage(), 500);
}
