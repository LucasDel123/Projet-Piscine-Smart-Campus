<?php
require_once 'bakend/config_cors.php';
require_once '../../utils/response.php';
require_once '../../classes/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Méthode non autorisée"], 405);
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['email']) || empty($data['mdp']) || empty($data['nom']) || empty($data['prenom'])) {
    jsonResponse(["error" => "Veuillez remplir tous les champs obligatoires."], 400);
}

$db = Database::getConnection();

$stmt = $db->prepare("SELECT id_etudiant FROM Etudiant WHERE email = ?");
$stmt->execute([$data['email']]);
if ($stmt->fetch()) {
    jsonResponse(["error" => "Cet email est déjà utilisé."], 409);
}

$hashedPassword = password_hash($data['mdp'], PASSWORD_DEFAULT);

$insertStmt = $db->prepare("
    INSERT INTO Etudiant (num_etudiant, nom, prenom, email, mdp, filiere, niveau, date_inscription) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
");

try {
    $insertStmt->execute([
        $data['num_etudiant'] ?? null,
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $hashedPassword,
        $data['filiere'] ?? null,
        $data['niveau'] ?? null
    ]);
    jsonResponse(["message" => "Inscription réussie."], 201);
} catch (PDOException $e) {
    jsonResponse(["error" => "Erreur lors de l'inscription."], 500);
}
