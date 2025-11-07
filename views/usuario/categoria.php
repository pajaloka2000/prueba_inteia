<div class="usuario-dashboard">
    <div class="dashboard-header">
        <div class="breadcrumb">
            <a href="/usuario" class="breadcrumb-link">
                <i class="fas fa-home"></i> Inicio
            </a>
            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-current">Categoría: <?php echo htmlspecialchars($categoria->nombre); ?></span>
        </div>
        <h1>
            <i class="fas fa-tag"></i>
            <?php echo htmlspecialchars($categoria->nombre); ?>
        </h1>
        <p>Información detallada de la categoría</p>
    </div>

    <!-- Información de la Categoría -->
    <div class="category-info-card">
        <div class="info-header">
            <h2>Información General</h2>
            <span class="estado activa">
                <i class="fas fa-check-circle"></i>
                Activa
            </span>
        </div>
        <div class="info-stats">
            <div class="stat-item">
                <i class="fas fa-layer-group"></i>
                <span class="stat-number"><?php echo count($subcategorias); ?></span>
                <span class="stat-label">Subcategorías</span>
            </div>
            <div class="stat-item">
                <i class="fas fa-box"></i>
                <span class="stat-number"><?php echo count($productos); ?></span>
                <span class="stat-label">Productos</span>
            </div>
        </div>
    </div>

    <!-- Sección de Subcategorías -->
    <?php if (!empty($subcategorias)): ?>
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-layer-group"></i> Subcategorías</h2>
        </div>

        <div class="cards-container">
            <?php foreach ($subcategorias as $subcategoria): ?>
                <div class="subcategory-card">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($subcategoria->nombre); ?></h3>
                        <span class="estado activa">
                            <i class="fas fa-check-circle"></i>
                            Activa
                        </span>
                    </div>
                    <div class="card-content">
                        <div class="card-stats">
                            <div class="stat">
                                <span class="stat-number">
                                    <?php 
                                    $productos_sub = 0;
                                    if (method_exists($subcategoria, 'contarProductos')) {
                                        $productos_sub = $subcategoria->contarProductos();
                                    }
                                    echo $productos_sub;
                                    ?>
                                </span>
                                <span class="stat-label">Productos</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-actions">
                        <a href="/usuario/subcategoria?id=<?php echo $subcategoria->id; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i>
                            Ver Detalles
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Sección de Productos -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-box"></i> Productos de esta Categoría</h2>
        </div>

        <div class="table-container">
            <?php if (!empty($productos)): ?>
                <table class="usuario-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($producto->nombre); ?></strong>
                                </td>
                                <td>
                                    <span class="estado activo">
                                        <i class="fas fa-check-circle"></i>
                                        Activo
                                    </span>
                                </td>
                                <td>
                                    <a href="/usuario/producto?id=<?php echo $producto->id; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                        Ver Detalles
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No hay productos activos en esta categoría</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="section-actions">
        <a href="/usuario" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver al Dashboard
        </a>
    </div>
</div>
