<?php

namespace Model;

class Admin extends ActiveRecord {
    protected static $tabla = 'admins';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'rol', 'estado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $rol;
    public $estado;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->rol = $args['rol'] ?? 'basico';
        $this->estado = $args['estado'] ?? 'activo';
    }

    // Método para verificar si el usuario puede acceder al sistema
    public function puedeAcceder() {
        return $this->estado === 'activo';
    }

    // Método para verificar si el usuario es administrador
    public function esAdministrador() {
        return $this->rol === 'administrador';
    }

    // Método para verificar si el usuario es básico
    public function esBasico() {
        return $this->rol === 'basico';
    }

    // Método para verificar permisos de CRUD
    public function tienePermisoCRUD($seccion) {
        // El administrador tiene permisos completos en todas las secciones
        if ($this->esAdministrador()) {
            return true;
        }

        // El usuario básico no tiene permisos de CRUD
        return false;
    }

    // Método para verificar permisos de consulta
    public function tienePermisoConsulta($seccion) {
        // Todos los usuarios activos pueden consultar
        return $this->puedeAcceder();
    }

    // Método para verificar si puede modificar un usuario específico
    public function puedeModificarUsuario($usuario_id) {
        // El administrador puede modificar cualquier usuario
        if ($this->esAdministrador()) {
            return true;
        }

        // El usuario básico solo puede modificar su propia cuenta
        return $this->id == $usuario_id;
    }

    // Método para hashear la contraseña antes de guardar
    public function hashPassword() {
        if ($this->password) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        }
    }

    // Método para verificar contraseña
    public function verificarPassword($password) {
        return password_verify($password, $this->password);
    }

    // Sobrescribir el método guardar para hashear la contraseña
    public function guardar() {
        // Solo hashear si la contraseña no está ya hasheada
        if ($this->password && !password_get_info($this->password)['algo']) {
            $this->hashPassword();
        }
        
        return parent::guardar();
    }

    // Método para obtener los roles disponibles
    public static function getRolesDisponibles() {
        return [
            'administrador' => 'Administrador',
            'basico' => 'Básico'
        ];
    }

    // Método para obtener los estados disponibles
    public static function getEstadosDisponibles() {
        return [
            'activo' => 'Activo',
            'inactivo' => 'Inactivo'
        ];
    }

    // Método para validar el usuario
    public function validar() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        
        if($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'El email no es válido';
        }
        
        if(!$this->password && !$this->id) {
            self::$alertas['error'][] = 'La contraseña es obligatoria';
        }
        
        if($this->password && strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if(!$this->rol) {
            self::$alertas['error'][] = 'El rol es obligatorio';
        }
        
        if($this->rol && !in_array($this->rol, ['administrador', 'basico'])) {
            self::$alertas['error'][] = 'El rol debe ser administrador o básico';
        }
        
        if(!$this->estado) {
            self::$alertas['error'][] = 'El estado es obligatorio';
        }
        
        if($this->estado && !in_array($this->estado, ['activo', 'inactivo'])) {
            self::$alertas['error'][] = 'El estado debe ser activo o inactivo';
        }
        
        return self::$alertas;
    }
}