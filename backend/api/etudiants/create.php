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
$niveau     = trim($input['niveau'] ?? 'ING1');
$groupe     = (int) ($input['groupe'] ?? 1);
$numEtu     = trim($input['num_etudiant'] ?? '');

if ($nom === '' || $prenom === '' || $email === '') {
    jsonError('Le nom, le prénom et l\'email sont obligatoires.', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Le format de l\'email est invalide.', 400);
}

if ($groupe <= 0) {
    jsonError('Le groupe doit être un entier positif.', 400);
}

if ($numEtu === '') {
    $numEtu = 'E' . date('Y') . rand(1000, 9999);
}

$pdo = Database::getConnection();

$mdpHache = password_hash($motDePasse, PASSWORD_BCRYPT);

$sql = "INSERT INTO etudiant (num_etudiant, nom, prenom, email, mdp, niveau, groupe, date_inscription)
        VALUES (:num, :nom, :prenom, :email, :mdp, :niveau, :groupe, CURDATE())";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':num'    => $numEtu,
        ':nom'    => $nom,
        ':prenom' => $prenom,
        ':email'  => $email,
        ':mdp'    => $mdpHache,
        ':niveau' => $niveau,
        ':groupe' => $groupe,
    ]);

    $nouvelId = $pdo->lastInsertId();

    jsonSuccess(
        [
            'id_etudiant'  => $nouvelId,
            'num_etudiant' => $numEtu,
            'nom'          => $nom,
            'prenom'       => $prenom,
            'email'        => $email,
            'niveau'       => $niveau,
            'groupe'       => $groupe,
        ],
        'Étudiant ajouté avec succès.',
        201
    );

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        jsonError('Cet email ou ce numéro étudiant est déjà utilisé.', 409);
    }
    jsonError('Erreur lors de l\'ajout : ' . $e->getMessage(), 500);
}
