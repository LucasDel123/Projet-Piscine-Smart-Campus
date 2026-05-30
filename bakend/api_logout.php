<?php

require_once 'bakend/config_cors.php';
require_once 'bakend/json_reponse.php';
require_once 'bakend/classes_authentification.php';

Auth::startSession();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

jsonResponse(["message" => "Déconnexion réussie."], 200);
