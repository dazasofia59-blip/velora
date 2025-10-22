<?php
session_start();

class Session
{
    // Verificar si el usuario está logueado
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    // Verificar si el usuario es administrador
    public static function isAdmin()
    {
        return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin';
    }

    // Redirigir si no está logueado
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header("Location: login.php");
            exit();
        }
    }

    // Redirigir si no es administrador
    public static function requireAdmin()
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            header("Location: index.php");
            exit();
        }
    }

    // Iniciar sesión
    public static function login($user_id, $username, $nombre, $rol)
    {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['user_rol'] = $rol;
        $_SESSION['login_time'] = time();
    }

    // Cerrar sesión
    public static function logout()
    {
        session_unset();
        session_destroy();
    }

    // Obtener información del usuario logueado
    public static function getUserInfo()
    {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'nombre' => $_SESSION['nombre'],
                'rol' => $_SESSION['user_rol']
            ];
        }
        return null;
    }
}
