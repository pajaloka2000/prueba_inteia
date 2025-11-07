<?php

namespace Model;

class Pedido extends ActiveRecord {
    protected static $tabla = 'pedidos';
    protected static $columnasDB = ['id', 'usuario_id', 'producto_id', 'categoria_id', 'cantidad', 'precio_unitario', 'total', 'estado', 'fecha_pedido', 'comentarios'];

    public $id;
    public $usuario_id;
    public $producto_id;
    public $categoria_id;
    public $cantidad;
    public $precio_unitario;
    public $total;
    public $estado;
    public $fecha_pedido;
    public $comentarios;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = $args['usuario_id'] ?? null;
        $this->producto_id = $args['producto_id'] ?? null;
        $this->categoria_id = $args['categoria_id'] ?? null;
        $this->cantidad = $args['cantidad'] ?? 1;
        $this->precio_unitario = $args['precio_unitario'] ?? 0.00;
        $this->total = $args['total'] ?? 0.00;
        $this->estado = $args['estado'] ?? 'pendiente';
        $this->fecha_pedido = $args['fecha_pedido'] ?? date('Y-m-d H:i:s');
        $this->comentarios = $args['comentarios'] ?? '';
    }

    // Método para calcular el total automáticamente
    public function calcularTotal() {
        $this->total = floatval($this->cantidad) * floatval($this->precio_unitario);
        return $this->total;
    }

    // Método para verificar si hay presupuesto disponible
    public function verificarPresupuestoDisponible() {
        error_log("=== VERIFICANDO PRESUPUESTO DISPONIBLE ===");
        error_log("Categoría ID: " . $this->categoria_id);
        error_log("Total del pedido: " . $this->total);
        
        if (!$this->categoria_id || !$this->total) {
            error_log("ERROR: Categoría ID o total no válidos");
            return false;
        }

        $query = "SELECT presupuesto_disponible FROM vista_presupuesto_categorias WHERE id = " . self::$db->escape_string($this->categoria_id);
        error_log("Query: " . $query);
        
        $resultado = self::$db->query($query);
        
        if ($resultado && $resultado->num_rows > 0) {
            $categoria = $resultado->fetch_assoc();
            error_log("Presupuesto disponible en BD: " . $categoria['presupuesto_disponible']);
            error_log("Total pedido: " . $this->total);
            
            $suficiente = floatval($categoria['presupuesto_disponible']) >= floatval($this->total);
            error_log("¿Suficiente presupuesto? " . ($suficiente ? 'SÍ' : 'NO'));
            
            return $suficiente;
        }
        
        error_log("ERROR: No se pudo ejecutar query o no hay resultados");
        return false;
    }

    // Método para obtener el producto asociado
    public function obtenerProducto() {
        return Producto::find($this->producto_id);
    }

    // Método para obtener la categoría asociada
    public function obtenerCategoria() {
        return Categoria::find($this->categoria_id);
    }

    // Método para obtener el usuario que hizo el pedido
    public function obtenerUsuario() {
        return Admin::find($this->usuario_id);
    }

    // Método para obtener pedidos por usuario
    public static function obtenerPedidosPorUsuario($usuario_id) {
        $query = "SELECT p.*, pr.nombre as producto_nombre, c.nombre as categoria_nombre 
                 FROM pedidos p 
                 INNER JOIN productos pr ON p.producto_id = pr.id 
                 INNER JOIN categorias c ON p.categoria_id = c.id 
                 WHERE p.usuario_id = " . self::$db->escape_string($usuario_id) . " 
                 ORDER BY p.fecha_pedido DESC";
        return self::consultarSQL($query);
    }

    // Método para obtener pedidos por categoría
    public static function obtenerPedidosPorCategoria($categoria_id) {
        $query = "SELECT p.*, pr.nombre as producto_nombre, u.nombre as usuario_nombre 
                 FROM pedidos p 
                 INNER JOIN productos pr ON p.producto_id = pr.id 
                 INNER JOIN admins u ON p.usuario_id = u.id 
                 WHERE p.categoria_id = " . self::$db->escape_string($categoria_id) . " 
                 ORDER BY p.fecha_pedido DESC";
        return self::consultarSQL($query);
    }

    // Método para aprobar pedido
    public function aprobar() {
        if ($this->verificarPresupuestoDisponible()) {
            $this->estado = 'aprobado';
            return $this->guardar();
        } else {
            self::$alertas['error'][] = 'No hay presupuesto suficiente para aprobar este pedido';
            return false;
        }
    }

    // Método para rechazar pedido
    public function rechazar($comentario = '') {
        $this->estado = 'rechazado';
        if ($comentario) {
            $this->comentarios = $comentario;
        }
        return $this->guardar();
    }

    // Sobrescribir el método guardar para calcular total automáticamente
    public function guardar() {
        $this->calcularTotal();
        return parent::guardar();
    }

    // Validación
    public function validar() {
        if (!$this->usuario_id) {
            self::$alertas['error'][] = 'El usuario es obligatorio';
        }

        if (!$this->producto_id) {
            self::$alertas['error'][] = 'El producto es obligatorio';
        }

        if (!$this->categoria_id) {
            self::$alertas['error'][] = 'La categoría es obligatoria';
        }

        if (!$this->cantidad || $this->cantidad <= 0) {
            self::$alertas['error'][] = 'La cantidad debe ser mayor a 0';
        }

        if (!is_numeric($this->precio_unitario) || $this->precio_unitario <= 0) {
            self::$alertas['error'][] = 'El precio unitario debe ser mayor a 0';
        }

        if (!in_array($this->estado, ['pendiente', 'aprobado', 'rechazado', 'entregado'])) {
            self::$alertas['error'][] = 'El estado del pedido no es válido';
        }

        // Validar que hay presupuesto disponible
        if ($this->estado === 'pendiente' || $this->estado === 'aprobado') {
            if (!$this->verificarPresupuestoDisponible()) {
                self::$alertas['error'][] = 'No hay presupuesto suficiente en la categoría para este pedido';
            }
        }

        return self::$alertas;
    }
}