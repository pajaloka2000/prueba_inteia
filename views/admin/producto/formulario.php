<div class="formulario-container">
    <div class="breadcrumb">
        <a href="/admin"><i class="fas fa-tachometer-alt"></i> Panel</a>
        <span class="separator">/</span>
        <span class="current">
            <?php echo isset($producto->id) ? 'Editar Producto' : 'Crear Producto'; ?>
        </span>
    </div>

    <h2><?php echo isset($producto->id) ? 'Editar Producto' : 'Crear Producto'; ?></h2>
    
    <a href="/admin" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver al Panel
    </a>

    <?php foreach($alertas as $key => $mensajes): ?>
        <div class="alerta alerta-<?php echo $key; ?>">
            <?php foreach($mensajes as $mensaje): ?>
                <p><i class="fas fa-exclamation-circle"></i> <?php echo $mensaje; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <form class="formulario" method="POST">
        <fieldset>
            <legend><i class="fas fa-box"></i> Información del Producto</legend>

            <div class="campo">
                <label for="nombre"><i class="fas fa-tag"></i> Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre del producto" 
                       value="<?php echo s($producto->nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="categoria_id"><i class="fas fa-layer-group"></i> Categoría:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">-- Seleccionar Categoría --</option>
                    <?php foreach($categorias as $categoria): ?>
                        <option value="<?php echo $categoria->id; ?>" 
                                <?php echo $producto->categoria_id == $categoria->id ? 'selected' : ''; ?>>
                            <?php echo $categoria->nombre; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="subcategoria_id"><i class="fas fa-folder-open"></i> Subcategoría:</label>
                <select id="subcategoria_id" name="subcategoria_id">
                    <option value="">-- Seleccionar Subcategoría (Opcional) --</option>
                    <?php foreach($subcategorias as $subcategoria): ?>
                        <option value="<?php echo $subcategoria->id; ?>" 
                                data-categoria="<?php echo $subcategoria->categoria_id; ?>"
                                <?php echo $producto->subcategoria_id == $subcategoria->id ? 'selected' : ''; ?>>
                            <?php echo $subcategoria->nombre; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="estado"><i class="fas fa-toggle-on"></i> Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="activo" <?php echo $producto->estado === 'activo' ? 'selected' : ''; ?>>Activo</option>
                    <option value="inactivo" <?php echo $producto->estado === 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>

            <div class="campo">
                <label for="precio"><i class="fas fa-dollar-sign"></i> Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="0.00" 
                       step="0.01" min="0" value="<?php echo s($producto->precio); ?>" required>
                <small class="campo-ayuda">
                    <i class="fas fa-info-circle"></i>
                    Precio unitario del producto en moneda local
                </small>
            </div>
        </fieldset>

        <button type="submit" class="boton boton-verde">
            <i class="fas fa-save"></i>
            <?php echo isset($producto->id) ? 'Actualizar Producto' : 'Crear Producto'; ?>
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoriaSelect = document.getElementById('categoria_id');
    const subcategoriaSelect = document.getElementById('subcategoria_id');
    const todasLasSubcategorias = Array.from(subcategoriaSelect.querySelectorAll('option[data-categoria]'));

    function filtrarSubcategorias() {
        const categoriaSeleccionada = categoriaSelect.value;
        
        // Limpiar subcategorías
        subcategoriaSelect.innerHTML = '<option value="">-- Seleccionar Subcategoría (Opcional) --</option>';
        
        if (categoriaSeleccionada) {
            // Agregar subcategorías que pertenecen a la categoría seleccionada
            todasLasSubcategorias.forEach(option => {
                if (option.dataset.categoria === categoriaSeleccionada) {
                    subcategoriaSelect.appendChild(option.cloneNode(true));
                }
            });
        }
    }

    categoriaSelect.addEventListener('change', filtrarSubcategorias);
    
    // Filtrar al cargar la página si hay una categoría seleccionada
    if (categoriaSelect.value) {
        filtrarSubcategorias();
        
        // Restaurar subcategoría seleccionada si existe
        const subcategoriaSeleccionada = <?php echo json_encode($producto->subcategoria_id ?? ''); ?>;
        if (subcategoriaSeleccionada) {
            subcategoriaSelect.value = subcategoriaSeleccionada;
        }
    }
});
</script>
