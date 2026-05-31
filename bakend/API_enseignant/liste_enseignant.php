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
$stmt = $db->query("SELECT id_enseignant, nom, prenom, email, departement, grade FROM enseignant ORDER BY nom ASC");
$enseignants = $stmt->fetchAll();

jsonResponse($enseignants, 200);
