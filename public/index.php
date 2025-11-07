<?php 

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AdminController;
use Controllers\LoginController;
use Controllers\UsuarioController;

$router = new Router();

// Rutas de autenticación
$router->get('/login', [LoginController::class, 'login']);
$router->post('/login', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

//Vista de administración
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/pedidos', [AdminController::class, 'pedidos']);

// Rutas para CRUD de Productos
$router->get('/admin/productos/crear', [AdminController::class, 'crearProducto']);
$router->post('/admin/productos/crear', [AdminController::class, 'crearProducto']);
$router->get('/admin/productos/editar', [AdminController::class, 'editarProducto']);
$router->post('/admin/productos/editar', [AdminController::class, 'editarProducto']);
$router->post('/admin/productos/eliminar', [AdminController::class, 'eliminarProducto']);

// Rutas para CRUD de Usuarios
$router->get('/admin/usuarios/crear', [AdminController::class, 'crearUsuario']);
$router->post('/admin/usuarios/crear', [AdminController::class, 'crearUsuario']);
$router->get('/admin/usuarios/editar', [AdminController::class, 'editarUsuario']);
$router->post('/admin/usuarios/editar', [AdminController::class, 'editarUsuario']);
$router->post('/admin/usuarios/eliminar', [AdminController::class, 'eliminarUsuario']);

// Rutas para CRUD de Categorías
$router->get('/admin/categorias/crear', [AdminController::class, 'crearCategoria']);
$router->post('/admin/categorias/crear', [AdminController::class, 'crearCategoria']);
$router->get('/admin/categorias/editar', [AdminController::class, 'editarCategoria']);
$router->post('/admin/categorias/editar', [AdminController::class, 'editarCategoria']);
$router->post('/admin/categorias/eliminar', [AdminController::class, 'eliminarCategoria']);
$router->post('/admin/categorias/estado', [AdminController::class, 'cambiarEstadoCategoria']);

// Rutas para CRUD de Subcategorías
$router->get('/admin/subcategorias/crear', [AdminController::class, 'crearSubcategoria']);
$router->post('/admin/subcategorias/crear', [AdminController::class, 'crearSubcategoria']);
$router->get('/admin/subcategorias/editar', [AdminController::class, 'editarSubcategoria']);
$router->post('/admin/subcategorias/editar', [AdminController::class, 'editarSubcategoria']);
$router->post('/admin/subcategorias/eliminar', [AdminController::class, 'eliminarSubcategoria']);
$router->post('/admin/subcategorias/estado', [AdminController::class, 'cambiarEstadoSubcategoria']);

// Ruta AJAX para obtener subcategorías por categoría
$router->post('/admin/subcategorias/obtener', [AdminController::class, 'obtenerSubcategorias']);

// Rutas para usuarios básicos
$router->get('/usuario', [UsuarioController::class, 'dashboard']);
$router->get('/usuario/categoria', [UsuarioController::class, 'verCategoria']);
$router->get('/usuario/subcategoria', [UsuarioController::class, 'verSubcategoria']);
$router->get('/usuario/producto', [UsuarioController::class, 'verProducto']);
$router->get('/usuario/pedidos', [UsuarioController::class, 'misPedidos']);
$router->get('/usuario/pedido', [UsuarioController::class, 'verPedido']);

// =================
// RUTAS API USUARIOS
// =================
$router->get('/api/usuarios/dashboard', [UsuarioController::class, 'apiDashboard']);
$router->get('/api/usuarios/categorias', [UsuarioController::class, 'apiCategorias']);
$router->get('/api/usuarios/categoria', [UsuarioController::class, 'apiCategoria']);
$router->get('/api/usuarios/subcategorias', [UsuarioController::class, 'apiSubcategorias']);
$router->get('/api/usuarios/subcategoria', [UsuarioController::class, 'apiSubcategoria']);
$router->get('/api/usuarios/productos', [UsuarioController::class, 'apiProductos']);
$router->get('/api/usuarios/producto', [UsuarioController::class, 'apiProducto']);

// Rutas para pedidos
$router->post('/api/usuarios/pedidos', [UsuarioController::class, 'crearPedido']);
$router->get('/api/usuarios/mis-pedidos', [UsuarioController::class, 'obtenerMisPedidos']);

// Rutas para notificaciones
$router->get('/api/usuarios/notificaciones', [UsuarioController::class, 'apiGetNotificaciones']);
$router->post('/api/usuarios/notificaciones/marcar-leida', [UsuarioController::class, 'apiMarcarNotificacionLeida']);
$router->post('/api/usuarios/notificaciones/marcar-todas-leidas', [UsuarioController::class, 'apiMarcarTodasLeidas']);

// =================
// RUTAS API ADMIN
// =================
$router->get('/api/admin/dashboard', [AdminController::class, 'apiDashboard']);
$router->get('/api/admin/pedidos', [AdminController::class, 'apiGetPedidos']);
$router->post('/api/admin/pedidos/aprobar', [AdminController::class, 'apiAprobarPedido']);
$router->post('/api/admin/pedidos/rechazar', [AdminController::class, 'apiRechazarPedido']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();