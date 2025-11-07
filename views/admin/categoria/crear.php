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
            <span class="breadcrumb-current">Crear Categoría</span>
        </div>
        <h1 class="admin-title">
            <i class="fas fa-tags"></i>
            Crear Nueva Categoría
        </h1>
    </div>

    <div class="form-container">
        <form method="POST" class="formulario">
            <fieldset>
                <legend>Información de la Categoría</legend>
                
                <div class="campo">
                    <label for="nombre">Nombre de la Categoría:</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        placeholder="Ingrese el nombre de la categoría"
                        value="<?php echo s($categoria->nombre); ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" required>
                        <option value="">-- Seleccione un Estado --</option>
                        <option value="activa" <?php echo ($categoria->estado === 'activa') ? 'selected' : ''; ?>>
                            Activa
                        </option>
                        <option value="inactiva" <?php echo ($categoria->estado === 'inactiva') ? 'selected' : ''; ?>>
                            Inactiva
                        </option>
                    </select>
                </div>

                <div class="campo">
                    <label for="presupuesto">Presupuesto:</label>
                    <input 
                        type="number" 
                        id="presupuesto" 
                        name="presupuesto" 
                        placeholder="0.00"
                        step="0.01"
                        min="0"
                        value="<?php echo s($categoria->presupuesto); ?>"
                        required
                    >
                    <small class="campo-ayuda">
                        <i class="fas fa-info-circle"></i>
                        Ingrese el presupuesto asignado para esta categoría
                    </small>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota:</strong> Al cambiar el estado de una categoría:
                    <ul>
                        <li>Si se <strong>activa</strong>, se activarán todas las subcategorías y productos asociados</li>
                        <li>Si se <strong>desactiva</strong>, se desactivarán todas las subcategorías y productos asociados</li>
                    </ul>
                </div>
            </fieldset>

            <div class="acciones">
                <a href="/admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Cancelar
                </a>
                <input type="submit" class="btn btn-primary" value="Crear Categoría">
            </div>
        </form>
    </div>
</div>
