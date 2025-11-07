<?php

namespace Model;

class Subcategoria extends ActiveRecord {
    protected static $tabla = 'subcategorias';
    protected static $columnasDB = ['id', 'nombre', 'estado', 'categoria_id'];

    public $id;
    public $nombre;
    public $estado;
    public $categoria_id;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->estado = $args['estado'] ?? 'activa';
        $this->categoria_id = $args['categoria_id'] ?? null;
    }

    // Método para obtener la categoría padre
    public function obtenerCategoria() {
        $query = "SELECT * FROM categorias WHERE id = " . self::$db->escape_string($this->categoria_id);
        $resultado = self::consultarSQL($query);
        return !empty($resultado) ? array_shift($resultado) : null;
    }

    // Método para obtener todos los productos de esta subcategoría
    public function obtenerProductos() {
        $query = "SELECT p.* FROM productos p 
                 INNER JOIN productos_subcategorias ps ON p.id = ps.producto_id 
                 WHERE ps.subcategoria_id = " . self::$db->escape_string($this->id);
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Método para contar productos asociados
    public function contarProductos() {
        $query = "SELECT COUNT(*) as total FROM productos_subcategorias WHERE subcategoria_id = " . self::$db->escape_string($this->id);
        $resultado = self::$db->query($query);
        $fila = $resultado->fetch_assoc();
        return $fila['total'];
    }

    // Método para activar/desactivar subcategoría y sus productos
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
            // Si se desactiva la subcategoría, desactivar todos los productos asociados
            if ($nuevoEstado === 'inactiva') {
                // Actualizar productos que están en la tabla de relaciones productos_subcategorias
                $query = "UPDATE productos p 
                         INNER JOIN productos_subcategorias ps ON p.id = ps.producto_id 
                         SET p.estado = '" . self::$db->escape_string($estadoProducto) . "' 
                         WHERE ps.subcategoria_id = " . self::$db->escape_string($this->id);
                $resultado = self::$db->query($query);
                
                if (!$resultado) {
                    self::$alertas['error'][] = 'Error al desactivar productos asociados a subcategoría: ' . self::$db->error;
                    return false;
                }
                
                // Debug: verificar cuántos productos se actualizaron
                $productosAfectados = self::$db->affected_rows;
                if ($productosAfectados == 0) {
                    self::$alertas['info'][] = "No se encontraron productos en la tabla de relaciones para esta subcategoría.";
                } else {
                    self::$alertas['success'][] = "Se desactivaron $productosAfectados productos asociados a la subcategoría.";
                }
            }
            // Si se activa la subcategoría, activar productos asociados (solo si la categoría padre está activa)
            else if ($nuevoEstado === 'activa') {
                $categoria = $this->obtenerCategoria();
                if ($categoria && $categoria->estado === 'activa') {
                    // Actualizar productos que están en la tabla de relaciones productos_subcategorias
                    $query = "UPDATE productos p 
                             INNER JOIN productos_subcategorias ps ON p.id = ps.producto_id 
                             SET p.estado = '" . self::$db->escape_string($estadoProducto) . "' 
                             WHERE ps.subcategoria_id = " . self::$db->escape_string($this->id);
                    $resultado = self::$db->query($query);
                    
                    if (!$resultado) {
                        self::$alertas['error'][] = 'Error al activar productos asociados a subcategoría: ' . self::$db->error;
                        return false;
                    }
                    
                    $productosAfectados = self::$db->affected_rows;
                    if ($productosAfectados > 0) {
                        self::$alertas['success'][] = "Se activaron $productosAfectados productos asociados a la subcategoría.";
                    }
                } else {
                    // Si la categoría padre está inactiva, no se pueden activar los productos
                    if ($categoria && $categoria->estado !== 'activa') {
                        self::$alertas['warning'][] = 'No se pueden activar los productos porque la categoría padre está inactiva.';
                    }
                }
            }
            
            // Guardar cambios en la subcategoría
            $guardar = $this->guardar();
            
            if (!$guardar) {
                self::$alertas['error'][] = 'Error al guardar los cambios en la subcategoría';
                return false;
            }
            
            return true;
            
        } catch (\Exception $e) {
            self::$alertas['error'][] = 'Error al cambiar estado de subcategoría: ' . $e->getMessage();
            return false;
        }
    }
    
    // Validación
    public function validar() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El nombre de la subcategoría es obligatorio';
        }
        
        if (!$this->categoria_id) {
            self::$alertas['error'][] = 'Debe seleccionar una categoría';
        }
        
        if (!in_array($this->estado, ['activa', 'inactiva'])) {
            self::$alertas['error'][] = 'El estado debe ser activa o inactiva';
        }
        
        return self::$alertas;
    }
}