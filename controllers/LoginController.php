<?php

namespace Controllers;

use MVC\Router;
use Model\Admin;

class LoginController {
    
    public static function login(Router $router) {
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if(!$email) {
                $alertas['error'][] = 'El email es obligatorio';
            }
            
            if(!$password) {
                $alertas['error'][] = 'La contraseña es obligatoria';
            }
            
            if(empty($alertas)) {
                // Buscar el usuario por email
                $usuario = Admin::where('email', $email);
                
                if($usuario && $usuario->verificarPassword($password)) {
                    if($usuario->puedeAcceder()) {
                        // Iniciar sesión
                        session_start();
                        $_SESSION['login'] = true;
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['rol'] = $usuario->rol;
                        
                        // Redireccionar según el rol del usuario
                        if($usuario->esAdministrador()) {
                            header('Location: /admin');
                        } else {
                            header('Location: /usuario');
                        }
                        exit;
                    } else {
                        $alertas['error'][] = 'Tu cuenta está inactiva';
                    }
                } else {
                    $alertas['error'][] = 'Email o contraseña incorrectos';
                }
            }
        }
        
        $router->render('auth/login', [
            'title' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }
    
    public static function logout() {
        session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: /login');
        exit;
    }
}
