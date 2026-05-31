<?php
require_once '../../config/cors.php';
require_once '../../utils/response.php';
require_once '../../classes/Database.php';
require_once '../../classes/Auth.php';

Auth::checkAuthenticated();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Méthode non autorisée"], 405);
}

$db = Database::getConnection();
$stmt = $db->query("SELECT id_etudiant, num_etudiant, nom, prenom, email, filiere, niveau, date_inscription FROM etudiant ORDER BY nom ASC");
$etudiants = $stmt->fetchAll();

jsonResponse($etudiants, 200);
