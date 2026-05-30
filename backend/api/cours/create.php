<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$code     = trim($input['code_cours'] ?? '');
$intitule = trim($input['intitule'] ?? '');
$semestre = trim($input['semestre'] ?? 'S1');
$credits  = (int) ($input['credits'] ?? 3);
$coef     = (float) ($input['coefficient'] ?? 1.0);
$capacite = (int) ($input['capacite_max'] ?? 30);

$idEns = ($input['id_enseignant'] ?? '') === '' ? null : (int) $input['id_enseignant'];

if ($code === '' || $intitule === '') {
    jsonError('Le code et l\'intitulé du cours sont obligatoires.', 400);
}
if ($capacite <= 0) {
    jsonError('La capacité maximale doit être supérieure à 0.', 400);
}

$pdo = Database::getConnection();

$sql = "INSERT INTO cours (code_cours, intitule, credits, coefficient, semestre, capacite_max, id_enseignant)
        VALUES (:code, :intitule, :credits, :coef, :semestre, :capacite, :idEns)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':code'     => $code,
        ':intitule' => $intitule,
        ':credits'  => $credits,
        ':coef'     => $coef,
        ':semestre' => $semestre,
        ':capacite' => $capacite,
        ':idEns'    => $idEns,
    ]);

    jsonSuccess(['id_cours' => $pdo->lastInsertId(), 'code_cours' => $code, 'intitule' => $intitule],
        'Cours créé avec succès.', 201);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        jsonError('Ce code de cours est déjà utilisé.', 409);
    }
    jsonError('Erreur lors de la création : ' . $e->getMessage(), 500);
}
