<?php

namespace Model;

class Producto extends ActiveRecord {
    protected static $tabla = 'productos';
    protected static $columnasDB = ['id', 'nombre', 'estado', 'categoria_id', 'subcategoria_id', 'precio'];

    public $id;
    public $nombre;
    public $estado;
    public $categoria_id;
    public $subcategoria_id;
    public $precio;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->estado = $args['estado'] ?? 'activo';
        $this->categoria_id = $args['categoria_id'] ?? null;
        $this->subcategoria_id = $args['subcategoria_id'] ?? null;
        $this->precio = $args['precio'] ?? 0.00;
    }

    // Método para obtener la categoría del producto
    public function obtenerCategoria() {
        $query = "SELECT * FROM categorias WHERE id = " . self::$db->escape_string($this->categoria_id);
        $resultado = self::consultarSQL($query);
        return !empty($resultado) ? array_shift($resultado) : null;
    }

    // Método para obtener la subcategoría del producto
    public function obtenerSubcategoria() {
        if (!$this->subcategoria_id) return null;
        $query = "SELECT * FROM subcategorias WHERE id = " . self::$db->escape_string($this->subcategoria_id);
        $resultado = self::consultarSQL($query);
        return !empty($resultado) ? array_shift($resultado) : null;
    }

    // Método para verificar si el producto puede estar activo
    public function puedeEstarActivo() {
        $categoria = $this->obtenerCategoria();
        if (!$categoria || $categoria->estado !== 'activa') {
            return false;
        }
        
        // Si tiene subcategoría asignada, verificar que esté activa
        if ($this->subcategoria_id) {
            $subcategoria = $this->obtenerSubcategoria();
            if (!$subcategoria || $subcategoria->estado !== 'activa') {
                return false;
            }
        }
        
        return true;
    }

    // Sobrescribir el método guardar para validar el estado
    public function guardar() {
        // Si se intenta activar el producto, verificar que sea posible
        if ($this->estado === 'activo' && !$this->puedeEstarActivo()) {
            $this->estado = 'inactivo';
        }
        
        return parent::guardar();
    }

    // Método para validar el producto
    public function validar() {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El nombre del producto es obligatorio';
        }
        
        if(!$this->categoria_id) {
            self::$alertas['error'][] = 'La categoría es obligatoria';
        }
        
        // Validar que si se selecciona subcategoría, pertenezca a la categoría
        if($this->subcategoria_id && $this->categoria_id) {
            $subcategoria = $this->obtenerSubcategoria();
            if($subcategoria && $subcategoria->categoria_id != $this->categoria_id) {
                self::$alertas['error'][] = 'La subcategoría no pertenece a la categoría seleccionada';
            }
        }
        
        if(!$this->estado) {
            self::$alertas['error'][] = 'El estado es obligatorio';
        }
        
        if($this->estado && !in_array($this->estado, ['activo', 'inactivo'])) {
            self::$alertas['error'][] = 'El estado debe ser activo o inactivo';
        }
        
        // Validar precio
        if (!is_numeric($this->precio) || $this->precio < 0) {
            self::$alertas['error'][] = 'El precio debe ser un número válido mayor o igual a 0';
        }
        
        return self::$alertas;
    }
}