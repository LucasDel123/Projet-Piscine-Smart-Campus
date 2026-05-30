<?php
require_once 'bakend/config_cors.php';
require_once 'bakend/json_reponse.php';
require_once '../../classes/Database.php';
require_once '../../classes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(["error" => "Méthode non autorisée"], 405);
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['email']) || empty($data['mdp']) || empty($data['role'])) {
    jsonResponse(["error" => "Email, mot de passe et rôle requis."], 400);
}

$db = Database::getConnection();
$role = $data['role'];

$table = '';
$idColumn = '';
if ($role === 'etudiant') { $table = 'Etudiant'; $idColumn = 'id_etudiant'; }
elseif ($role === 'enseignant') { $table = 'Enseignant'; $idColumn = 'id_enseignant'; }
elseif ($role === 'admin') { $table = 'Administrateur'; $idColumn = 'id_admin'; }
else { jsonResponse(["error" => "Rôle invalide."], 400); }

$stmt = $db->prepare("SELECT * FROM $table WHERE email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch();

if ($user && password_verify($data['mdp'], $user['mdp'])) {
    Auth::startSession();
    $_SESSION['user_id'] = $user[$idColumn];
    $_SESSION['role'] = $role;

    unset($user['mdp']);
    
    jsonResponse([
        "message" => "Connexion réussie.",
        "user" => $user,
        "role" => $role
    ], 200);
} else {
    jsonResponse(["error" => "Identifiants incorrects."], 401);
}
