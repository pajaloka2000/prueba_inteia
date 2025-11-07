<div class="usuario-dashboard">
    <div class="dashboard-header">
        <h1>Panel de Usuario</h1>
        <p>Consulta de productos, categorías y subcategorías</p>
        <div class="user-info">
            <span class="user-welcome">
                <i class="fas fa-user"></i>
                Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>
            </span>
            <div class="user-actions">
                <!-- Botón de Notificaciones -->
                <div class="notificaciones-container">
                    <button id="btn-notificaciones" onclick="toggleNotificaciones()" class="btn btn-info btn-sm">
                        <i class="fas fa-bell"></i>
                        Notificaciones
                        <span id="notificaciones-badge" class="badge" style="display: none;">0</span>
                    </button>
                    
                    <!-- Panel de Notificaciones -->
                    <div id="notificaciones-panel" class="notificaciones-panel" style="display: none;">
                        <div class="notificaciones-header">
                            <h4>Notificaciones</h4>
                            <button onclick="marcarTodasLasNotificacionesLeidas()" class="btn-marcar-todas">
                                Marcar todas como leídas
                            </button>
                        </div>
                        <div id="notificaciones-lista" class="notificaciones-lista">
                            <!-- Las notificaciones se cargarán aquí -->
                        </div>
                    </div>
                </div>
                
                <a href="/usuario/pedidos" class="btn btn-primary btn-sm">
                    <i class="fas fa-clipboard-list"></i>
                    Mis Pedidos
                </a>
                <a href="/logout" class="btn btn-secondary btn-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <!-- Contenedor para los datos de la API -->
    <div id="dashboard-content">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Cargando datos desde la API...</p>
        </div>
    </div>

</div>

<script>
// Funciones para navegación desde las tarjetas API
window.verCategoria = async function(id) {
    try {
        console.log('Navegando a categoría:', id);
        window.location.href = `/usuario/categoria?id=${id}`;
    } catch (error) {
        alert('Error al cargar la categoría: ' + error.message);
    }
};

window.verProducto = async function(id) {
    try {
        console.log('Navegando a producto:', id);
        window.location.href = `/usuario/producto?id=${id}`;
    } catch (error) {
        alert('Error al cargar el producto: ' + error.message);
    }
};
</script>
