<?php

require_once __DIR__ . '/../utils/response.php';

class Auth
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($id, $role, $infos = [])
    {
        self::start();

        session_regenerate_id(true);

        $_SESSION['user'] = array_merge([
            'id'   => $id,
            'role' => $role,
        ], $infos);
    }

    public static function check()
    {
        self::start();
        return isset($_SESSION['user']);
    }

    public static function user()
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function role()
    {
        $user = self::user();
        return $user['role'] ?? null;
    }

    public static function logout()
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function requireLogin()
    {
        if (!self::check()) {
            jsonError('Vous devez être connecté pour accéder à cette ressource.', 401);
        }
    }

    public static function requireRole($rolesAutorises)
    {
        self::requireLogin();

        if (!is_array($rolesAutorises)) {
            $rolesAutorises = [$rolesAutorises];
        }

        if (!in_array(self::role(), $rolesAutorises, true)) {
            jsonError("Vous n'avez pas les droits nécessaires pour cette action.", 403);
        }
    }
}
