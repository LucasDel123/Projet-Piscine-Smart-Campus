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

if (empty($data['id_enseignant'])) {
    jsonResponse(["error" => "ID de l'enseignant manquant."], 400);
}

$db = Database::getConnection();
$stmt = $db->prepare("UPDATE enseignant SET nom = ?, prenom = ?, email = ?, departement = ?, grade = ? WHERE id_enseignant = ?");

try {
    $stmt->execute([
        $data['nom'] ?? null,
        $data['prenom'] ?? null,
        $data['email'] ?? null,
        $data['departement'] ?? null,
        $data['grade'] ?? null,
        $data['id_enseignant']
    ]);
    jsonResponse(["message" => "Enseignant mis à jour avec succès"], 200);
} catch (PDOException $e) {
    jsonResponse(["error" => "Erreur lors de la mise à jour."], 500);
}
