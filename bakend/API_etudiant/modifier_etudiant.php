<?php

require_once 'bakend/config_cors.php';
require_once 'bakend/json_reponse.php ';
require_once 'bakend/config_database.php';
require_once 'bakend/classes_authentification.php';

Auth::checkRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Méthode non autorisée"], 405);
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['id_etudiant'])) {
    jsonResponse(["error" => "ID de l'étudiant manquant."], 400);
}

$db = Database::getConnection();
$stmt = $db->prepare("UPDATE etudiant SET num_etudiant = ?, nom = ?, prenom = ?, email = ?, filiere = ?, niveau = ? WHERE id_etudiant = ?");

try {
    $stmt->execute([
        $data['num_etudiant'] ?? null,
        $data['nom'] ?? null,
        $data['prenom'] ?? null,
        $data['email'] ?? null,
        $data['filiere'] ?? null,
        $data['niveau'] ?? null,
        $data['id_etudiant']
    ]);
    jsonResponse(["message" => "Étudiant mis à jour avec succès"], 200);
} catch (PDOException $e) {
    jsonResponse(["error" => "Erreur lors de la mise à jour."], 500);
}
