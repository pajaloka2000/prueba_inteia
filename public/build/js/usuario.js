/**
 * API Cliente para Usuario
 * Maneja todas las peticiones fetch a la API del usuario
 */

class UsuarioAPI {
    constructor() {
        this.baseURL = '/api/usuarios';
        this.headers = {
            'Content-Type': 'application/json',
        };
    }

    /**
     * Método genérico para realizar peticiones fetch
     */
    async request(endpoint, options = {}) {
        try {
            const url = `${this.baseURL}${endpoint}`;
            const config = {
                headers: this.headers,
                ...options
            };

            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                // Si hay errores específicos de validación, mostrarlos
                if (data.errores) {
                    const erroresTexto = Object.values(data.errores).flat().join('\n');
                    throw new Error(`${data.message}: ${erroresTexto}`);
                }
                throw new Error(data.message || 'Error en la respuesta del servidor');
            }

            return data;
        } catch (error) {
            console.error('Error en la petición API:', error);
            throw error;
        }
    }

    /**
     * Obtener datos del dashboard
     */
    async getDashboard() {
        return await this.request('/dashboard');
    }

    /**
     * Obtener todas las categorías activas
     */
    async getCategorias() {
        return await this.request('/categorias');
    }

    /**
     * Obtener detalles de una categoría específica
     */
    async getCategoria(id) {
        return await this.request(`/categoria?id=${id}`);
    }

    /**
     * Obtener todas las subcategorías activas
     */
    async getSubcategorias() {
        return await this.request('/subcategorias');
    }

    /**
     * Obtener detalles de una subcategoría específica
     */
    async getSubcategoria(id) {
        return await this.request(`/subcategoria?id=${id}`);
    }

    /**
     * Obtener todos los productos activos
     */
    async getProductos() {
        return await this.request('/productos');
    }

    /**
     * Obtener detalles de un producto específico
     */
    async getProducto(id) {
        return await this.request(`/producto?id=${id}`);
    }

    /**
     * Crear un nuevo pedido
     */
    async crearPedido(pedidoData) {
        return await this.request('/pedidos', {
            method: 'POST',
            body: JSON.stringify(pedidoData)
        });
    }

    /**
     * Obtener mis pedidos
     */
    async getMisPedidos() {
        return await this.request('/mis-pedidos');
    }

    /**
     * Obtener notificaciones
     */
    async getNotificaciones(soloNoLeidas = false) {
        const url = soloNoLeidas ? '/notificaciones?solo_no_leidas=true' : '/notificaciones';
        return await this.request(url);
    }

    /**
     * Marcar notificación como leída
     */
    async marcarNotificacionLeida(notificacionId) {
        return await this.request('/notificaciones/marcar-leida', {
            method: 'POST',
            body: JSON.stringify({ notificacion_id: notificacionId })
        });
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    async marcarTodasLasNotificacionesLeidas() {
        return await this.request('/notificaciones/marcar-todas-leidas', {
            method: 'POST'
        });
    }
}

/**
 * Utilidades para renderizar datos en el DOM
 */
class UsuarioRenderer {
    constructor() {
        this.api = new UsuarioAPI();
    }

    /**
     * Mostrar loading spinner
     */
    showLoading(container) {
        if (typeof container === 'string') {
            container = document.querySelector(container);
        }
        if (container) {
            container.innerHTML = `
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Cargando...</p>
                </div>
            `;
        }
    }

    /**
     * Mostrar mensaje de error
     */
    showError(container, message) {
        if (typeof container === 'string') {
            container = document.querySelector(container);
        }
        if (container) {
            container.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error: ${message}</p>
                </div>
            `;
        }
    }

    /**
     * Renderizar dashboard
     */
    async renderDashboard(container) {
        try {
            this.showLoading(container);
            const response = await this.api.getDashboard();
            const { categorias, subcategorias, productos, stats } = response.data;

            const containerElement = typeof container === 'string' ? 
                document.querySelector(container) : container;

            containerElement.innerHTML = `
                <!-- Estadísticas del Sistema -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="stat-info">
                            <h3>${stats.total_categorias}</h3>
                            <p>Categorías Activas</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="stat-info">
                            <h3>${stats.total_subcategorias}</h3>
                            <p>Subcategorías Activas</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3>${stats.total_productos}</h3>
                            <p>Productos Activos</p>
                        </div>
                    </div>
                </div>

                <!-- Sección de Categorías -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-tags"></i> Categorías</h2>
                    </div>

                    <div class="cards-container">
                        ${categorias.length > 0 ? categorias.map(categoria => {
                            const subcatsCount = subcategorias.filter(sub => sub.categoria_id == categoria.id).length;
                            const productosCount = productos.filter(prod => prod.categoria_id == categoria.id).length;
                            
                            return `
                                <div class="category-card">
                                    <div class="card-header">
                                        <h3>${categoria.nombre}</h3>
                                        <span class="estado activa">
                                            <i class="fas fa-check-circle"></i>
                                            Activa
                                        </span>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-stats">
                                            <div class="stat">
                                                <span class="stat-number">${subcatsCount}</span>
                                                <span class="stat-label">Subcategorías</span>
                                            </div>
                                            <div class="stat">
                                                <span class="stat-number">${productosCount}</span>
                                                <span class="stat-label">Productos</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-actions">
                                        <button onclick="verCategoria(${categoria.id})" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                            Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            `;
                        }).join('') : `
                            <div class="empty-state">
                                <i class="fas fa-tags"></i>
                                <p>No hay categorías activas disponibles</p>
                            </div>
                        `}
                    </div>
                </section>

                <!-- Sección de Productos Disponibles -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-box"></i> Productos Disponibles</h2>
                    </div>

                    <div class="table-container">
                        ${productos.length > 0 ? `
                            <table class="usuario-table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Categoría</th>
                                        <th>Subcategoría</th>
                                        <th>Cantidad</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${productos.slice(0, 10).map(producto => `
                                        <tr>
                                            <td>
                                                <strong>${producto.nombre || 'Sin nombre'}</strong>
                                            </td>
                                            <td>
                                                <span class="precio-badge">
                                                    <i class="fas fa-dollar-sign"></i>
                                                    $${this.formatPresupuesto(producto.precio || 0)}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="categoria-badge">
                                                    <i class="fas fa-tag"></i>
                                                    ${producto.categoria_nombre || 'Sin categoría'}
                                                </span>
                                                <br>
                                                <small>Disponible: $${this.formatPresupuesto(producto.presupuesto_disponible || 0)}</small>
                                            </td>
                                            <td>
                                                ${producto.subcategoria_nombre ? 
                                                    `<span class="subcategoria-badge">
                                                        <i class="fas fa-layer-group"></i>
                                                        ${producto.subcategoria_nombre}
                                                    </span>` : 
                                                    '<span class="sin-subcategoria">Sin subcategoría</span>'
                                                }
                                            </td>
                                            <td>
                                                <input type="number" min="1" value="1" 
                                                       id="cantidad-${producto.id}" 
                                                       class="cantidad-input"
                                                       style="width: 60px;">
                                            </td>
                                            <td>
                                                <button onclick="realizarPedido(${producto.id})" class="btn btn-success btn-sm">
                                                    <i class="fas fa-cart-plus"></i>
                                                    Pedir
                                                </button>
                                                <button onclick="verProducto(${producto.id})" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                    Ver
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                            
                            ${productos.length > 10 ? `
                                <div class="table-footer">
                                    <p>Mostrando 10 de ${productos.length} productos totales</p>
                                </div>
                            ` : ''}
                        ` : `
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p>No hay productos activos disponibles</p>
                            </div>
                        `}
                    </div>
                </section>
            `;
        } catch (error) {
            this.showError(container, error.message);
        }
    }

    /**
     * Renderizar lista de categorías
     */
    async renderCategorias(container) {
        try {
            this.showLoading(container);
            const response = await this.api.getCategorias();
            const categorias = response.data;

            const containerElement = typeof container === 'string' ? 
                document.querySelector(container) : container;

            containerElement.innerHTML = `
                <div class="categorias-list">
                    <div class="list-header">
                        <h2><i class="fas fa-tags"></i> Categorías Disponibles</h2>
                        <p>Total: ${categorias.length} categorías</p>
                    </div>
                    
                    <div class="items-grid">
                        ${categorias.map(categoria => `
                            <div class="categoria-card" data-id="${categoria.id}">
                                <div class="card-content">
                                    <h3>${categoria.nombre}</h3>
                                    <p class="estado ${categoria.estado}">${categoria.estado}</p>
                                </div>
                                <div class="card-actions">
                                    <button class="btn btn-primary" onclick="verCategoria(${categoria.id})">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        } catch (error) {
            this.showError(container, error.message);
        }
    }

    /**
     * Renderizar lista de productos
     */
    async renderProductos(container) {
        try {
            this.showLoading(container);
            const response = await this.api.getProductos();
            const productos = response.data;

            const containerElement = typeof container === 'string' ? 
                document.querySelector(container) : container;

            containerElement.innerHTML = `
                <div class="productos-list">
                    <div class="list-header">
                        <h2><i class="fas fa-box"></i> Productos Disponibles</h2>
                        <p>Total: ${productos.length} productos</p>
                    </div>
                    
                    <div class="items-grid">
                        ${productos.map(producto => `
                            <div class="producto-card" data-id="${producto.id}">
                                <div class="card-content">
                                    <h3>${producto.nombre}</h3>
                                    <p class="categoria">Categoría: ${producto.categoria_nombre}</p>
                                    ${producto.subcategoria_nombre ? `<p class="subcategoria">Subcategoría: ${producto.subcategoria_nombre}</p>` : ''}
                                    <p class="estado ${producto.estado}">${producto.estado}</p>
                                </div>
                                <div class="card-actions">
                                    <button class="btn btn-primary" onclick="verProducto(${producto.id})">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        } catch (error) {
            this.showError(container, error.message);
        }
    }

    /**
     * Formatear presupuesto
     */
    formatPresupuesto(cantidad) {
        return parseFloat(cantidad || 0).toLocaleString('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /**
     * Cargar y mostrar mis pedidos
     */
    async cargarMisPedidos() {
        try {
            const container = document.getElementById('mis-pedidos-container');
            if (!container) return;

            this.showLoading(container);
            const response = await this.api.getMisPedidos();
            const pedidos = response.data;

            if (pedidos.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <p>No tienes pedidos realizados</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = `
                <div class="pedidos-lista">
                    ${pedidos.map(pedido => `
                        <div class="pedido-item estado-${pedido.estado}">
                            <div class="pedido-info">
                                <h4>${pedido.producto_nombre}</h4>
                                <p>Categoría: ${pedido.categoria_nombre}</p>
                                <p>Cantidad: ${pedido.cantidad}</p>
                                <p>Total: $${this.formatPresupuesto(pedido.total)}</p>
                            </div>
                            <div class="pedido-estado">
                                <span class="estado ${pedido.estado}">${this.formatearEstadoPedido(pedido.estado)}</span>
                                <small>${this.formatearFecha(pedido.fecha_pedido)}</small>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        } catch (error) {
            console.error('Error al cargar pedidos:', error);
        }
    }

    /**
     * Formatear estado de pedido
     */
    formatearEstadoPedido(estado) {
        const estados = {
            'pendiente': 'Pendiente',
            'aprobado': 'Aprobado',
            'rechazado': 'Rechazado',
            'entregado': 'Entregado'
        };
        return estados[estado] || estado;
    }

    /**
     * Formatear fecha
     */
    formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        try {
            return new Date(fecha).toLocaleDateString('es-ES');
        } catch (error) {
            return 'N/A';
        }
    }

    /**
     * ===========================
     * MÉTODOS PARA NOTIFICACIONES
     * ===========================
     */

    async cargarNotificaciones() {
        try {
            const response = await this.api.getNotificaciones();
            const { notificaciones, total_no_leidas } = response.data;

            // Actualizar badge de notificaciones
            this.actualizarBadgeNotificaciones(total_no_leidas);

            // Renderizar notificaciones
            this.renderizarNotificaciones(notificaciones);

        } catch (error) {
            console.error('Error al cargar notificaciones:', error);
        }
    }

    actualizarBadgeNotificaciones(total) {
        const badge = document.getElementById('notificaciones-badge');
        if (badge) {
            if (total > 0) {
                badge.style.display = 'inline-block';
                badge.textContent = total > 9 ? '9+' : total;
            } else {
                badge.style.display = 'none';
            }
        }
    }

    renderizarNotificaciones(notificaciones) {
        const container = document.getElementById('notificaciones-lista');
        if (!container) return;

        if (!notificaciones || notificaciones.length === 0) {
            container.innerHTML = `
                <div class="empty-notificaciones">
                    <i class="fas fa-bell-slash"></i>
                    <p>No tienes notificaciones</p>
                </div>
            `;
            return;
        }

        container.innerHTML = notificaciones.map(notificacion => `
            <div class="notificacion-item ${notificacion.leida ? 'leida' : 'no-leida'}" 
                 data-id="${notificacion.id}">
                <div class="notificacion-icono">
                    <i class="${this.obtenerIconoNotificacion(notificacion.tipo)}"></i>
                </div>
                <div class="notificacion-contenido">
                    <p class="notificacion-mensaje">${this.escapeHtml(notificacion.mensaje)}</p>
                    <small class="notificacion-fecha">${this.formatearFechaNotificacion(notificacion.fecha_creacion)}</small>
                </div>
                <div class="notificacion-acciones">
                    ${!notificacion.leida ? `
                        <button onclick="marcarNotificacionLeida(${notificacion.id})" 
                                class="btn-marcar-leida" title="Marcar como leída">
                            <i class="fas fa-check"></i>
                        </button>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }

    obtenerIconoNotificacion(tipo) {
        const iconos = {
            'pedido_aprobado': 'fas fa-check-circle text-success',
            'pedido_rechazado': 'fas fa-times-circle text-danger',
            'pedido_entregado': 'fas fa-truck text-info',
            'sistema': 'fas fa-info-circle text-primary'
        };
        
        return iconos[tipo] || 'fas fa-bell text-secondary';
    }

    formatearFechaNotificacion(fecha) {
        if (!fecha) return 'Fecha no disponible';
        
        try {
            const fechaObj = new Date(fecha);
            const ahora = new Date();
            const diferencia = ahora - fechaObj;
            const minutos = Math.floor(diferencia / (1000 * 60));
            const horas = Math.floor(diferencia / (1000 * 60 * 60));
            const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
            
            if (minutos < 1) {
                return 'Ahora mismo';
            } else if (minutos < 60) {
                return `Hace ${minutos} min`;
            } else if (horas < 24) {
                return `Hace ${horas} h`;
            } else if (dias < 7) {
                return `Hace ${dias} día${dias > 1 ? 's' : ''}`;
            } else {
                return fechaObj.toLocaleDateString('es-ES');
            }
        } catch (error) {
            return 'Fecha no disponible';
        }
    }

    async marcarNotificacionLeida(notificacionId) {
        try {
            await this.api.marcarNotificacionLeida(notificacionId);
            // Recargar notificaciones
            await this.cargarNotificaciones();
        } catch (error) {
            console.error('Error al marcar notificación:', error);
            alert('Error al marcar la notificación como leída');
        }
    }

    async marcarTodasLeidas() {
        try {
            await this.api.marcarTodasLasNotificacionesLeidas();
            // Recargar notificaciones
            await this.cargarNotificaciones();
        } catch (error) {
            console.error('Error al marcar notificaciones:', error);
            alert('Error al marcar todas las notificaciones como leídas');
        }
    }

    // ===========================
    // FUNCIONES PARA MIS PEDIDOS
    // ===========================

    async cargarMisPedidos() {
        try {
            const response = await this.api.getMisPedidos();
            this.renderizarMisPedidos(response.data);
        } catch (error) {
            console.error('Error al cargar mis pedidos:', error);
            this.mostrarErrorMisPedidos();
        }
    }

    renderizarMisPedidos(pedidos) {
        const container = document.getElementById('mis-pedidos-container');
        if (!container) return;

        if (!pedidos || pedidos.length === 0) {
            container.innerHTML = `
                <div class="no-pedidos">
                    <i class="fas fa-inbox"></i>
                    <h3>No tienes pedidos realizados</h3>
                    <p>Empieza a explorar nuestras categorías y productos</p>
                    <a href="/usuario" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i>
                        Ir al Dashboard
                    </a>
                </div>
            `;
            return;
        }

        const pedidosHTML = pedidos.map(pedido => {
            const fechaFormateada = new Date(pedido.fecha_pedido).toLocaleDateString('es-ES');
            const fechaSQL = new Date(pedido.fecha_pedido).toISOString().split('T')[0];
            
            const estadoClass = {
                'pendiente': 'estado-pendiente',
                'aprobado': 'estado-aprobado', 
                'rechazado': 'estado-rechazado',
                'entregado': 'estado-entregado'
            }[pedido.estado] || 'estado-pendiente';

            const estadoIcon = {
                'pendiente': 'fas fa-clock',
                'aprobado': 'fas fa-check-circle',
                'rechazado': 'fas fa-times-circle',
                'entregado': 'fas fa-truck'
            }[pedido.estado] || 'fas fa-clock';

            const estadoTexto = {
                'pendiente': 'Pendiente',
                'aprobado': 'Aprobado',
                'rechazado': 'Rechazado',
                'entregado': 'Entregado'
            }[pedido.estado] || 'Pendiente';

            return `
                <div class="pedido-item" data-estado="${pedido.estado}" data-fecha="${fechaSQL}">
                    <div class="pedido-header">
                        <div class="pedido-info">
                            <h4>Pedido #${pedido.id}</h4>
                            <span class="pedido-fecha">
                                <i class="fas fa-calendar-alt"></i>
                                ${fechaFormateada}
                            </span>
                        </div>
                        <div class="pedido-estado ${estadoClass}">
                            <i class="${estadoIcon}"></i>
                            ${estadoTexto}
                        </div>
                    </div>

                    <div class="pedido-body">
                        <div class="pedido-productos">
                            <h5><i class="fas fa-box"></i> Productos:</h5>
                            <div class="productos-lista">
                                ${JSON.parse(pedido.productos).map(prod => `
                                    <div class="producto-item">
                                        <span class="producto-nombre">${prod.nombre}</span>
                                        <span class="producto-cantidad">x${prod.cantidad}</span>
                                        <span class="producto-precio">$${parseFloat(prod.precio_unitario).toLocaleString('es-ES', {minimumFractionDigits: 2})}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        <div class="pedido-total">
                            <strong>Total: $${parseFloat(pedido.total).toLocaleString('es-ES', {minimumFractionDigits: 2})}</strong>
                        </div>

                        ${pedido.comentarios ? `
                            <div class="pedido-comentarios">
                                <h5><i class="fas fa-comment"></i> Comentarios del administrador:</h5>
                                <p>${pedido.comentarios}</p>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');

        container.innerHTML = `
            <div class="pedidos-lista">
                ${pedidosHTML}
            </div>
        `;
    }

    mostrarErrorMisPedidos() {
        const container = document.getElementById('mis-pedidos-container');
        if (!container) return;

        container.innerHTML = `
            <div class="error-pedidos">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Error al cargar los pedidos</h3>
                <p>Ha ocurrido un error al cargar tus pedidos. Por favor, intenta de nuevo.</p>
                <button onclick="window.usuarioRenderer.cargarMisPedidos()" class="btn btn-primary">
                    <i class="fas fa-retry"></i>
                    Reintentar
                </button>
            </div>
        `;
    }
}

// Funciones globales para navegación
async function verCategoria(id) {
    try {
        const api = new UsuarioAPI();
        const response = await api.getCategoria(id);
        console.log('Categoría:', response.data);
        // Aquí puedes agregar lógica para mostrar los detalles o redirigir
        window.location.href = `/usuario/categoria?id=${id}`;
    } catch (error) {
        alert('Error al cargar la categoría: ' + error.message);
    }
}

async function verSubcategoria(id) {
    try {
        const api = new UsuarioAPI();
        const response = await api.getSubcategoria(id);
        console.log('Subcategoría:', response.data);
        window.location.href = `/usuario/subcategoria?id=${id}`;
    } catch (error) {
        alert('Error al cargar la subcategoría: ' + error.message);
    }
}

async function verProducto(id) {
    try {
        const api = new UsuarioAPI();
        const response = await api.getProducto(id);
        console.log('Producto:', response.data);
        window.location.href = `/usuario/producto?id=${id}`;
    } catch (error) {
        alert('Error al cargar el producto: ' + error.message);
    }
}

// Función para realizar pedido
async function realizarPedido(productoId) {
    try {
        const cantidadInput = document.getElementById(`cantidad-${productoId}`);
        const cantidad = cantidadInput ? parseInt(cantidadInput.value) : 1;
        
        if (cantidad <= 0) {
            alert('La cantidad debe ser mayor a 0');
            return;
        }
        
        const comentarios = prompt('Comentarios adicionales (opcional):') || '';
        
        console.log('Enviando pedido:', {
            producto_id: productoId,
            cantidad: cantidad,
            comentarios: comentarios
        });
        
        const api = new UsuarioAPI();
        const response = await api.crearPedido({
            producto_id: productoId,
            cantidad: cantidad,
            comentarios: comentarios
        });
        
        console.log('Respuesta del servidor:', response);
        
        if (response.success) {
            alert(`Pedido creado exitosamente. Total: $${response.pedido.total}`);
            // Recargar la página o actualizar la vista
            if (typeof cargarMisPedidos === 'function') {
                cargarMisPedidos();
            }
            location.reload();
        } else {
            alert('Error al crear pedido: ' + response.message);
        }
    } catch (error) {
        console.error('Error completo:', error);
        alert('Error al realizar pedido: ' + error.message);
    }
}

// Función para cargar mis pedidos
async function cargarMisPedidos() {
    const renderer = window.usuarioRenderer;
    if (renderer) {
        await renderer.cargarMisPedidos();
    }
}

// ===========================
// FUNCIONES PARA NOTIFICACIONES
// ===========================

// Función global para marcar notificación como leída
async function marcarNotificacionLeida(notificacionId) {
    const renderer = window.usuarioRenderer;
    if (renderer) {
        await renderer.marcarNotificacionLeida(notificacionId);
    }
}

// Función global para marcar todas como leídas
async function marcarTodasLasNotificacionesLeidas() {
    const renderer = window.usuarioRenderer;
    if (renderer) {
        await renderer.marcarTodasLeidas();
    }
}

// Función para mostrar/ocultar panel de notificaciones
function toggleNotificaciones() {
    const panel = document.getElementById('notificaciones-panel');
    if (panel) {
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        
        // Si se abre el panel, cargar notificaciones
        if (panel.style.display === 'block') {
            const renderer = window.usuarioRenderer;
            if (renderer) {
                renderer.cargarNotificaciones();
            }
        }
    }
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    const renderer = new UsuarioRenderer();
    
    // Auto-detectar y renderizar contenido basado en la página actual
    const path = window.location.pathname;
    
    if (path === '/usuario' || path === '/usuario/') {
        const dashboardContainer = document.querySelector('#dashboard-content');
        if (dashboardContainer) {
            renderer.renderDashboard(dashboardContainer);
        }
        
        // Cargar notificaciones cada 30 segundos
        renderer.cargarNotificaciones();
        setInterval(() => {
            renderer.cargarNotificaciones();
        }, 30000);
    } else if (path === '/usuario/pedidos' || path === '/usuario/pedidos/') {
        // Página de mis pedidos
        const pedidosContainer = document.querySelector('#mis-pedidos-container');
        if (pedidosContainer) {
            renderer.cargarMisPedidos();
        }
        
        // También cargar notificaciones
        renderer.cargarNotificaciones();
    }
    
    // Exponer instancias globalmente para uso en otros scripts
    window.usuarioAPI = new UsuarioAPI();
    window.usuarioRenderer = renderer;
});

// Exportar para uso en módulos ES6 si es necesario
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { UsuarioAPI, UsuarioRenderer };
}
