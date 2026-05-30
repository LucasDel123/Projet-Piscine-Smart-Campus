<?php

require_once '../../config/cors.php';
require_once '../../utils/response.php';
require_once '../../classes/Auth.php';

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
