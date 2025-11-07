<body class="login-page">
    <div class="login-container">
        <div class="login-form">
            <div class="login-header">
                <h1><i class="fas fa-shield-alt"></i> Inteia Admin</h1>
                <p>Acceso al Panel de Administración</p>
            </div>

            <?php foreach($alertas as $key => $mensajes): ?>
                <div class="alerta alerta-<?php echo $key; ?>">
                    <?php foreach($mensajes as $mensaje): ?>
                        <p><i class="fas fa-exclamation-circle"></i> <?php echo $mensaje; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <form method="POST" class="formulario-login">
                <div class="campo">
                    <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           placeholder="tu-email@ejemplo.com"
                           value="<?php echo s($_POST['email'] ?? ''); ?>"
                           required>
                </div>

                <div class="campo">
                    <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Tu contraseña"
                           required>
                </div>

                <button type="submit" class="boton-login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>

            <div class="login-footer">
                <p><i class="fas fa-info-circle"></i> Datos de prueba:</p>
                <div class="datos-prueba">
                    <strong>Administrador:</strong><br>
                    Email: admin@empresa.com<br>
                    Contraseña: password
                    <hr style="margin: 0.5rem 0; border: none; border-top: 1px solid #ccc;">
                    <strong>Usuario Básico:</strong><br>
                    Email: juan@empresa.com<br>
                    Contraseña: password
                </div>
            </div>
        </div>
    </div>
</body>
