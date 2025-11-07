<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Panel de Usuario'; ?> - Inteia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PiLldshb4dMcqvfmfWHNSE0Ny7ZSfC6mV0W4V8M8A8dRQqHuR1E1VklrmN3EKjGdV8qWJV2w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/build/css/app.css">
</head>

<body class="usuario-layout">
    <header class="usuario-header">
        <div class="header-container">
            <div class="header-brand">
                <i class="fas fa-cube"></i>
                <h1>Inteia</h1>
                <span class="badge badge-user">Usuario</span>
            </div>
            
            <nav class="header-nav">
                <a href="/usuario" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/usuario') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/usuario/pedidos" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/usuario/pedidos') ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Mis Pedidos</span>
                </a>
            </nav>
            
            <div class="header-user">
                <div class="user-info">
                    <span class="user-name">
                        <i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Usuario'); ?>
                    </span>
                    <span class="user-role">Usuario Básico</span>
                </div>
                <a href="/logout" class="btn-logout" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <main class="usuario-main">
        <div class="main-container">
            <?php echo $contenido; ?>
        </div>
    </main>

    <footer class="usuario-footer">
        <div class="footer-container">
            <div class="footer-info">
                <p>&copy; <?php echo date('Y'); ?> Inteia. Sistema de Gestión de Inventario.</p>
                <p>Panel de Usuario - Solo Consulta</p>
            </div>
            <div class="footer-links">
                <a href="/usuario" class="footer-link">
                    <i class="fas fa-home"></i>
                    Inicio
                </a>
            </div>
        </div>
    </footer>

    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
    <script src="/build/js/usuario.js"></script>
</body>
</html>
