<?php

require_once 'bakend/config_cors.php';
require_once 'bakend/json_reponse.php ';
require_once 'bakend/config_database.php';
require_once 'bakend/classes_authentification.php';

Auth::checkAuthenticated();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Méthode non autorisée"], 405);
}

if (!isset($_GET['id'])) {
    jsonResponse(["error" => "ID de l'enseignant manquant. Utilisez ?id=X"], 400);
}

$db = Database::getConnection();
$stmt = $db->prepare("SELECT id_enseignant, nom, prenom, email, departement, grade FROM enseignant WHERE id_enseignant = ?");
$stmt->execute([$_GET['id']]);
$enseignant = $stmt->fetch();

if ($enseignant) {
    jsonResponse($enseignant, 200);
} else {
    jsonResponse(["error" => "Enseignant introuvable"], 404);
}
