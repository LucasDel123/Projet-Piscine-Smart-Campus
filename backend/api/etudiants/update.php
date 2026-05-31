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

$stmt = $pdo->prepare("SELECT id_etudiant FROM etudiant WHERE id_etudiant = :id");
$stmt->execute([':id' => $id]);
if (!$stmt->fetch()) {
    jsonError('Étudiant introuvable.', 404);
}

$nom    = trim($input['nom'] ?? '');
$prenom = trim($input['prenom'] ?? '');
$email  = trim($input['email'] ?? '');
$niveau = trim($input['niveau'] ?? '');
$groupe = (int) ($input['groupe'] ?? 0);

if ($nom === '' || $prenom === '' || $email === '') {
    jsonError('Le nom, le prénom et l\'email sont obligatoires.', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonError('Le format de l\'email est invalide.', 400);
}

if ($groupe <= 0) {
    jsonError('Le groupe doit être un entier positif.', 400);
}

$sql = "UPDATE etudiant
        SET nom = :nom, prenom = :prenom, email = :email, niveau = :niveau, groupe = :groupe
        WHERE id_etudiant = :id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom'    => $nom,
        ':prenom' => $prenom,
        ':email'  => $email,
        ':niveau' => $niveau,
        ':groupe' => $groupe,
        ':id'     => $id,
    ]);

    jsonSuccess(
        [
            'id_etudiant' => $id,
            'nom'         => $nom,
            'prenom'      => $prenom,
            'email'       => $email,
            'niveau'      => $niveau,
            'groupe'      => $groupe,
        ],
        'Étudiant modifié avec succès.'
    );

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        jsonError('Cet email est déjà utilisé par un autre étudiant.', 409);
    }
    jsonError('Erreur lors de la modification : ' . $e->getMessage(), 500);
}
