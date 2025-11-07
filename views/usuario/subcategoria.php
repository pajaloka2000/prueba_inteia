<div class="usuario-dashboard">
    <div class="dashboard-header">
        <div class="breadcrumb">
            <a href="/usuario" class="breadcrumb-link">
                <i class="fas fa-home"></i> Inicio
            </a>
            <span class="breadcrumb-separator">></span>
            <a href="/usuario/categoria?id=<?php echo $categoria->id; ?>" class="breadcrumb-link">
                <?php echo htmlspecialchars($categoria->nombre); ?>
            </a>
            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-current">Subcategoría: <?php echo htmlspecialchars($subcategoria->nombre); ?></span>
        </div>
        <h1>
            <i class="fas fa-layer-group"></i>
            <?php echo htmlspecialchars($subcategoria->nombre); ?>
        </h1>
        <p>Subcategoría de: <strong><?php echo htmlspecialchars($categoria->nombre); ?></strong></p>
    </div>

    <!-- Información de la Subcategoría -->
    <div class="subcategory-info-card">
        <div class="info-header">
            <h2>Información General</h2>
            <span class="estado activa">
                <i class="fas fa-check-circle"></i>
                Activa
            </span>
        </div>
        <div class="info-content">
            <div class="info-item">
                <strong>Categoría Padre:</strong>
                <a href="/usuario/categoria?id=<?php echo $categoria->id; ?>" class="category-link">
                    <?php echo htmlspecialchars($categoria->nombre); ?>
                </a>
            </div>
            <div class="info-stats">
                <div class="stat-item">
                    <i class="fas fa-box"></i>
                    <span class="stat-number"><?php echo count($productos); ?></span>
                    <span class="stat-label">Productos Asociados</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Productos -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2><i class="fas fa-box"></i> Productos de esta Subcategoría</h2>
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
                    <p>No hay productos activos asociados a esta subcategoría</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="section-actions">
        <a href="/usuario/categoria?id=<?php echo $categoria->id; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver a Categoría
        </a>
        <a href="/usuario" class="btn btn-outline">
            <i class="fas fa-home"></i>
            Ir al Dashboard
        </a>
    </div>
</div>
