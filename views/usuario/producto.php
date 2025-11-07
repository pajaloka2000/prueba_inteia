<div class="usuario-dashboard">
    <div class="dashboard-header">
        <div class="breadcrumb" id="breadcrumb-container">
            <!-- Se cargará dinámicamente desde la API -->
        </div>
        <h1 id="product-title">
            <i class="fas fa-box"></i>
            <span id="product-name">Cargando producto...</span>
        </h1>
        <p>Información detallada del producto</p>
    </div>

    <!-- Loading Spinner -->
    <div id="loading-container" class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Cargando información del producto...</p>
    </div>

    <!-- Error Container -->
    <div id="error-container" class="error-message" style="display: none;">
        <i class="fas fa-exclamation-triangle"></i>
        <p id="error-message">Error al cargar el producto</p>
        <button onclick="window.history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver
        </button>
    </div>

    <!-- Product Content Container -->
    <div id="product-content" style="display: none;">
        <!-- Información del Producto -->
        <div class="product-info-card">
            <div class="info-header">
                <h2>Información General</h2>
                <span class="estado activo">
                    <i class="fas fa-check-circle"></i>
                    Activo
                </span>
            </div>
            <div class="info-content">
                <div class="product-details" id="product-details">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="additional-info">
            <div class="info-section">
                <h3><i class="fas fa-info-circle"></i> Información Adicional</h3>
                <div class="info-grid" id="additional-info-grid">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
        </div>

        <div class="section-actions" id="product-actions">
            <!-- Se cargará dinámicamente -->
        </div>
    </div>
</div>

<script>
/**
 * Script para manejar la vista de producto usando API
 */
document.addEventListener('DOMContentLoaded', async function() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    
    if (!productId) {
        showError('ID de producto no proporcionado');
        return;
    }
    
    try {
        await loadProductData(productId);
    } catch (error) {
        console.error('Error loading product:', error);
        showError('Error al cargar la información del producto');
    }
});

async function loadProductData(productId) {
    try {
        const response = await fetch(`/api/usuarios/producto?id=${productId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Error en la respuesta del servidor');
        }
        
        renderProductData(data.data);
        
    } catch (error) {
        console.error('Error fetching product data:', error);
        showError(error.message);
    }
}

function renderProductData(data) {
    const { producto, categoria, subcategoria, subcategorias_disponibles } = data;
    
    // Actualizar título y breadcrumb
    document.getElementById('product-name').textContent = producto.nombre;
    
    // Breadcrumb
    const breadcrumbContainer = document.getElementById('breadcrumb-container');
    breadcrumbContainer.innerHTML = `
        <a href="/usuario" class="breadcrumb-link">
            <i class="fas fa-home"></i> Inicio
        </a>
        <span class="breadcrumb-separator">></span>
        <a href="/usuario/categoria?id=${categoria.id}" class="breadcrumb-link">
            ${categoria.nombre}
        </a>
        <span class="breadcrumb-separator">></span>
        <span class="breadcrumb-current">Producto: ${producto.nombre}</span>
    `;
    
    // Detalles del producto
    const productDetails = document.getElementById('product-details');
    productDetails.innerHTML = `
        <div class="detail-item">
            <strong>Nombre del Producto:</strong>
            <span>${producto.nombre}</span>
        </div>
        <div class="detail-item">
            <strong>Categoría:</strong>
            <a href="/usuario/categoria?id=${categoria.id}" class="category-link">
                <i class="fas fa-tag"></i>
                ${categoria.nombre}
            </a>
        </div>
        <div class="detail-item">
            <strong>Estado:</strong>
            <span class="estado activo">
                <i class="fas fa-check-circle"></i>
                Activo
            </span>
        </div>
        ${subcategoria ? `
        <div class="detail-item">
            <strong>Subcategoría:</strong>
            <a href="/usuario/subcategoria?id=${subcategoria.id}" class="subcategory-tag">
                <i class="fas fa-layer-group"></i>
                ${subcategoria.nombre}
            </a>
        </div>
        ` : ''}
    `;
    
    // Información adicional
    const additionalInfoGrid = document.getElementById('additional-info-grid');
    additionalInfoGrid.innerHTML = `
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-tag"></i>
            </div>
            <div class="info-text">
                <h4>Categoría Principal</h4>
                <p>${categoria.nombre}</p>
                <a href="/usuario/categoria?id=${categoria.id}" class="info-link">
                    Ver todos los productos de esta categoría
                </a>
            </div>
        </div>

        ${subcategoria ? `
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="info-text">
                <h4>Subcategoría</h4>
                <p>Este producto pertenece a: <strong>${subcategoria.nombre}</strong></p>
                <a href="/usuario/subcategoria?id=${subcategoria.id}" class="info-link">
                    Ver productos de esta subcategoría
                </a>
            </div>
        </div>
        ` : subcategorias_disponibles && subcategorias_disponibles.length > 0 ? `
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="info-text">
                <h4>Subcategorías Disponibles</h4>
                <p>Esta categoría tiene ${subcategorias_disponibles.length} subcategoría(s) disponible(s)</p>
                <div class="subcategory-links">
                    ${subcategorias_disponibles.map(sub => `
                        <a href="/usuario/subcategoria?id=${sub.id}" class="info-link">
                            ${sub.nombre}
                        </a>
                    `).join('')}
                </div>
            </div>
        </div>
        ` : ''}

        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="info-text">
                <h4>Estado del Producto</h4>
                <p>Este producto está <strong>activo</strong> y disponible para consulta</p>
            </div>
        </div>
    `;
    
    // Acciones
    const productActions = document.getElementById('product-actions');
    productActions.innerHTML = `
        <a href="/usuario/categoria?id=${categoria.id}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Volver a Categoría
        </a>
        <a href="/usuario" class="btn btn-outline">
            <i class="fas fa-home"></i>
            Ir al Dashboard
        </a>
    `;
    
    // Mostrar contenido y ocultar loading
    document.getElementById('loading-container').style.display = 'none';
    document.getElementById('product-content').style.display = 'block';
}

function showError(message) {
    document.getElementById('loading-container').style.display = 'none';
    document.getElementById('error-message').textContent = message;
    document.getElementById('error-container').style.display = 'block';
}
</script>
