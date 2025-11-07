<div class="usuario-pedidos">
    <div class="dashboard-header">
        <h1>
            <i class="fas fa-clipboard-list"></i>
            Mis Pedidos
        </h1>
        <p>Consulta el estado de tus pedidos realizados</p>
        <div class="user-info">
            <span class="user-welcome">
                <i class="fas fa-user"></i>
                Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>
            </span>
            <div class="user-actions">
                <a href="/usuario" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Dashboard
                </a>
                <a href="/logout" class="btn btn-secondary btn-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros de Pedidos -->
    <div class="pedidos-filters">
        <div class="filter-group">
            <label for="filtro-estado-usuario">Filtrar por estado:</label>
            <select id="filtro-estado-usuario" class="form-control">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendientes</option>
                <option value="aprobado">Aprobados</option>
                <option value="rechazado">Rechazados</option>
                <option value="entregado">Entregados</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="filtro-fecha">Filtrar por fecha:</label>
            <input type="date" id="filtro-fecha" class="form-control">
        </div>

        <div class="filter-group">
            <button id="btn-actualizar-pedidos" class="btn btn-primary">
                <i class="fas fa-sync-alt"></i>
                Actualizar
            </button>
        </div>
    </div>

    <!-- Contenedor para los pedidos -->
    <section class="pedidos-section">
        <div class="section-header">
            <h2><i class="fas fa-list"></i> Historial de Pedidos</h2>
        </div>
        
        <div id="mis-pedidos-container" class="pedidos-container">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Cargando mis pedidos...</p>
            </div>
        </div>
    </section>

    <!-- Resumen de pedidos -->
    <div class="pedidos-resumen">
        <h3>Resumen de Pedidos</h3>
        <div class="resumen-cards">
            <div class="resumen-card pendientes">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-info">
                    <h4 id="total-pendientes">-</h4>
                    <p>Pendientes</p>
                </div>
            </div>
            
            <div class="resumen-card aprobados">
                <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-info">
                    <h4 id="total-aprobados">-</h4>
                    <p>Aprobados</p>
                </div>
            </div>
            
            <div class="resumen-card rechazados">
                <div class="card-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="card-info">
                    <h4 id="total-rechazados">-</h4>
                    <p>Rechazados</p>
                </div>
            </div>
            
            <div class="resumen-card total-gastado">
                <div class="card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="card-info">
                    <h4 id="total-gastado">$0.00</h4>
                    <p>Total Gastado</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función global para cargar mis pedidos
async function cargarMisPedidos() {
    if (window.usuarioRenderer) {
        await window.usuarioRenderer.cargarMisPedidos();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    cargarMisPedidos();
    cargarResumenPedidos();
    
    // Event listeners para filtros
    document.getElementById('filtro-estado-usuario').addEventListener('change', filtrarPedidos);
    document.getElementById('filtro-fecha').addEventListener('change', filtrarPedidos);
    document.getElementById('btn-actualizar-pedidos').addEventListener('click', function() {
        cargarMisPedidos();
        cargarResumenPedidos();
    });
});

// Función para filtrar pedidos
function filtrarPedidos() {
    const filtroEstado = document.getElementById('filtro-estado-usuario').value;
    const filtroFecha = document.getElementById('filtro-fecha').value;
    const pedidos = document.querySelectorAll('.pedido-item');
    
    pedidos.forEach(pedido => {
        const estado = pedido.dataset.estado;
        const fecha = pedido.dataset.fecha;
        
        let mostrar = true;
        
        // Filtrar por estado
        if (filtroEstado && estado !== filtroEstado) {
            mostrar = false;
        }
        
        // Filtrar por fecha
        if (filtroFecha && fecha !== filtroFecha) {
            mostrar = false;
        }
        
        pedido.style.display = mostrar ? 'block' : 'none';
    });
}

async function cargarResumenPedidos() {
    try {
        const response = await fetch('/api/usuarios/mis-pedidos');
        const data = await response.json();
        
        if (data.success) {
            const pedidos = data.data;
            
            // Calcular totales
            const pendientes = pedidos.filter(p => p.estado === 'pendiente').length;
            const aprobados = pedidos.filter(p => p.estado === 'aprobado').length;
            const rechazados = pedidos.filter(p => p.estado === 'rechazado').length;
            const totalGastado = pedidos
                .filter(p => p.estado === 'aprobado')
                .reduce((sum, p) => sum + parseFloat(p.total || 0), 0);
            
            // Actualizar UI
            document.getElementById('total-pendientes').textContent = pendientes;
            document.getElementById('total-aprobados').textContent = aprobados;
            document.getElementById('total-rechazados').textContent = rechazados;
            document.getElementById('total-gastado').textContent = '$' + totalGastado.toLocaleString('es-ES', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    } catch (error) {
        console.error('Error al cargar resumen:', error);
    }
}
</script>