<?php

namespace Model;

class Notificacion extends ActiveRecord {
    protected static $tabla = 'notificaciones';
    protected static $columnasDB = ['id', 'usuario_id', 'tipo', 'referencia_id', 'mensaje', 'leida', 'fecha_creacion', 'fecha_lectura'];

    public $id;
    public $usuario_id;
    public $tipo;
    public $referencia_id;
    public $mensaje;
    public $leida;
    public $fecha_creacion;
    public $fecha_lectura;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? null;
        $this->tipo = $args['tipo'] ?? 'sistema';
        $this->referencia_id = $args['referencia_id'] ?? null;
        $this->mensaje = $args['mensaje'] ?? '';
        $this->leida = $args['leida'] ?? false;
        $this->fecha_creacion = $args['fecha_creacion'] ?? date('Y-m-d H:i:s');
        $this->fecha_lectura = $args['fecha_lectura'] ?? null;
    }

    // Validación
    public function validar() {
        if (!$this->usuario_id) {
            self::$alertas['error'][] = 'El usuario es obligatorio';
        }

        if (!$this->tipo) {
            self::$alertas['error'][] = 'El tipo de notificación es obligatorio';
        }

        if (!in_array($this->tipo, ['pedido_aprobado', 'pedido_rechazado', 'pedido_entregado', 'sistema'])) {
            self::$alertas['error'][] = 'El tipo de notificación no es válido';
        }

        if (!$this->mensaje) {
            self::$alertas['error'][] = 'El mensaje es obligatorio';
        }

        return self::$alertas;
    }

    // Método para marcar como leída
    public function marcarComoLeida() {
        $this->leida = true;
        $this->fecha_lectura = date('Y-m-d H:i:s');
        return $this->guardar();
    }

    // Método estático para crear notificación fácilmente
    public static function crearNotificacion($usuario_id, $tipo, $mensaje, $referencia_id = null) {
        $notificacion = new Notificacion([
            'usuario_id' => $usuario_id,
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'referencia_id' => $referencia_id
        ]);

        $alertas = $notificacion->validar();
        
        if (empty($alertas)) {
            return $notificacion->guardar();
        }
        
        return false;
    }

    // Método para obtener notificaciones de un usuario
    public static function obtenerPorUsuario($usuario_id, $solo_no_leidas = false) {
        $condicion_leida = $solo_no_leidas ? "AND leida = 0" : "";
        
        $query = "SELECT * FROM " . self::$tabla . " 
                 WHERE usuario_id = " . self::$db->escape_string($usuario_id) . " 
                 $condicion_leida
                 ORDER BY fecha_creacion DESC";
        
        return self::consultarSQL($query);
    }

    // Método para contar notificaciones no leídas
    public static function contarNoLeidasPorUsuario($usuario_id) {
        $query = "SELECT COUNT(*) as total FROM " . self::$tabla . " 
                 WHERE usuario_id = " . self::$db->escape_string($usuario_id) . " 
                 AND leida = 0";
        
        $resultado = self::$db->query($query);
        if ($resultado && $resultado->num_rows > 0) {
            $data = $resultado->fetch_assoc();
            return (int) $data['total'];
        }
        
        return 0;
    }

    // Método para marcar todas las notificaciones como leídas
    public static function marcarTodasComoLeidas($usuario_id) {
        $query = "UPDATE " . self::$tabla . " 
                 SET leida = 1, fecha_lectura = NOW() 
                 WHERE usuario_id = " . self::$db->escape_string($usuario_id) . " 
                 AND leida = 0";
        
        return self::$db->query($query);
    }

    // Método para obtener notificaciones recientes (últimas 24 horas)
    public static function obtenerRecientes($usuario_id, $horas = 24) {
        $query = "SELECT * FROM " . self::$tabla . " 
                 WHERE usuario_id = " . self::$db->escape_string($usuario_id) . " 
                 AND fecha_creacion >= DATE_SUB(NOW(), INTERVAL $horas HOUR)
                 ORDER BY fecha_creacion DESC";
        
        return self::consultarSQL($query);
    }

    // Método para limpiar notificaciones antiguas
    public static function limpiarAntiguas($dias = 30) {
        $query = "DELETE FROM " . self::$tabla . " 
                 WHERE fecha_creacion <= DATE_SUB(NOW(), INTERVAL $dias DAY)";
        
        return self::$db->query($query);
    }

    // Formatear fecha para mostrar
    public function formatearFecha() {
        if (!$this->fecha_creacion) return 'Fecha no disponible';
        
        try {
            $fecha = new \DateTime($this->fecha_creacion);
            $ahora = new \DateTime();
            $diferencia = $ahora->diff($fecha);
            
            if ($diferencia->days == 0) {
                if ($diferencia->h == 0) {
                    return $diferencia->i == 0 ? 'Ahora mismo' : "Hace {$diferencia->i} minuto(s)";
                } else {
                    return "Hace {$diferencia->h} hora(s)";
                }
            } elseif ($diferencia->days == 1) {
                return 'Ayer';
            } elseif ($diferencia->days < 7) {
                return "Hace {$diferencia->days} día(s)";
            } else {
                return $fecha->format('d/m/Y');
            }
        } catch (\Exception $e) {
            return 'Fecha no disponible';
        }
    }

    // Obtener icono según el tipo
    public function obtenerIcono() {
        $iconos = [
            'pedido_aprobado' => 'fas fa-check-circle text-success',
            'pedido_rechazado' => 'fas fa-times-circle text-danger',
            'pedido_entregado' => 'fas fa-truck text-info',
            'sistema' => 'fas fa-info-circle text-primary'
        ];
        
        return $iconos[$this->tipo] ?? 'fas fa-bell text-secondary';
    }

    // Obtener estilo CSS según el tipo
    public function obtenerEstilo() {
        $estilos = [
            'pedido_aprobado' => 'success',
            'pedido_rechazado' => 'danger',
            'pedido_entregado' => 'info',
            'sistema' => 'primary'
        ];
        
        return $estilos[$this->tipo] ?? 'secondary';
    }
}