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
$stmt = $pdo->prepare("SELECT id_cours FROM cours WHERE id_cours = :id");
$stmt->execute([':id' => $id]);
if (!$stmt->fetch()) {
    jsonError('Cours introuvable.', 404);
}

$code     = trim($input['code_cours'] ?? '');
$intitule = trim($input['intitule'] ?? '');
$semestre = trim($input['semestre'] ?? 'S1');
$credits  = (int) ($input['credits'] ?? 3);
$coef     = (float) ($input['coefficient'] ?? 1.0);
$capacite = (int) ($input['capacite_max'] ?? 30);
$idEns    = ($input['id_enseignant'] ?? '') === '' ? null : (int) $input['id_enseignant'];

if ($code === '' || $intitule === '') {
    jsonError('Le code et l\'intitulé du cours sont obligatoires.', 400);
}
if ($capacite <= 0) {
    jsonError('La capacité maximale doit être supérieure à 0.', 400);
}

$sql = "UPDATE cours
        SET code_cours = :code, intitule = :intitule, credits = :credits, coefficient = :coef,
            semestre = :semestre, capacite_max = :capacite, id_enseignant = :idEns
        WHERE id_cours = :id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':code' => $code, ':intitule' => $intitule, ':credits' => $credits, ':coef' => $coef,
        ':semestre' => $semestre, ':capacite' => $capacite, ':idEns' => $idEns, ':id' => $id,
    ]);

    jsonSuccess(['id_cours' => $id, 'code_cours' => $code, 'intitule' => $intitule],
        'Cours modifié avec succès.');
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        jsonError('Ce code de cours est déjà utilisé par un autre cours.', 409);
    }
    jsonError('Erreur lors de la modification : ' . $e->getMessage(), 500);
}
