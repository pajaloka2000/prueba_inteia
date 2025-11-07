// ===========================
// ADMIN API y RENDERIZACIÓN
// ===========================

class AdminAPI {
    constructor() {
        this.baseURL = '/api/admin';
        this.pedidos = []; // Cache de pedidos
    }

    async obtenerDashboard() {
        try {
            const response = await fetch(`${this.baseURL}/dashboard`);
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al obtener datos del dashboard');
            }
            
            return data.data;
        } catch (error) {
            console.error('Error en API Dashboard:', error);
            throw error;
        }
    }

    async obtenerPedidos() {
        try {
            const response = await fetch(`${this.baseURL}/pedidos`);
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al obtener pedidos');
            }
            
            this.pedidos = data.data.pedidos; // Cachear pedidos
            return data.data;
        } catch (error) {
            console.error('Error en API Pedidos:', error);
            throw error;
        }
    }

    async aprobarPedido(pedidoId, comentarios = '') {
        try {
            const response = await fetch(`${this.baseURL}/pedidos/aprobar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    pedido_id: pedidoId,
                    comentarios: comentarios
                })
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al aprobar pedido');
            }
            
            return data;
        } catch (error) {
            console.error('Error al aprobar pedido:', error);
            throw error;
        }
    }

    async rechazarPedido(pedidoId, comentarios = '') {
        try {
            const response = await fetch(`${this.baseURL}/pedidos/rechazar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    pedido_id: pedidoId,
                    comentarios: comentarios
                })
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al rechazar pedido');
            }
            
            return data;
        } catch (error) {
            console.error('Error al rechazar pedido:', error);
            throw error;
        }
    }
}

class AdminRenderer {
    constructor() {
        this.api = new AdminAPI();
    }

    async cargarDashboard() {
        try {
            // Mostrar loading en las estadísticas
            this.mostrarLoading();
            
            const data = await this.api.obtenerDashboard();
            
            // Actualizar estadísticas principales
            this.actualizarEstadisticas(data.estadisticas);
            
            // Renderizar presupuestos en tiempo real
            this.renderizarPresupuestosFinancieros(data.presupuestos_categorias, data.estadisticas_pedidos);
            
            // Renderizar tablas pasando productos para calcular conteos
            this.renderizarProductos(data.productos, data.categorias, data.subcategorias);
            this.renderizarCategorias(data.categorias, data.subcategorias, data.productos);
            this.renderizarSubcategorias(data.subcategorias, data.categorias, data.productos);
            this.renderizarUsuarios(data.usuarios);
            
        } catch (error) {
            console.error('Error al cargar dashboard:', error);
            this.mostrarError('Error al cargar los datos del dashboard');
        }
    }

    mostrarLoading() {
        // Actualizar contadores con loading
        const statsCards = document.querySelectorAll('.admin-stat-card h3');
        statsCards.forEach(card => {
            card.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });
    }

    actualizarEstadisticas(estadisticas) {
        const stats = [
            { selector: '.admin-stat-card:nth-child(1) h3', value: estadisticas.total_productos },
            { selector: '.admin-stat-card:nth-child(2) h3', value: estadisticas.total_categorias },
            { selector: '.admin-stat-card:nth-child(3) h3', value: estadisticas.total_subcategorias },
            { selector: '.admin-stat-card:nth-child(4) h3', value: estadisticas.total_usuarios },
            { 
                selector: '#presupuesto-total', 
                value: `$${this.formatearPresupuesto(estadisticas.presupuesto_total || 0)}` 
            },
            { 
                selector: '#pedidos-pendientes', 
                value: estadisticas.pedidos_pendientes || 0
            }
        ];

        stats.forEach(stat => {
            const element = document.querySelector(stat.selector);
            if (element) {
                element.innerHTML = stat.value;
            }
        });
    }

    renderizarPresupuestosFinancieros(presupuestos, estadisticasPedidos) {
        const container = document.getElementById('presupuestos-grid');
        if (!container) return;

        if (!presupuestos || presupuestos.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-chart-line"></i>
                    <p>No hay información financiera disponible</p>
                </div>
            `;
            return;
        }

        container.innerHTML = presupuestos.map(categoria => {
            const porcentajeUsado = parseFloat(categoria.porcentaje_usado || 0);
            const disponible = parseFloat(categoria.presupuesto_disponible || 0);
            const asignado = parseFloat(categoria.presupuesto_asignado || 0);
            const usado = parseFloat(categoria.presupuesto_usado || 0);
            
            // Determinar color según el porcentaje usado
            let colorBarra = 'success';
            if (porcentajeUsado > 80) colorBarra = 'danger';
            else if (porcentajeUsado > 60) colorBarra = 'warning';
            
            return `
                <div class="presupuesto-card">
                    <div class="presupuesto-header">
                        <h4>${this.escapeHtml(categoria.nombre)}</h4>
                        <span class="estado-categoria ${categoria.estado}">
                            ${this.formatearEstado(categoria.estado)}
                        </span>
                    </div>
                    
                    <div class="presupuesto-amounts">
                        <div class="amount-item">
                            <span class="label">Presupuesto Total:</span>
                            <span class="value total">$${this.formatearPresupuesto(asignado)}</span>
                        </div>
                        <div class="amount-item">
                            <span class="label">Disponible:</span>
                            <span class="value disponible">$${this.formatearPresupuesto(disponible)}</span>
                        </div>
                        <div class="amount-item">
                            <span class="label">Usado:</span>
                            <span class="value usado">$${this.formatearPresupuesto(usado)}</span>
                        </div>
                    </div>
                    
                    <div class="presupuesto-progress">
                        <div class="progress-bar">
                            <div class="progress-fill ${colorBarra}" 
                                 style="width: ${Math.min(porcentajeUsado, 100)}%"></div>
                        </div>
                        <span class="progress-text">${porcentajeUsado.toFixed(1)}% usado</span>
                    </div>
                    
                    <div class="presupuesto-status">
                        ${porcentajeUsado > 90 ? 
                            '<div class="alert-status danger"><i class="fas fa-exclamation-triangle"></i> Presupuesto crítico</div>' :
                            porcentajeUsado > 75 ? 
                            '<div class="alert-status warning"><i class="fas fa-exclamation-circle"></i> Presupuesto alto</div>' :
                            '<div class="alert-status success"><i class="fas fa-check-circle"></i> Presupuesto saludable</div>'
                        }
                    </div>
                </div>
            `;
        }).join('');
    }

    renderizarProductos(productos, categorias, subcategorias) {
        const tbody = document.getElementById('productos-tbody');
        if (!tbody) return;

        if (!productos || productos.length === 0) {
            this.mostrarEstadoVacio(tbody.closest('.table-container'), 'productos');
            return;
        }

        tbody.innerHTML = productos.map(producto => {
            // Buscar nombres de categoría y subcategoría
            const categoria = categorias ? categorias.find(c => c.id == producto.categoria_id) : null;
            const subcategoria = subcategorias ? subcategorias.find(s => s.id == producto.subcategoria_id) : null;
            
            const categoria_nombre = categoria ? categoria.nombre : 'N/A';
            const subcategoria_nombre = subcategoria ? subcategoria.nombre : 'Sin subcategoría';

            return `
                <tr>
                    <td>${producto.id || ''}</td>
                    <td>${this.escapeHtml(producto.nombre || '')}</td>
                    <td>
                        <span class="precio">$${this.formatearPresupuesto(producto.precio || 0)}</span>
                    </td>
                    <td>${this.escapeHtml(categoria_nombre)}</td>
                    <td>
                        <span class="subcategoria">${this.escapeHtml(subcategoria_nombre)}</span>
                    </td>
                    <td>
                        <span class="estado ${producto.estado || 'activo'}">${this.formatearEstado(producto.estado || 'activo')}</span>
                    </td>
                    <td>${this.formatearFecha(producto.created_at)}</td>
                    <td class="acciones">
                        <a href="/admin/productos/editar?id=${producto.id}" class="btn btn-warning btn-sm">
                            Editar
                        </a>
                        <form method="POST" action="/admin/productos/eliminar" style="display: inline;">
                            <input type="hidden" name="id" value="${producto.id}">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            `;
        }).join('');
    }

    renderizarCategorias(categorias, subcategorias, productos = []) {
        const tbody = document.getElementById('categorias-tbody');
        if (!tbody) return;

        if (!categorias || categorias.length === 0) {
            this.mostrarEstadoVacio(tbody.closest('.table-container'), 'categorias');
            return;
        }

        tbody.innerHTML = categorias.map(categoria => {
            // Contar subcategorías y productos relacionados
            const conteoSubcategorias = subcategorias ? subcategorias.filter(s => s.categoria_id == categoria.id).length : 0;
            // Contar productos que pertenecen a esta categoría
            const conteoProductos = productos ? productos.filter(p => p.categoria_id == categoria.id).length : 0;

            return `
                <tr>
                    <td>${categoria.id || ''}</td>
                    <td>${this.escapeHtml(categoria.nombre || '')}</td>
                    <td>
                        <span class="presupuesto">$${this.formatearPresupuesto(categoria.presupuesto || 0)}</span>
                    </td>
                    <td>
                        <span class="estado ${categoria.estado || 'activa'}">${this.formatearEstado(categoria.estado || 'activa')}</span>
                    </td>
                    <td>
                        <span class="relation-count">${conteoSubcategorias}</span>
                    </td>
                    <td>
                        <span class="relation-count">${conteoProductos}</span>
                    </td>
                    <td class="acciones">
                        <a href="/admin/categorias/editar?id=${categoria.id}" class="btn btn-warning btn-sm">
                            Editar
                        </a>
                        ${this.generarBotonEstado(categoria, 'categorias')}
                        <form method="POST" action="/admin/categorias/eliminar" style="display: inline;">
                            <input type="hidden" name="id" value="${categoria.id}">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            `;
        }).join('');
    }

    renderizarSubcategorias(subcategorias, categorias, productos = []) {
        const tbody = document.getElementById('subcategorias-tbody');
        if (!tbody) return;

        if (!subcategorias || subcategorias.length === 0) {
            this.mostrarEstadoVacio(tbody.closest('.table-container'), 'subcategorias');
            return;
        }

        tbody.innerHTML = subcategorias.map(subcategoria => {
            // Buscar categoría padre
            const categoria = categorias ? categorias.find(c => c.id == subcategoria.categoria_id) : null;
            const categoria_nombre = categoria ? categoria.nombre : 'N/A';
            // Contar productos que pertenecen a esta subcategoría
            const conteoProductos = productos ? productos.filter(p => p.subcategoria_id == subcategoria.id).length : 0;

            return `
                <tr>
                    <td>${subcategoria.id || ''}</td>
                    <td>${this.escapeHtml(subcategoria.nombre || '')}</td>
                    <td>${this.escapeHtml(categoria_nombre)}</td>
                    <td>
                        <span class="estado ${subcategoria.estado || 'activa'}">${this.formatearEstado(subcategoria.estado || 'activa')}</span>
                    </td>
                    <td>
                        <span class="relation-count">${conteoProductos}</span>
                    </td>
                    <td class="acciones">
                        <a href="/admin/subcategorias/editar?id=${subcategoria.id}" class="btn btn-warning btn-sm">
                            Editar
                        </a>
                        ${this.generarBotonEstado(subcategoria, 'subcategorias')}
                        <form method="POST" action="/admin/subcategorias/eliminar" style="display: inline;">
                            <input type="hidden" name="id" value="${subcategoria.id}">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta subcategoría?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            `;
        }).join('');
    }

    renderizarUsuarios(usuarios) {
        const tbody = document.getElementById('usuarios-tbody');
        if (!tbody) return;

        if (!usuarios || usuarios.length === 0) {
            this.mostrarEstadoVacio(tbody.closest('.table-container'), 'usuarios');
            return;
        }

        tbody.innerHTML = usuarios.map(usuario => `
            <tr>
                <td>${usuario.id || ''}</td>
                <td>${this.escapeHtml(usuario.nombre || '')}</td>
                <td>${this.escapeHtml(usuario.email || '')}</td>
                <td>
                    <span class="rol ${usuario.rol || 'basico'}">${this.formatearRol(usuario.rol || 'basico')}</span>
                </td>
                <td>
                    <span class="estado ${usuario.estado || 'activo'}">${this.formatearEstado(usuario.estado || 'activo')}</span>
                </td>
                <td>${this.formatearFecha(usuario.created_at)}</td>
                <td class="acciones">
                    <a href="/admin/usuarios/editar?id=${usuario.id}" class="btn btn-warning btn-sm">
                        Editar
                    </a>
                    <form method="POST" action="/admin/usuarios/eliminar" style="display: inline;">
                        <input type="hidden" name="id" value="${usuario.id}">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        `).join('');
    }

    generarBotonEstado(item, tipo) {
        const estadoActual = item.estado || 'activa';
        const nuevoEstado = estadoActual === 'activa' ? 'inactiva' : 'activa';
        const btnClass = estadoActual === 'activa' ? 'btn-secondary' : 'btn-success';
        const texto = estadoActual === 'activa' ? 'Desactivar' : 'Activar';

        return `
            <form method="POST" action="/admin/${tipo}/estado" style="display: inline;">
                <input type="hidden" name="id" value="${item.id}">
                <input type="hidden" name="estado" value="${nuevoEstado}">
                <button type="submit" class="btn ${btnClass} btn-sm">
                    ${texto}
                </button>
            </form>
        `;
    }

    mostrarEstadoVacio(container, tipo) {
        const mensajes = {
            productos: { icono: 'fa-box-open', texto: 'No hay productos registrados', url: '/admin/productos/crear', btnTexto: 'Crear primer producto' },
            categorias: { icono: 'fa-tags', texto: 'No hay categorías registradas', url: '/admin/categorias/crear', btnTexto: 'Crear primera categoría' },
            subcategorias: { icono: 'fa-layer-group', texto: 'No hay subcategorías registradas', url: '/admin/subcategorias/crear', btnTexto: 'Crear primera subcategoría' },
            usuarios: { icono: 'fa-users', texto: 'No hay usuarios registrados', url: '/admin/usuarios/crear', btnTexto: 'Crear primer usuario' }
        };

        const config = mensajes[tipo];
        
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas ${config.icono}"></i>
                <p>${config.texto}</p>
                <a href="${config.url}" class="btn btn-primary">${config.btnTexto}</a>
            </div>
        `;
    }

    mostrarError(mensaje) {
        const container = document.querySelector('.admin-dashboard');
        if (container) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-warning';
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error:</strong> ${mensaje}
            `;
            container.insertBefore(errorDiv, container.firstChild);
        }
    }

    // Métodos de utilidad
    escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        try {
            const date = new Date(fecha);
            return date.toLocaleDateString('es-ES');
        } catch (error) {
            return 'N/A';
        }
    }

    formatearEstado(estado) {
        const estados = {
            'activo': 'Activo',
            'inactivo': 'Inactivo',
            'activa': 'Activa',
            'inactiva': 'Inactiva'
        };
        return estados[estado] || estado;
    }

    formatearRol(rol) {
        const roles = {
            'administrador': 'Administrador',
            'basico': 'Básico'
        };
        return roles[rol] || rol;
    }

    formatearPresupuesto(presupuesto) {
        // Convertir a número si es string
        const numero = typeof presupuesto === 'string' ? parseFloat(presupuesto) : presupuesto;
        
        // Verificar si es un número válido
        if (isNaN(numero)) {
            return '0.00';
        }
        
        // Formatear con separadores de miles y decimales
        return numero.toLocaleString('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ===========================
    // MÉTODOS PARA GESTIÓN DE PEDIDOS
    // ===========================

    async cargarPedidos() {
        try {
            const data = await this.api.obtenerPedidos();
            
            // Actualizar estadísticas de pedidos
            this.actualizarEstadisticasPedidos(data.estadisticas);
            
            // Renderizar tabla de pedidos
            this.renderizarTablaPedidos(data.pedidos);
            
            // Cargar categorías para filtro
            this.cargarCategoriasParaFiltro();
            
        } catch (error) {
            console.error('Error al cargar pedidos:', error);
            this.mostrarError('Error al cargar los pedidos');
        }
    }

    actualizarEstadisticasPedidos(stats) {
        const elementos = {
            'stat-pendientes': stats.pendientes || 0,
            'stat-aprobados': stats.aprobados || 0,
            'stat-rechazados': stats.rechazados || 0,
            'stat-total-valor': `$${this.formatearPresupuesto(stats.total_valor || 0)}`
        };

        for (const [id, valor] of Object.entries(elementos)) {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.textContent = valor;
            }
        }
    }

    renderizarTablaPedidos(pedidos) {
        const tbody = document.getElementById('pedidos-table-body');
        if (!tbody) return;

        if (!pedidos || pedidos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-inbox"></i>
                        <p>No hay pedidos registrados</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = pedidos.map(pedido => `
            <tr class="pedido-row estado-${pedido.estado}">
                <td>#${pedido.id}</td>
                <td>
                    <div class="usuario-info">
                        <strong>${this.escapeHtml(pedido.usuario_nombre)}</strong>
                        <br>
                        <small>${this.escapeHtml(pedido.usuario_email)}</small>
                    </div>
                </td>
                <td>${this.escapeHtml(pedido.producto_nombre)}</td>
                <td>
                    <span class="categoria-badge">
                        ${this.escapeHtml(pedido.categoria_nombre)}
                    </span>
                </td>
                <td>${pedido.cantidad}</td>
                <td>$${this.formatearPresupuesto(pedido.precio_unitario)}</td>
                <td>
                    <strong>$${this.formatearPresupuesto(pedido.total)}</strong>
                </td>
                <td>
                    <span class="estado-badge ${pedido.estado}">
                        ${this.formatearEstadoPedido(pedido.estado)}
                    </span>
                </td>
                <td>${this.formatearFecha(pedido.fecha_pedido)}</td>
                <td>
                    ${this.generarBotonesPedido(pedido)}
                </td>
            </tr>
        `).join('');
    }

    formatearEstadoPedido(estado) {
        const estados = {
            'pendiente': 'Pendiente',
            'aprobado': 'Aprobado',
            'rechazado': 'Rechazado',
            'entregado': 'Entregado'
        };
        return estados[estado] || estado;
    }

    generarBotonesPedido(pedido) {
        if (pedido.estado === 'pendiente') {
            return `
                <div class="btn-group">
                    <button onclick="abrirModalPedido(${pedido.id}, 'aprobar')" 
                            class="btn btn-success btn-sm" title="Aprobar pedido">
                        <i class="fas fa-check"></i>
                    </button>
                    <button onclick="abrirModalPedido(${pedido.id}, 'rechazar')" 
                            class="btn btn-danger btn-sm" title="Rechazar pedido">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        } else {
            return `
                <span class="text-muted">
                    <i class="fas fa-${pedido.estado === 'aprobado' ? 'check-circle' : 'times-circle'}"></i>
                    ${this.formatearEstadoPedido(pedido.estado)}
                </span>
            `;
        }
    }

    async aprobarPedido(pedidoId, comentarios) {
        try {
            const response = await this.api.aprobarPedido(pedidoId, comentarios);
            
            // Mostrar mensaje de éxito
            this.mostrarMensajeExito('Pedido aprobado exitosamente');
            
            // Recargar la lista de pedidos
            await this.cargarPedidos();
            
        } catch (error) {
            throw error;
        }
    }

    async rechazarPedido(pedidoId, comentarios) {
        try {
            const response = await this.api.rechazarPedido(pedidoId, comentarios);
            
            // Mostrar mensaje de éxito
            this.mostrarMensajeExito('Pedido rechazado exitosamente');
            
            // Recargar la lista de pedidos
            await this.cargarPedidos();
            
        } catch (error) {
            throw error;
        }
    }

    mostrarMensajeExito(mensaje) {
        const container = document.querySelector('.admin-pedidos') || document.querySelector('.admin-dashboard');
        if (container) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <strong>Éxito:</strong> ${mensaje}
            `;
            container.insertBefore(alertDiv, container.firstChild);
            
            // Remover después de 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }
    }

    cargarCategoriasParaFiltro() {
        // Este método se puede implementar si hay un select de filtro por categoría
        // Por ahora lo dejamos vacío
    }
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Solo ejecutar en la página del admin
    if (document.querySelector('.admin-dashboard')) {
        const adminRenderer = new AdminRenderer();
        adminRenderer.cargarDashboard();
        
        // Agregar event listener para el botón de actualizar presupuestos
        const btnActualizar = document.getElementById('btn-actualizar-presupuestos');
        if (btnActualizar) {
            btnActualizar.addEventListener('click', () => {
                adminRenderer.cargarDashboard();
            });
        }
        
        // Exponer globalmente para uso en otros scripts
        window.adminRenderer = adminRenderer;
        window.adminAPI = adminRenderer.api;
    }
    
    // Ejecutar en la página de pedidos
    if (document.querySelector('.admin-pedidos')) {
        const adminRenderer = new AdminRenderer();
        adminRenderer.cargarPedidos();
        
        // Exponer globalmente para uso en otros scripts
        window.adminRenderer = adminRenderer;
        window.adminAPI = adminRenderer.api;
    }
});