<?php

require_once 'bakend/config_cors.php';
require_once 'bakend/json_reponse.php ';
require_once 'bakend/config_database.php';
require_once 'bakend/classes_authentification.php';

Auth::checkRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Méthode non autorisée"], 405);
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['email']) || empty($data['nom']) || empty($data['prenom'])) {
    jsonResponse(["error" => "Nom, prénom et email sont obligatoires."], 400);
}

$db = Database::getConnection();

$stmtCheck = $db->prepare("SELECT id_enseignant FROM enseignant WHERE email = ?");
$stmtCheck->execute([$data['email']]);
if ($stmtCheck->fetch()) {
    jsonResponse(["error" => "Cet email est déjà utilisé."], 409);
}

$defaultPassword = password_hash('smartcampus2026', PASSWORD_DEFAULT);

$stmt = $db->prepare("INSERT INTO enseignant (nom, prenom, email, mdp, departement, grade) VALUES (?, ?, ?, ?, ?, ?)");

try {
    $stmt->execute([
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $defaultPassword,
        $data['departement'] ?? null,
        $data['grade'] ?? null
    ]);
    jsonResponse(["message" => "Enseignant créé avec succès", "id" => $db->lastInsertId()], 201);
} catch (PDOException $e) {
    jsonResponse(["error" => "Erreur lors de la création."], 500);
}
