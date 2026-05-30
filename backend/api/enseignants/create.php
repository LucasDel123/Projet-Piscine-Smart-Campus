<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../utils/response.php';

Auth::requireRole('administrateur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Méthode non autorisée. Utilisez POST.', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

$nom        = trim($input['nom'] ?? '');
$prenom     = trim($input['prenom'] ?? '');
$email      = trim($input['email'] ?? '');
$motDePasse = $input['mot_de_passe'] ?? 'smartcampus';
$departement= trim($input['departement'] ?? 'Non défini');
$grade      = trim($input['grade'] ?? '');

if ($nom === '' || $prenom === '' || $email === '') {
    jsonError('Le nom, le prénom et l\'email sont obligatoires.', 400);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Le format de l\'email est invalide.', 400);
}

$pdo = Database::getConnection();
$mdpHache = password_hash($motDePasse, PASSWORD_BCRYPT);

$sql = "INSERT INTO enseignant (nom, prenom, email, mdp, departement, grade)
        VALUES (:nom, :prenom, :email, :mdp, :departement, :grade)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'         => $nom,
        ':prenom'      => $prenom,
        ':email'       => $email,
        ':mdp'         => $mdpHache,
        ':departement' => $departement,
        ':grade'       => $grade,
    ]);

    jsonSuccess(
        [
            'id_enseignant' => $pdo->lastInsertId(),
            'nom'           => $nom,
            'prenom'        => $prenom,
            'email'         => $email,
            'departement'   => $departement,
            'grade'         => $grade,
        ],
        'Enseignant ajouté avec succès.',
        201
    );
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        jsonError('Cet email est déjà utilisé.', 409);
    }
    jsonError('Erreur lors de l\'ajout : ' . $e->getMessage(), 500);
}
