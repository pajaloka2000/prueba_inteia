<div class="formulario-container">
    <div class="breadcrumb">
        <a href="/admin"><i class="fas fa-tachometer-alt"></i> Panel</a>
        <span class="separator">/</span>
        <span class="current">
            <?php echo isset($usuario->id) ? 'Editar Usuario' : 'Crear Usuario'; ?>
        </span>
    </div>

    <h2><?php echo isset($usuario->id) ? 'Editar Usuario' : 'Crear Usuario'; ?></h2>
    
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
            <legend><i class="fas fa-user"></i> Información del Usuario</legend>

            <div class="campo">
                <label for="nombre"><i class="fas fa-id-card"></i> Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Nombre completo del usuario" 
                       value="<?php echo s($usuario->nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" id="email" name="email" placeholder="email@ejemplo.com" 
                       value="<?php echo s($usuario->email); ?>" required>
            </div>

            <?php if (!isset($usuario->id)): ?>
                <div class="campo">
                    <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
                    <input type="password" id="password" name="password" placeholder="Contraseña del usuario" required>
                </div>
            <?php else: ?>
                <div class="campo">
                    <label for="password"><i class="fas fa-lock"></i> Nueva Contraseña (opcional):</label>
                    <input type="password" id="password" name="password" placeholder="Dejar vacío para mantener la actual">
                    <small>Deja este campo vacío si no deseas cambiar la contraseña</small>
                </div>
            <?php endif; ?>

            <div class="campo">
                <label for="rol"><i class="fas fa-user-shield"></i> Rol:</label>
                <select id="rol" name="rol" required>
                    <option value="">-- Seleccionar Rol --</option>
                    <option value="administrador" <?php echo $usuario->rol === 'administrador' ? 'selected' : ''; ?>>
                        Administrador
                    </option>
                    <option value="basico" <?php echo $usuario->rol === 'basico' ? 'selected' : ''; ?>>
                        Básico
                    </option>
                </select>
            </div>

            <div class="campo">
                <label for="estado"><i class="fas fa-toggle-on"></i> Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="activo" <?php echo $usuario->estado === 'activo' ? 'selected' : ''; ?>>
                        Activo
                    </option>
                    <option value="inactivo" <?php echo $usuario->estado === 'inactivo' ? 'selected' : ''; ?>>
                        Inactivo
                    </option>
                </select>
            </div>
        </fieldset>

        <div class="info-permisos">
            <h3><i class="fas fa-info-circle"></i> Información sobre Roles</h3>
            <div class="row">
                <div class="col-6">
                    <div class="rol-info">
                        <h4><i class="fas fa-user-shield"></i> Administrador</h4>
                        <ul>
                            <li>Acceso completo a todas las secciones</li>
                            <li>Puede crear, editar y eliminar: usuarios, categorías, subcategorías y productos</li>
                            <li>Gestión completa del sistema</li>
                        </ul>
                    </div>
                </div>
                <div class="col-6">
                    <div class="rol-info">
                        <h4><i class="fas fa-user"></i> Básico</h4>
                        <ul>
                            <li>Solo lectura en todas las secciones</li>
                            <li>Puede consultar: usuarios, categorías, subcategorías y productos</li>
                            <li>Solo puede modificar su propia cuenta</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="boton boton-verde">
            <i class="fas fa-save"></i>
            <?php echo isset($usuario->id) ? 'Actualizar Usuario' : 'Crear Usuario'; ?>
        </button>
    </form>
</div>
