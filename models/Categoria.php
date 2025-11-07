<?php

namespace Model;

class Categoria extends ActiveRecord {
    protected static $tabla = 'categorias';
    protected static $columnasDB = ['id', 'nombre', 'estado', 'presupuesto'];

    public $id;
    public $nombre;
    public $estado;
    public $presupuesto;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->estado = $args['estado'] ?? 'activa';
        $this->presupuesto = $args['presupuesto'] ?? 0.00;
    }

    // Método para obtener todas las subcategorías de esta categoría
    public function obtenerSubcategorias() {
        $query = "SELECT * FROM subcategorias WHERE categoria_id = " . self::$db->escape_string($this->id);
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Método para obtener todos los productos de esta categoría
    public function obtenerProductos() {
        $query = "SELECT * FROM productos WHERE categoria_id = " . self::$db->escape_string($this->id);
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Método para activar/desactivar categoría y sus elementos relacionados
    public function cambiarEstado($nuevoEstado) {
        // Verificar que la conexión a la base de datos existe
        if (!self::$db) {
            self::$alertas['error'][] = 'Error: No hay conexión a la base de datos';
            return false;
        }
        
        $this->estado = $nuevoEstado;
        
        // Determinar el estado correspondiente para productos
        $estadoProducto = ($nuevoEstado === 'activa') ? 'activo' : 'inactivo';
        
        try {
            // Actualizar subcategorías asociadas
            $query = "UPDATE subcategorias SET estado = '" . self::$db->escape_string($nuevoEstado) . "' WHERE categoria_id = " . self::$db->escape_string($this->id);
            $resultado1 = self::$db->query($query);
            
            if (!$resultado1) {
                self::$alertas['error'][] = 'Error al actualizar subcategorías: ' . self::$db->error;
                return false;
            }
            
            // Actualizar productos asociados directamente de la categoría
            $query = "UPDATE productos SET estado = '" . self::$db->escape_string($estadoProducto) . "' WHERE categoria_id = " . self::$db->escape_string($this->id);
            $resultado2 = self::$db->query($query);
            
            if (!$resultado2) {
                self::$alertas['error'][] = 'Error al actualizar productos: ' . self::$db->error;
                return false;
            }
            
            // Si hay productos asociados a subcategorías de esta categoría, también actualizarlos
            $query = "UPDATE productos p 
                     INNER JOIN productos_subcategorias ps ON p.id = ps.producto_id 
                     INNER JOIN subcategorias s ON ps.subcategoria_id = s.id 
                     SET p.estado = '" . self::$db->escape_string($estadoProducto) . "' 
                     WHERE s.categoria_id = " . self::$db->escape_string($this->id);
            $resultado3 = self::$db->query($query);
            
            // Este query puede fallar si no existe la tabla productos_subcategorias, pero no es crítico
            
            // Guardar cambios en la categoría
            $guardar = $this->guardar();
            
            if (!$guardar) {
                self::$alertas['error'][] = 'Error al guardar los cambios en la categoría';
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            self::$alertas['error'][] = 'Error al cambiar estado: ' . $e->getMessage();
            return false;
        }
    }
    
    // Validación
    public function validar() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre de la categoría es obligatorio';
        }
        
        if (!in_array($this->estado, ['activa', 'inactiva'])) {
            self::$alertas['error'][] = 'El estado debe ser activa o inactiva';
        }
        
        // Validar presupuesto
        if (!is_numeric($this->presupuesto) || $this->presupuesto < 0) {
            self::$alertas['error'][] = 'El presupuesto debe ser un número válido mayor o igual a 0';
        }
        
        return self::$alertas;
    }
}