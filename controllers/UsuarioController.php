<?php

namespace Controllers;

use MVC\Router;
use Model\Admin;
use Model\Producto;
use Model\Categoria;
use Model\Subcategoria;
use Model\Pedido;
use Model\Notificacion;

class UsuarioController {
    
    public static function dashboard(Router $router) {
        // Verificar que el usuario esté logueado
        session_start();
        self::verificarUsuario();
        
        // Solo renderizar la vista - los datos se cargarán desde la API
        $router->render('usuario/dashboard', [
            'title' => 'Panel de Usuario - Vista de Productos'
        ]);
    }
    
    // Método para ver detalles de una categoría específica
    public static function verCategoria(Router $router) {
        session_start();
        self::verificarUsuario();
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: /usuario');
            return;
        }
        
        $categoria = Categoria::find($id);
        
        if (!$categoria || $categoria->estado !== 'activa') {
            header('Location: /usuario');
            return;
        }
        
        // Obtener subcategorías y productos de esta categoría
        $subcategorias = self::obtenerSubcategoriasPorCategoria($id);
        $productos = self::obtenerProductosPorCategoria($id);
        
        $router->render('usuario/categoria', [
            'title' => 'Categoría: ' . $categoria->nombre,
            'categoria' => $categoria,
            'subcategorias' => $subcategorias,
            'productos' => $productos
        ]);
    }
    
    // Método para ver detalles de una subcategoría específica
    public static function verSubcategoria(Router $router) {
        session_start();
        self::verificarUsuario();
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: /usuario');
            return;
        }
        
        $subcategoria = Subcategoria::find($id);
        
        if (!$subcategoria || $subcategoria->estado !== 'activa') {
            header('Location: /usuario');
            return;
        }
        
        // Obtener categoría padre y productos de esta subcategoría
        $categoria = $subcategoria->obtenerCategoria();
        $productos = $subcategoria->obtenerProductos();
        
        // Filtrar solo productos activos
        $productos = array_filter($productos, function($producto) {
            return $producto->estado === 'activo';
        });
        
        $router->render('usuario/subcategoria', [
            'title' => 'Subcategoría: ' . $subcategoria->nombre,
            'subcategoria' => $subcategoria,
            'categoria' => $categoria,
            'productos' => $productos
        ]);
    }
    
    // Método para ver detalles de un producto específico
    public static function verProducto(Router $router) {
        session_start();
        self::verificarUsuario();
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: /usuario');
            return;
        }
        
        // Solo verificar que el producto existe, los datos se cargarán desde la API
        $producto_data = self::obtenerProductoCompleto($id);
        
        if (!$producto_data) {
            header('Location: /usuario');
            return;
        }
        
        // Renderizar solo la vista - los datos se cargarán desde JavaScript/API
        $router->render('usuario/producto', [
            'title' => 'Producto: ' . $producto_data['producto']->nombre
        ]);
    }

    // Método para ver mis pedidos
    public static function misPedidos(Router $router) {
        session_start();
        self::verificarUsuario();
        
        $router->render('usuario/pedidos', [
            'title' => 'Mis Pedidos'
        ]);
    }

    // Método para ver detalles de un pedido específico
    public static function verPedido(Router $router) {
        session_start();
        self::verificarUsuario();
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: /usuario/pedidos');
            return;
        }
        
        $router->render('usuario/pedido', [
            'title' => 'Detalle del Pedido'
        ]);
    }
    
    // Métodos auxiliares para obtener datos filtrados
    private static function obtenerCategoriasActivas() {
        $query = "SELECT c.*, 
                         COALESCE(rp.presupuesto_asignado, c.presupuesto) as presupuesto_asignado,
                         COALESCE(rp.presupuesto_usado, 0) as presupuesto_usado,
                         COALESCE(rp.presupuesto_disponible, c.presupuesto) as presupuesto_disponible
                  FROM categorias c
                  LEFT JOIN resumen_presupuesto rp ON c.id = rp.categoria_id
                  WHERE c.estado = 'activa' 
                  ORDER BY c.nombre";
        return Categoria::SQL($query);
    }
    
    private static function obtenerSubcategoriasActivas() {
        $query = "SELECT s.*, c.nombre as categoria_nombre 
                 FROM subcategorias s 
                 INNER JOIN categorias c ON s.categoria_id = c.id 
                 WHERE s.estado = 'activa' AND c.estado = 'activa' 
                 ORDER BY c.nombre, s.nombre";
        return Subcategoria::SQL($query);
    }
    
    private static function obtenerProductosActivos() {
        $query = "SELECT p.*, c.nombre as categoria_nombre, s.nombre as subcategoria_nombre 
                 FROM productos p 
                 INNER JOIN categorias c ON p.categoria_id = c.id 
                 LEFT JOIN subcategorias s ON p.subcategoria_id = s.id 
                 WHERE p.estado = 'activo' AND c.estado = 'activa' 
                 ORDER BY c.nombre, s.nombre, p.nombre";
        return Producto::SQL($query);
    }
    
    private static function obtenerSubcategoriasPorCategoria($categoria_id) {
        $query = "SELECT * FROM subcategorias 
                 WHERE categoria_id = " . intval($categoria_id) . " 
                 AND estado = 'activa' 
                 ORDER BY nombre";
        return Subcategoria::SQL($query);
    }
    
    private static function obtenerProductosPorCategoria($categoria_id) {
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                 FROM productos p 
                 INNER JOIN categorias c ON p.categoria_id = c.id 
                 WHERE p.categoria_id = " . intval($categoria_id) . " 
                 AND p.estado = 'activo' AND c.estado = 'activa' 
                 ORDER BY p.nombre";
        return Producto::SQL($query);
    }
    
    // Método auxiliar para obtener producto con información completa
    private static function obtenerProductoCompleto($id) {
        // Obtener producto básico
        $producto = Producto::find($id);
        
        if (!$producto || $producto->estado !== 'activo') {
            return null;
        }
        
        // Obtener categoría
        $categoria = Categoria::find($producto->categoria_id);
        
        if (!$categoria || $categoria->estado !== 'activa') {
            return null;
        }
        
        // Obtener subcategoría si existe
        $subcategoria = null;
        if ($producto->subcategoria_id) {
            $subcategoria = Subcategoria::find($producto->subcategoria_id);
        }
        
        // Obtener todas las subcategorías de la categoría para mostrar opciones
        $subcategorias = self::obtenerSubcategoriasPorCategoria($producto->categoria_id);
        
        return [
            'producto' => $producto,
            'categoria' => $categoria,
            'subcategoria' => $subcategoria,
            'subcategorias' => $subcategorias
        ];
    }
    
    // Método auxiliar para verificar que el usuario esté logueado
    private static function verificarUsuario() {
        if (!isset($_SESSION['login']) || !$_SESSION['login']) {
            header('Location: /login');
            exit;
        }
        
        // Verificar que el usuario tenga acceso
        $usuario = Admin::find($_SESSION['id']);
        if (!$usuario || !$usuario->puedeAcceder()) {
            header('Location: /login');
            exit;
        }
    }

    // =================
    // MÉTODOS API (JSON)
    // =================

    // API: Obtener dashboard con datos generales
    public static function apiDashboard() {
        session_start();
        self::verificarUsuarioAPI();
        
        $categorias = self::obtenerCategoriasActivas();
        $subcategorias = self::obtenerSubcategoriasActivas();
        $productos = self::obtenerProductosActivosConInfo();
        
        // Debug temporal
        error_log("API Dashboard - Productos count: " . count($productos));
        if (count($productos) > 0) {
            error_log("Primer producto: " . print_r($productos[0], true));
        }
        
        $response = [
            'success' => true,
            'data' => [
                'categorias' => $categorias,
                'subcategorias' => $subcategorias,
                'productos' => $productos,
                'stats' => [
                    'total_categorias' => count($categorias),
                    'total_subcategorias' => count($subcategorias),
                    'total_productos' => count($productos)
                ]
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // API: Obtener categorías activas
    public static function apiCategorias() {
        session_start();
        self::verificarUsuarioAPI();
        
        $categorias = self::obtenerCategoriasActivas();
        
        $response = [
            'success' => true,
            'data' => $categorias
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // API: Obtener detalles de una categoría específica
    public static function apiCategoria() {
        session_start();
        self::verificarUsuarioAPI();
        
        $id = $_GET['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            $response = [
                'success' => false,
                'message' => 'ID de categoría inválido'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        $categoria = Categoria::find($id);
        
        if (!$categoria || $categoria->estado !== 'activa') {
            $response = [
                'success' => false,
                'message' => 'Categoría no encontrada o inactiva'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        $subcategorias = self::obtenerSubcategoriasPorCategoria($id);
        $productos = self::obtenerProductosPorCategoria($id);
        
        $response = [
            'success' => true,
            'data' => [
                'categoria' => $categoria,
                'subcategorias' => $subcategorias,
                'productos' => $productos
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // API: Obtener subcategorías activas
    public static function apiSubcategorias() {
        session_start();
        self::verificarUsuarioAPI();
        
        $subcategorias = self::obtenerSubcategoriasActivas();
        
        $response = [
            'success' => true,
            'data' => $subcategorias
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // API: Obtener detalles de una subcategoría específica
    public static function apiSubcategoria() {
        session_start();
        self::verificarUsuarioAPI();
        
        $id = $_GET['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            $response = [
                'success' => false,
                'message' => 'ID de subcategoría inválido'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        $subcategoria = Subcategoria::find($id);
        
        if (!$subcategoria || $subcategoria->estado !== 'activa') {
            $response = [
                'success' => false,
                'message' => 'Subcategoría no encontrada o inactiva'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        $categoria = $subcategoria->obtenerCategoria();
        $productos = $subcategoria->obtenerProductos();
        
        // Filtrar solo productos activos
        $productos = array_filter($productos, function($producto) {
            return $producto->estado === 'activo';
        });
        
        $response = [
            'success' => true,
            'data' => [
                'subcategoria' => $subcategoria,
                'categoria' => $categoria,
                'productos' => array_values($productos) // Re-indexar array
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // API: Obtener productos activos
    public static function apiProductos() {
        session_start();
        self::verificarUsuarioAPI();
        
        $productos = self::obtenerProductosActivosConInfo();
        
        $response = [
            'success' => true,
            'data' => $productos
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // API: Obtener detalles de un producto específico
    public static function apiProducto() {
        session_start();
        self::verificarUsuarioAPI();
        
        $id = $_GET['id'] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            $response = [
                'success' => false,
                'message' => 'ID de producto inválido'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        $producto_data = self::obtenerProductoCompleto($id);
        
        if (!$producto_data) {
            $response = [
                'success' => false,
                'message' => 'Producto no encontrado o inactivo'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            return;
        }
        
        $response = [
            'success' => true,
            'data' => [
                'producto' => $producto_data['producto'],
                'categoria' => $producto_data['categoria'],
                'subcategoria' => $producto_data['subcategoria'],
                'subcategorias_disponibles' => $producto_data['subcategorias']
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Método auxiliar para verificar usuario en API
    private static function verificarUsuarioAPI() {
        if (!isset($_SESSION['login']) || !$_SESSION['login']) {
            $response = [
                'success' => false,
                'message' => 'Usuario no autenticado'
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        
        // Para la API, solo verificamos que el usuario esté logueado
        // El control de permisos específicos se puede hacer por endpoint si es necesario
    }

    // Método auxiliar mejorado para obtener productos con información completa
    private static function obtenerProductosActivosConInfo() {
        // Usar consulta SQL directa con la base de datos
        $query = "SELECT p.*, c.nombre as categoria_nombre, s.nombre as subcategoria_nombre,
                         c.presupuesto as categoria_presupuesto,
                         COALESCE(rp.presupuesto_disponible, c.presupuesto) as presupuesto_disponible
                 FROM productos p 
                 INNER JOIN categorias c ON p.categoria_id = c.id 
                 LEFT JOIN subcategorias s ON p.subcategoria_id = s.id
                 LEFT JOIN resumen_presupuesto rp ON c.id = rp.categoria_id
                 WHERE p.estado = 'activo' AND c.estado = 'activa' 
                 AND (s.id IS NULL OR s.estado = 'activa')
                 ORDER BY c.nombre, s.nombre, p.nombre";
        
        error_log("Ejecutando query: " . $query);
        
        // Usar reflexión para acceder a la propiedad estática protegida
        $reflection = new \ReflectionClass('\Model\ActiveRecord');
        $dbProperty = $reflection->getProperty('db');
        $dbProperty->setAccessible(true);
        $db = $dbProperty->getValue();
        
        $resultado = $db->query($query);
        
        $productos = [];
        while ($row = $resultado->fetch_assoc()) {
            $productos[] = $row;
        }
        
        error_log("Productos con info count: " . count($productos));
        if (count($productos) > 0) {
            error_log("Primer producto con JOIN: " . print_r($productos[0], true));
        }
        
        return $productos;
    }

    // =================
    // MÉTODOS PARA PEDIDOS
    // =================

    // Crear un nuevo pedido
    public static function crearPedido() {
        session_start();
        self::verificarUsuarioAPI();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            error_log("Datos recibidos para pedido: " . print_r($input, true));
            
            $producto_id = filter_var($input['producto_id'] ?? null, FILTER_VALIDATE_INT);
            $cantidad = filter_var($input['cantidad'] ?? 1, FILTER_VALIDATE_INT);
            $comentarios = $input['comentarios'] ?? '';
            
            error_log("Datos procesados - Producto ID: $producto_id, Cantidad: $cantidad");
            
            if (!$producto_id || !$cantidad || $cantidad <= 0) {
                echo json_encode(['success' => false, 'message' => 'Datos de pedido inválidos', 'debug' => [
                    'producto_id' => $producto_id,
                    'cantidad' => $cantidad
                ]]);
                return;
            }
            
            // Obtener producto y verificar que esté activo
            $producto = Producto::find($producto_id);
            error_log("Producto encontrado: " . ($producto ? 'Sí' : 'No'));
            
            if (!$producto) {
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
                return;
            }
            
            // Verificar propiedades del producto usando array notation para evitar errores de propiedades
            $producto_array = (array) $producto;
            error_log("Producto array: " . print_r($producto_array, true));
            
            if ($producto_array['estado'] !== 'activo') {
                echo json_encode(['success' => false, 'message' => 'Producto no disponible']);
                return;
            }
            
            // Verificar que la categoría esté activa
            $categoria = Categoria::find($producto_array['categoria_id']);
            if (!$categoria) {
                echo json_encode(['success' => false, 'message' => 'Categoría no encontrada']);
                return;
            }
            
            $categoria_array = (array) $categoria;
            if ($categoria_array['estado'] !== 'activa') {
                echo json_encode(['success' => false, 'message' => 'Categoría no disponible']);
                return;
            }
            
            // Crear pedido
            $pedido_data = [
                'usuario_id' => $_SESSION['id'],
                'producto_id' => $producto_id,
                'categoria_id' => $producto_array['categoria_id'],
                'cantidad' => $cantidad,
                'precio_unitario' => floatval($producto_array['precio'] ?? 0),
                'comentarios' => $comentarios
            ];
            
            error_log("Datos del pedido a crear: " . print_r($pedido_data, true));
            error_log("Usuario en sesión: " . $_SESSION['id']);
            
            $pedido = new Pedido($pedido_data);
            
            // Calcular total y loggear
            $pedido->calcularTotal();
            error_log("Total calculado: " . $pedido->total);
            
            // Verificar presupuesto antes de validar
            $presupuestoDisponible = $pedido->verificarPresupuestoDisponible();
            error_log("¿Hay presupuesto disponible? " . ($presupuestoDisponible ? 'SÍ' : 'NO'));
            
            // Validar pedido
            $alertas = $pedido->validar();
            error_log("Alertas de validación: " . print_r($alertas, true));
            
            if (!empty($alertas)) {
                echo json_encode(['success' => false, 'message' => 'Error de validación', 'errores' => $alertas]);
                return;
            }
            
            // Guardar pedido
            $resultado = $pedido->guardar();
            error_log("Resultado del guardado: " . ($resultado ? 'Éxito' : 'Falló'));
            
            if ($resultado) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Pedido creado exitosamente',
                    'pedido' => [
                        'id' => $pedido->id,
                        'total' => $pedido->total,
                        'estado' => $pedido->estado
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear el pedido']);
            }
            
        } catch (\Exception $e) {
            error_log("Error en crearPedido: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // Obtener pedidos del usuario actual
    public static function obtenerMisPedidos() {
        session_start();
        self::verificarUsuarioAPI();
        
        header('Content-Type: application/json');
        
        try {
            $pedidos = Pedido::obtenerPedidosPorUsuario($_SESSION['id']);
            
            echo json_encode([
                'success' => true,
                'data' => $pedidos
            ]);
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // API: Obtener notificaciones del usuario
    public static function apiGetNotificaciones() {
        session_start();
        self::verificarUsuarioAPI();
        
        header('Content-Type: application/json');
        
        try {
            $solo_no_leidas = isset($_GET['solo_no_leidas']) && $_GET['solo_no_leidas'] === 'true';
            
            // Obtener notificaciones
            $notificaciones = Notificacion::obtenerPorUsuario($_SESSION['id'], $solo_no_leidas);
            
            // Contar no leídas
            $total_no_leidas = Notificacion::contarNoLeidasPorUsuario($_SESSION['id']);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'notificaciones' => $notificaciones,
                    'total_no_leidas' => $total_no_leidas
                ]
            ]);
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // API: Marcar notificación como leída
    public static function apiMarcarNotificacionLeida() {
        session_start();
        self::verificarUsuarioAPI();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $notificacion_id = filter_var($input['notificacion_id'] ?? null, FILTER_VALIDATE_INT);
            
            if (!$notificacion_id) {
                echo json_encode(['success' => false, 'message' => 'ID de notificación inválido']);
                return;
            }
            
            // Obtener la notificación
            $notificacion = Notificacion::find($notificacion_id);
            
            if (!$notificacion) {
                echo json_encode(['success' => false, 'message' => 'Notificación no encontrada']);
                return;
            }
            
            // Verificar que pertenece al usuario
            if ($notificacion->usuario_id != $_SESSION['id']) {
                echo json_encode(['success' => false, 'message' => 'No tienes permisos para esta acción']);
                return;
            }
            
            // Marcar como leída
            $resultado = $notificacion->marcarComoLeida();
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Notificación marcada como leída'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al marcar notificación']);
            }
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // API: Marcar todas las notificaciones como leídas
    public static function apiMarcarTodasLeidas() {
        session_start();
        self::verificarUsuarioAPI();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            $resultado = Notificacion::marcarTodasComoLeidas($_SESSION['id']);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Todas las notificaciones marcadas como leídas'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al marcar notificaciones']);
            }
            
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }
}
?>
