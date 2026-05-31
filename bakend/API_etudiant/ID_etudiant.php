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
    jsonResponse(["error" => "ID de l'étudiant manquant. Utilisez ?id=X"], 400);
}

$db = Database::getConnection();
$stmt = $db->prepare("SELECT id_etudiant, num_etudiant, nom, prenom, email, filiere, niveau, date_inscription FROM etudiant WHERE id_etudiant = ?");
$stmt->execute([$_GET['id']]);
$etudiant = $stmt->fetch();

if ($etudiant) {
    jsonResponse($etudiant, 200);
} else {
    jsonResponse(["error" => "Étudiant introuvable"], 404);
}
