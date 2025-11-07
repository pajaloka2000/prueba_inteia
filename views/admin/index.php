<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>
            <i class="fas fa-tachometer-alt"></i>
            Panel de Administración financiera
        </h1>
        <p>Gestiona productos, categorías, subcategorías y usuarios del sistema</p>
        <div class="admin-info">
            <span class="admin-welcome">
                <i class="fas fa-user-shield"></i>
                Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Administrador'); ?>
            </span>
            <div class="admin-actions">
                <a href="/admin/pedidos" class="btn btn-info btn-sm">
                    <i class="fas fa-clipboard-list"></i>
                    Gestionar Pedidos
                </a>
                <a href="/admin/productos/crear" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i>
                    Nuevo Producto
                </a>
                <a href="/logout" class="btn btn-secondary btn-sm">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="admin-stats-container">
        <div class="admin-stat-card">
            <div class="stat-icon productos">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3><i class="fas fa-spinner fa-spin"></i></h3>
                <p>Productos Totales</p>
            </div>
        </div>
        
        <div class="admin-stat-card">
            <div class="stat-icon categorias">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-info">
                <h3><i class="fas fa-spinner fa-spin"></i></h3>
                <p>Categorías</p>
            </div>
        </div>
        
        <div class="admin-stat-card">
            <div class="stat-icon subcategorias">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-info">
                <h3><i class="fas fa-spinner fa-spin"></i></h3>
                <p>Subcategorías</p>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon usuarios">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><i class="fas fa-spinner fa-spin"></i></h3>
                <p>Usuarios</p>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon presupuesto">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3 id="presupuesto-total"><i class="fas fa-spinner fa-spin"></i></h3>
                <p>Presupuesto Total</p>
            </div>
        </div>

        <div class="admin-stat-card">
            <div class="stat-icon pedidos">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-info">
                <h3 id="pedidos-pendientes"><i class="fas fa-spinner fa-spin"></i></h3>
                <p>Pedidos Pendientes</p>
            </div>
        </div>
    </div>

    <!-- Panel de Resumen Financiero -->
    <section class="dashboard-section resumen-financiero">
        <div class="section-header">
            <h2><i class="fas fa-chart-line"></i> Resumen Financiero</h2>
            <button id="btn-actualizar-presupuestos" class="btn btn-info btn-sm">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>

        <div class="presupuestos-grid" id="presupuestos-grid">
            <div class="loading-card">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Cargando información financiera...</p>
            </div>
        </div>
    </section>

    <!-- Sección de Productos -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-box"></i> Productos</h2>
            <a href="/admin/productos/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Producto
            </a>
        </div>

        <div class="table-container">
            <table class="admin-table" id="productos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Subcategoría</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="productos-tbody">
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando productos...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Sección de Categorías -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-tags"></i> Categorías</h2>
            <a href="/admin/categorias/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Categoría
            </a>
        </div>

        <div class="table-container">
            <table class="admin-table" id="categorias-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Presupuesto</th>
                        <th>Estado</th>
                        <th>Subcategorías</th>
                        <th>Productos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="categorias-tbody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando categorías...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Sección de Subcategorías -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-layer-group"></i> Subcategorías</h2>
            <a href="/admin/subcategorias/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Subcategoría
            </a>
        </div>

        <div class="table-container">
            <table class="admin-table" id="subcategorias-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría Padre</th>
                        <th>Estado</th>
                        <th>Productos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="subcategorias-tbody">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando subcategorías...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Sección de Usuarios -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-users"></i> Usuarios</h2>
            <a href="/admin/usuarios/crear" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </a>
        </div>

        <div class="table-container">
            <table class="admin-table" id="usuarios-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="usuarios-tbody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem;">
                            <i class="fas fa-spinner fa-spin"></i> Cargando usuarios...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
