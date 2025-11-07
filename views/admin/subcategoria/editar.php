<?php
include_once __DIR__ . '/../../templates/alertas.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <div class="breadcrumb">
            <a href="/admin" class="breadcrumb-link">
                <i class="fas fa-home"></i> Inicio
            </a>
            <span class="breadcrumb-separator">></span>
            <span class="breadcrumb-current">Editar Subcategoría</span>
        </div>
        <h1 class="admin-title">
            <i class="fas fa-edit"></i>
            Editar Subcategoría
        </h1>
    </div>

    <div class="form-container">
        <form method="POST" class="formulario">
            <fieldset>
                <legend>Información de la Subcategoría</legend>
                
                <div class="campo">
                    <label for="nombre">Nombre de la Subcategoría:</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        placeholder="Ingrese el nombre de la subcategoría"
                        value="<?php echo s($subcategoria->nombre); ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="categoria_id">Categoría Padre:</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <option value="">-- Seleccione una Categoría --</option>
                        <?php foreach($categorias as $categoria): ?>
                            <option value="<?php echo $categoria->id; ?>" <?php echo ($subcategoria->categoria_id == $categoria->id) ? 'selected' : ''; ?>>
                                <?php echo s($categoria->nombre); ?> (<?php echo ucfirst($categoria->estado); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" required>
                        <option value="">-- Seleccione un Estado --</option>
                        <option value="activa" <?php echo ($subcategoria->estado === 'activa') ? 'selected' : ''; ?>>
                            Activa
                        </option>
                        <option value="inactiva" <?php echo ($subcategoria->estado === 'inactiva') ? 'selected' : ''; ?>>
                            Inactiva
                        </option>
                    </select>
                </div>

                <?php if($subcategoria->id): ?>
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <strong>Productos asociados:</strong> <?php echo $subcategoria->contarProductos(); ?> productos
                </div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Importante:</strong> Al cambiar el estado de esta subcategoría:
                    <ul>
                        <li>Si se <strong>activa</strong>, se activarán automáticamente todos los productos asociados</li>
                        <li>Si se <strong>desactiva</strong>, se desactivarán automáticamente todos los productos asociados</li>
                        <li>Una subcategoría solo puede estar activa si su categoría padre también está activa</li>
                    </ul>
                </div>
            </fieldset>

            <div class="acciones">
                <a href="/admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Cancelar
                </a>
                <input type="submit" class="btn btn-primary" value="Actualizar Subcategoría">
            </div>
        </form>
    </div>
</div>
