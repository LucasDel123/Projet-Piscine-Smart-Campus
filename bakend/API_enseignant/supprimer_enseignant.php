<?php

require_once 'bakend/config_cors.php';
require_once 'bakend/json_reponse.php ';
require_once 'bakend/config_database.php';
require_once 'bakend/classes_authentification.php';

Auth::checkRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    jsonResponse(["error" => "Méthode non autorisée"], 405);
}

if (!isset($_GET['id'])) {
    jsonResponse(["error" => "ID de l'enseignant manquant. Utilisez ?id=X"], 400);
}

$db = Database::getConnection();

$stmt = $db->prepare("DELETE FROM enseignant WHERE id_enseignant = ?");

try {
    $stmt->execute([$_GET['id']]);
    jsonResponse(["message" => "Enseignant supprimé avec succès"], 200);
} catch (PDOException $e) {
    jsonResponse(["error" => "Impossible de supprimer cet enseignant (il est probablement assigné à des cours)."], 409);
}
