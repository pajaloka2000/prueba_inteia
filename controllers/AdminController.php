<?php

namespace Controllers;

use MVC\Router;
use Model\Admin;
use Model\Producto;
use Model\Categoria;
use Model\Subcategoria;
use Model\Pedido;
use Model\Notificacion;
use Exception;

class AdminController {
    
    public static function index(Router $router) {
        // Verificar que el usuario esté logueado y sea administrador
        session_start();
        self::verificarAdmin();
        
        // Solo pasamos datos básicos, el resto se carga vía API
        $router->render('admin/index', [
            'title' => 'Panel de Administración'
        ]);
    }
    
    // Métodos para CRUD de Productos
    public static function crearProducto(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $producto = new Producto();
        $categorias = Categoria::all();
        $subcategorias = Subcategoria::all();
        $alertas = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto->sincronizar($_POST);
            $alertas = $producto->validar();
            
            if (empty($alertas)) {
                $producto->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/producto/crear', [
            'title' => 'Crear Producto',
            'producto' => $producto,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'alertas' => $alertas
        ]);
    }
    
    public static function editarProducto(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: /admin');
        }
        
        $producto = Producto::find($id);
        $categorias = Categoria::all();
        $subcategorias = Subcategoria::all();
        $alertas = [];
        
        if (!$producto) {
            header('Location: /admin');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $producto->sincronizar($_POST);
            $alertas = $producto->validar();
            
            if (empty($alertas)) {
                $producto->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/producto/editar', [
            'title' => 'Editar Producto',
            'producto' => $producto,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'alertas' => $alertas
        ]);
    }
    
    public static function eliminarProducto() {
        session_start();
        self::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            
            if ($id) {
                $producto = Producto::find($id);
                if ($producto) {
                    $producto->eliminar();
                }
            }
        }
        
        header('Location: /admin');
    }
    
    // Métodos para CRUD de Usuarios
    public static function crearUsuario(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $usuario = new Admin();
        $alertas = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar();
            
            if (empty($alertas)) {
                $usuario->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/usuario/crear', [
            'title' => 'Crear Usuario',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    
    public static function editarUsuario(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: /admin');
        }
        
        $usuario = Admin::find($id);
        $alertas = [];
        
        if (!$usuario) {
            header('Location: /admin');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar();
            
            if (empty($alertas)) {
                $usuario->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/usuario/editar', [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    
    public static function eliminarUsuario() {
        session_start();
        self::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            
            if ($id) {
                $usuario = Admin::find($id);
                if ($usuario) {
                    $usuario->eliminar();
                }
            }
        }
        
        header('Location: /admin');
    }
    
    // Métodos para CRUD de Categorías
    public static function crearCategoria(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $categoria = new Categoria();
        $alertas = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria->sincronizar($_POST);
            $alertas = $categoria->validar();
            
            if (empty($alertas)) {
                $categoria->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/categoria/crear', [
            'title' => 'Crear Categoría',
            'categoria' => $categoria,
            'alertas' => $alertas
        ]);
    }
    
    public static function editarCategoria(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: /admin');
        }
        
        $categoria = Categoria::find($id);
        $alertas = [];
        
        if (!$categoria) {
            header('Location: /admin');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria->sincronizar($_POST);
            $alertas = $categoria->validar();
            
            if (empty($alertas)) {
                $categoria->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/categoria/editar', [
            'title' => 'Editar Categoría',
            'categoria' => $categoria,
            'alertas' => $alertas
        ]);
    }
    
    public static function eliminarCategoria() {
        session_start();
        self::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            
            if ($id) {
                $categoria = Categoria::find($id);
                if ($categoria) {
                    $categoria->eliminar();
                }
            }
        }
        
        header('Location: /admin');
    }
    
    public static function cambiarEstadoCategoria() {
        session_start();
        self::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $estado = $_POST['estado'];
            
            $id = filter_var($id, FILTER_VALIDATE_INT);
            
            if ($id && in_array($estado, ['activa', 'inactiva'])) {
                $categoria = Categoria::find($id);
                if ($categoria) {
                    $categoria->cambiarEstado($estado);
                }
            }
        }
        
        header('Location: /admin');
    }
    
    // Métodos para CRUD de Subcategorías
    public static function crearSubcategoria(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $subcategoria = new Subcategoria();
        $categorias = Categoria::all();
        $alertas = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subcategoria->sincronizar($_POST);
            $alertas = $subcategoria->validar();
            
            if (empty($alertas)) {
                $subcategoria->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/subcategoria/crear', [
            'title' => 'Crear Subcategoría',
            'subcategoria' => $subcategoria,
            'categorias' => $categorias,
            'alertas' => $alertas
        ]);
    }
    
    public static function editarSubcategoria(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: /admin');
        }
        
        $subcategoria = Subcategoria::find($id);
        $categorias = Categoria::all();
        $alertas = [];
        
        if (!$subcategoria) {
            header('Location: /admin');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subcategoria->sincronizar($_POST);
            $alertas = $subcategoria->validar();
            
            if (empty($alertas)) {
                $subcategoria->guardar();
                header('Location: /admin');
            }
        }
        
        $router->render('admin/subcategoria/editar', [
            'title' => 'Editar Subcategoría',
            'subcategoria' => $subcategoria,
            'categorias' => $categorias,
            'alertas' => $alertas
        ]);
    }
    
    public static function eliminarSubcategoria() {
        session_start();
        self::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);
            
            if ($id) {
                $subcategoria = Subcategoria::find($id);
                if ($subcategoria) {
                    $subcategoria->eliminar();
                }
            }
        }
        
        header('Location: /admin');
    }
    
    public static function cambiarEstadoSubcategoria() {
        session_start();
        self::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $estado = $_POST['estado'];
            
            $id = filter_var($id, FILTER_VALIDATE_INT);
            
            if ($id && in_array($estado, ['activa', 'inactiva'])) {
                $subcategoria = Subcategoria::find($id);
                if ($subcategoria) {
                    $subcategoria->cambiarEstado($estado);
                }
            }
        }
        
        header('Location: /admin');
    }
    
    // Método para obtener subcategorías de una categoría específica (AJAX)
    public static function obtenerSubcategorias() {
        session_start();
        self::verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria_id = $_POST['categoria_id'] ?? null;
            $categoria_id = filter_var($categoria_id, FILTER_VALIDATE_INT);
            
            if ($categoria_id) {
                $query = "SELECT * FROM subcategorias WHERE categoria_id = $categoria_id AND estado = 'activa'";
                $subcategorias = Subcategoria::SQL($query);
                
                header('Content-Type: application/json');
                echo json_encode($subcategorias);
                return;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode([]);
    }

    // =================
    // MÉTODOS API ADMIN
    // =================
    
    public static function apiDashboard() {
        session_start();
        self::verificarAdmin();
        
        header('Content-Type: application/json');
        
        try {
            // Obtener datos del dashboard - corregir consulta de usuarios
            $productos = Producto::all();
            $categorias = Categoria::all();
            $subcategorias = Subcategoria::all();
            
            // Obtener todos los usuarios (tanto admins como usuarios básicos)
            $query_usuarios = "SELECT * FROM admins ORDER BY created_at DESC";
            $usuarios = Admin::consultarSQL($query_usuarios);
            
            // Calcular presupuesto total de todas las categorías
            $presupuesto_total = 0;
            foreach ($categorias as $categoria) {
                $presupuesto_total += floatval($categoria->presupuesto ?? 0);
            }

            // Obtener información de presupuestos por categoría con cálculo correcto
            $query_presupuestos = "SELECT 
                c.id,
                c.nombre,
                c.presupuesto as presupuesto_asignado,
                COALESCE(SUM(CASE WHEN pe.estado IN ('aprobado', 'entregado') THEN pe.total ELSE 0 END), 0) as presupuesto_usado,
                (c.presupuesto - COALESCE(SUM(CASE WHEN pe.estado IN ('aprobado', 'entregado') THEN pe.total ELSE 0 END), 0)) as presupuesto_disponible,
                CASE 
                    WHEN c.presupuesto > 0 THEN 
                        (COALESCE(SUM(CASE WHEN pe.estado IN ('aprobado', 'entregado') THEN pe.total ELSE 0 END), 0) / c.presupuesto) * 100 
                    ELSE 0 
                END as porcentaje_usado,
                c.estado
                FROM categorias c
                LEFT JOIN productos pr ON pr.categoria_id = c.id
                LEFT JOIN pedidos pe ON pe.producto_id = pr.id
                GROUP BY c.id, c.nombre, c.presupuesto, c.estado
                ORDER BY c.nombre";
            
            $presupuestos_result = Categoria::consultarSQL($query_presupuestos);
            
            // Convertir objetos a arrays para facilitar el manejo
            $presupuestos_categorias = [];
            foreach ($presupuestos_result as $item) {
                $presupuestos_categorias[] = [
                    'id' => $item->id ?? 0,
                    'nombre' => $item->nombre ?? '',
                    'presupuesto_asignado' => floatval($item->presupuesto_asignado ?? 0),
                    'presupuesto_usado' => floatval($item->presupuesto_usado ?? 0),
                    'presupuesto_disponible' => floatval($item->presupuesto_disponible ?? 0),
                    'porcentaje_usado' => floatval($item->porcentaje_usado ?? 0),
                    'estado' => $item->estado ?? 'activa'
                ];
            }

            // Obtener estadísticas de pedidos - corregir la consulta
            $query_pedidos_stats = "SELECT 
                COUNT(*) as total_pedidos,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) as rechazados,
                SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as entregados,
                SUM(CASE WHEN estado IN ('aprobado', 'entregado') THEN total ELSE 0 END) as valor_aprobado
                FROM pedidos";
            
            $resultado_pedidos = Pedido::consultarSQL($query_pedidos_stats);
            $stats_pedidos = [];
            
            if (!empty($resultado_pedidos) && isset($resultado_pedidos[0])) {
                $first = $resultado_pedidos[0];
                $stats_pedidos = [
                    'total_pedidos' => intval($first->total_pedidos ?? 0),
                    'pendientes' => intval($first->pendientes ?? 0),
                    'aprobados' => intval($first->aprobados ?? 0),
                    'rechazados' => intval($first->rechazados ?? 0),
                    'entregados' => intval($first->entregados ?? 0),
                    'valor_aprobado' => floatval($first->valor_aprobado ?? 0)
                ];
            } else {
                $stats_pedidos = [
                    'total_pedidos' => 0,
                    'pendientes' => 0,
                    'aprobados' => 0,
                    'rechazados' => 0,
                    'entregados' => 0,
                    'valor_aprobado' => 0
                ];
            }
            
            $response = [
                'success' => true,
                'data' => [
                    'estadisticas' => [
                        'total_productos' => count($productos),
                        'total_categorias' => count($categorias),
                        'total_subcategorias' => count($subcategorias),
                        'total_usuarios' => count($usuarios),
                        'presupuesto_total' => $presupuesto_total,
                        'pedidos_pendientes' => $stats_pedidos['pendientes']
                    ],
                    'productos' => $productos,
                    'categorias' => $categorias,
                    'subcategorias' => $subcategorias,
                    'usuarios' => $usuarios,
                    'presupuestos_categorias' => $presupuestos_categorias,
                    'estadisticas_pedidos' => $stats_pedidos
                ]
            ];
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos del dashboard',
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    // Vista de gestión de pedidos
    public static function pedidos(Router $router) {
        session_start();
        self::verificarAdmin();
        
        $router->render('admin/pedidos', [
            'title' => 'Gestión de Pedidos'
        ]);
    }

    // API: Obtener todos los pedidos para administrador
    public static function apiGetPedidos() {
        session_start();
        self::verificarAdmin();
        
        header('Content-Type: application/json');
        
        try {
            // Obtener todos los pedidos con información relacionada
            $query = "SELECT p.*, 
                            pr.nombre as producto_nombre,
                            c.nombre as categoria_nombre,
                            u.nombre as usuario_nombre,
                            u.email as usuario_email
                     FROM pedidos p 
                     INNER JOIN productos pr ON p.producto_id = pr.id 
                     INNER JOIN categorias c ON p.categoria_id = c.id 
                     INNER JOIN admins u ON p.usuario_id = u.id 
                     ORDER BY p.fecha_pedido DESC";
            
            $pedidos = Pedido::consultarSQL($query);
            
            // Obtener estadísticas
            $stats = [
                'pendientes' => 0,
                'aprobados' => 0,
                'rechazados' => 0,
                'entregados' => 0,
                'total_valor' => 0
            ];
            
            foreach ($pedidos as $pedido) {
                $stats[$pedido->estado]++;
                if ($pedido->estado === 'aprobado' || $pedido->estado === 'entregado') {
                    $stats['total_valor'] += floatval($pedido->total);
                }
            }
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'pedidos' => $pedidos,
                    'estadisticas' => $stats
                ]
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener pedidos',
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    // API: Aprobar pedido
    public static function apiAprobarPedido() {
        session_start();
        self::verificarAdmin();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $pedido_id = filter_var($input['pedido_id'] ?? null, FILTER_VALIDATE_INT);
            $comentarios = $input['comentarios'] ?? '';
            
            if (!$pedido_id) {
                echo json_encode(['success' => false, 'message' => 'ID de pedido inválido']);
                return;
            }
            
            // Obtener el pedido
            $pedido = Pedido::find($pedido_id);
            if (!$pedido) {
                echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
                return;
            }
            
            // Verificar que esté pendiente
            if ($pedido->estado !== 'pendiente') {
                echo json_encode(['success' => false, 'message' => 'El pedido no está pendiente']);
                return;
            }
            
            // Aprobar el pedido (esto activará el trigger para actualizar presupuesto)
            $resultado = $pedido->aprobar();
            
            if ($resultado) {
                // Agregar comentarios del admin si los hay
                if ($comentarios) {
                    $pedido->comentarios = ($pedido->comentarios ? $pedido->comentarios . "\n\n" : '') . 
                                          "Comentarios del admin: " . $comentarios;
                    $pedido->guardar();
                }
                
                // Crear notificación para el usuario
                Notificacion::crearNotificacion($pedido->usuario_id, 'pedido_aprobado', 
                    "Tu pedido #{$pedido->id} ha sido aprobado", $pedido->id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Pedido aprobado exitosamente',
                    'pedido' => $pedido
                ]);
            } else {
                $alertas = $pedido->getAlertas();
                $mensaje = !empty($alertas) ? implode(', ', array_merge(...$alertas)) : 'Error al aprobar pedido';
                echo json_encode(['success' => false, 'message' => $mensaje]);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    // API: Rechazar pedido
    public static function apiRechazarPedido() {
        session_start();
        self::verificarAdmin();
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $pedido_id = filter_var($input['pedido_id'] ?? null, FILTER_VALIDATE_INT);
            $comentarios = $input['comentarios'] ?? '';
            
            if (!$pedido_id) {
                echo json_encode(['success' => false, 'message' => 'ID de pedido inválido']);
                return;
            }
            
            // Obtener el pedido
            $pedido = Pedido::find($pedido_id);
            if (!$pedido) {
                echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
                return;
            }
            
            // Verificar que esté pendiente
            if ($pedido->estado !== 'pendiente') {
                echo json_encode(['success' => false, 'message' => 'El pedido no está pendiente']);
                return;
            }
            
            // Rechazar el pedido
            $resultado = $pedido->rechazar($comentarios);
            
            if ($resultado) {
                // Crear notificación para el usuario
                Notificacion::crearNotificacion($pedido->usuario_id, 'pedido_rechazado', 
                    "Tu pedido #{$pedido->id} ha sido rechazado", $pedido->id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Pedido rechazado exitosamente',
                    'pedido' => $pedido
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al rechazar pedido']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }
    
    // Método auxiliar para verificar permisos de administrador
    private static function verificarAdmin() {
        if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'administrador') {
            header('Location: /login');
            exit;
        }
    }
}
?>