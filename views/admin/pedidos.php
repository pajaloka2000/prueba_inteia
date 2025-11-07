<div class="admin-pedidos">
    <div class="dashboard-header">
        <h1>
            <i class="fas fa-clipboard-list"></i>
            Gestión de Pedidos
        </h1>
        <p>Administra y gestiona todos los pedidos del sistema</p>
        <div class="admin-info">
            <span class="admin-welcome">
                <i class="fas fa-user-shield"></i>
                Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Administrador'); ?>
            </span>
            <div class="admin-actions">
                <a href="/admin" class="btn btn-secondary btn-sm">
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
            <label for="filtro-estado">Filtrar por estado:</label>
            <select id="filtro-estado" class="form-control">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendientes</option>
                <option value="aprobado">Aprobados</option>
                <option value="rechazado">Rechazados</option>
                <option value="entregado">Entregados</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="filtro-categoria">Filtrar por categoría:</label>
            <select id="filtro-categoria" class="form-control">
                <option value="">Todas las categorías</option>
                <!-- Se cargarán dinámicamente -->
            </select>
        </div>

        <div class="filter-group">
            <button id="btn-actualizar" class="btn btn-primary">
                <i class="fas fa-sync-alt"></i>
                Actualizar
            </button>
        </div>
    </div>

    <!-- Estadísticas de Pedidos -->
    <div class="pedidos-stats">
        <div class="stat-card pendientes">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3 id="stat-pendientes">-</h3>
                <p>Pendientes</p>
            </div>
        </div>
        
        <div class="stat-card aprobados">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3 id="stat-aprobados">-</h3>
                <p>Aprobados</p>
            </div>
        </div>
        
        <div class="stat-card rechazados">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3 id="stat-rechazados">-</h3>
                <p>Rechazados</p>
            </div>
        </div>

        <div class="stat-card total-valor">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3 id="stat-total-valor">$0.00</h3>
                <p>Valor Total</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Pedidos -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-list"></i> Lista de Pedidos</h2>
        </div>

        <div class="table-container">
            <table class="admin-table pedidos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="pedidos-table-body">
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando pedidos...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Modal para aprobar/rechazar pedido -->
<div id="modal-accion-pedido" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-titulo">Gestionar Pedido</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <div id="pedido-detalles"></div>
            <div class="accion-form">
                <div class="form-group">
                    <label for="comentario-admin">Comentarios (opcional):</label>
                    <textarea id="comentario-admin" class="form-control" rows="3" placeholder="Agregar comentarios sobre la decisión..."></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button id="btn-aprobar" class="btn btn-success">
                <i class="fas fa-check"></i> Aprobar Pedido
            </button>
            <button id="btn-rechazar" class="btn btn-danger">
                <i class="fas fa-times"></i> Rechazar Pedido
            </button>
            <button id="btn-cancelar" class="btn btn-secondary">Cancelar</button>
        </div>
    </div>
</div>

<script>
// Variable global para el pedido actual
let pedidoActual = null;

// Funciones globales para gestión de pedidos
window.abrirModalPedido = function(pedidoId, accion) {
    const pedido = window.adminAPI.pedidos.find(p => p.id == pedidoId);
    if (!pedido) return;
    
    pedidoActual = pedido;
    
    const modal = document.getElementById('modal-accion-pedido');
    const titulo = document.getElementById('modal-titulo');
    const detalles = document.getElementById('pedido-detalles');
    
    titulo.textContent = accion === 'aprobar' ? 'Aprobar Pedido' : 'Rechazar Pedido';
    
    detalles.innerHTML = `
        <div class="pedido-info">
            <h4>Detalles del Pedido #${pedido.id}</h4>
            <p><strong>Usuario:</strong> ${pedido.usuario_nombre}</p>
            <p><strong>Producto:</strong> ${pedido.producto_nombre}</p>
            <p><strong>Categoría:</strong> ${pedido.categoria_nombre}</p>
            <p><strong>Cantidad:</strong> ${pedido.cantidad}</p>
            <p><strong>Total:</strong> $${pedido.total}</p>
            <p><strong>Fecha:</strong> ${pedido.fecha_pedido}</p>
            ${pedido.comentarios ? `<p><strong>Comentarios del usuario:</strong> ${pedido.comentarios}</p>` : ''}
        </div>
    `;
    
    // Mostrar/ocultar botones según la acción
    const btnAprobar = document.getElementById('btn-aprobar');
    const btnRechazar = document.getElementById('btn-rechazar');
    
    if (accion === 'aprobar') {
        btnAprobar.style.display = 'inline-block';
        btnRechazar.style.display = 'none';
    } else {
        btnAprobar.style.display = 'none';
        btnRechazar.style.display = 'inline-block';
    }
    
    modal.style.display = 'block';
};

window.cerrarModal = function() {
    document.getElementById('modal-accion-pedido').style.display = 'none';
    document.getElementById('comentario-admin').value = '';
    pedidoActual = null;
};

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal
    document.querySelector('.close').addEventListener('click', cerrarModal);
    document.getElementById('btn-cancelar').addEventListener('click', cerrarModal);
    
    // Aprobar pedido
    document.getElementById('btn-aprobar').addEventListener('click', async function() {
        if (!pedidoActual) return;
        
        const comentarios = document.getElementById('comentario-admin').value;
        try {
            await window.adminRenderer.aprobarPedido(pedidoActual.id, comentarios);
            cerrarModal();
        } catch (error) {
            alert('Error al aprobar pedido: ' + error.message);
        }
    });
    
    // Rechazar pedido
    document.getElementById('btn-rechazar').addEventListener('click', async function() {
        if (!pedidoActual) return;
        
        const comentarios = document.getElementById('comentario-admin').value;
        try {
            await window.adminRenderer.rechazarPedido(pedidoActual.id, comentarios);
            cerrarModal();
        } catch (error) {
            alert('Error al rechazar pedido: ' + error.message);
        }
    });
    
    // Actualizar lista
    document.getElementById('btn-actualizar').addEventListener('click', function() {
        if (window.adminRenderer) {
            window.adminRenderer.cargarPedidos();
        }
    });
});
</script>