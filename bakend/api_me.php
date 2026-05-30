<?php
require_once 'bakend/config_cors.php';
require_once 'bakend/json_reponse.php ';
require_once 'bakend/config_database.php';
require_once 'bakend/classes_authentification.php';

Auth::startSession();

if (!isset($_SESSION['user_id'])) {
    jsonResponse(["error" => "Non connecté."], 401);
}

$db = Database::getConnection();
$role = $_SESSION['role'];
$id = $_SESSION['user_id'];

$table = '';
$idColumn = '';
if ($role === 'etudiant') { $table = 'Etudiant'; $idColumn = 'id_etudiant'; }
elseif ($role === 'enseignant') { $table = 'Enseignant'; $idColumn = 'id_enseignant'; }
elseif ($role === 'admin') { $table = 'Administrateur'; $idColumn = 'id_admin'; }

$stmt = $db->prepare("SELECT * FROM $table WHERE $idColumn = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($user) {
    unset($user['mdp']);
    jsonResponse([
        "user" => $user,
        "role" => $role
    ], 200);
} else {
    session_destroy();
    jsonResponse(["error" => "Utilisateur introuvable."], 404);
}
