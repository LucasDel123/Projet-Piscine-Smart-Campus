<?php
require_once 'bakend/config_cors.php';
require_once 'bakend/json_reponse.php ';
require_once 'bakend/config_database.php';
require_once 'bakend/classes_authentification.php';

Auth::checkAuthenticated();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(["error" => "Méthode non autorisée"], 405);
}

$db = Database::getConnection();
$stmt = $db->query("SELECT id_etudiant, num_etudiant, nom, prenom, email, filiere, niveau, date_inscription FROM etudiant ORDER BY nom ASC");
$etudiants = $stmt->fetchAll();

jsonResponse($etudiants, 200);
