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

$stmt = $pdo->prepare("SELECT id_enseignant FROM enseignant WHERE id_enseignant = :id");
$stmt->execute([':id' => $id]);
if (!$stmt->fetch()) {
    jsonError('Enseignant introuvable.', 404);
}

$nom         = trim($input['nom'] ?? '');
$prenom      = trim($input['prenom'] ?? '');
$email       = trim($input['email'] ?? '');
$departement = trim($input['departement'] ?? '');
$grade       = trim($input['grade'] ?? '');

if ($nom === '' || $prenom === '' || $email === '') {
    jsonError('Le nom, le prénom et l\'email sont obligatoires.', 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Le format de l\'email est invalide.', 400);
}

$sql = "UPDATE enseignant
        SET nom = :nom, prenom = :prenom, email = :email, departement = :departement, grade = :grade
        WHERE id_enseignant = :id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'         => $nom,
        ':prenom'      => $prenom,
        ':email'       => $email,
        ':departement' => $departement,
        ':grade'       => $grade,
        ':id'          => $id,
    ]);

    jsonSuccess(
        ['id_enseignant' => $id, 'nom' => $nom, 'prenom' => $prenom, 'email' => $email, 'departement' => $departement, 'grade' => $grade],
        'Enseignant modifié avec succès.'
    );
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        jsonError('Cet email est déjà utilisé par un autre enseignant.', 409);
    }
    jsonError('Erreur lors de la modification : ' . $e->getMessage(), 500);
}
