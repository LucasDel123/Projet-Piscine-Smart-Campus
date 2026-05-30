<?php

class Auth {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 86400,
                'path' => '/',
                'domain' => 'localhost', 
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax' 
            ]);
            session_start();
        }
    }

    public static function checkAuthenticated() {
        self::startSession();
        if (!isset($_SESSION['user_id'])) {
            jsonResponse(["error" => "Accès non autorisé. Veuillez vous connecter."], 401);
        }
    }

    public static function checkRole($requiredRole) {
        self::checkAuthenticated();
        if ($_SESSION['role'] !== $requiredRole) {
            jsonResponse(["error" => "Accès refusé. Rôle insuffisant."], 403);
        }
    }
}
