# Sistema de Gestión de Pedidos - Inteia

## 📋 Descripción General

Sistema web de gestión presupuestaria y aprobación de pedidos desarrollado en PHP con arquitectura MVC. Permite a usuarios básicos solicitar productos respetando presupuestos por categoría, mientras que los administradores tienen control total sobre la aprobación de pedidos y monitoreo financiero en tiempo real.

## 🚀 Características Principales

- ✅ **Gestión de Presupuestos**: Control automático por categoría
- ✅ **Flujo de Aprobación**: Sistema completo de aprobación/rechazo
- ✅ **Notificaciones en Tiempo Real**: Sistema automático de notificaciones
- ✅ **Dashboard Interactivo**: Monitoreo en tiempo real de presupuestos
- ✅ **API RESTful**: Arquitectura desacoplada con endpoints JSON
- ✅ **Sistema de Roles**: Administradores y usuarios básicos
- ✅ **Interfaz Responsive**: Compatible con dispositivos móviles

## 🏗️ Arquitectura del Sistema

```
Frontend (JavaScript/SCSS) ←→ Backend (PHP MVC) ←→ Database (MySQL)
      ↓                           ↓                    ↓
- UsuarioAPI/Renderer      - Controllers         - Tables
- AdminAPI/Renderer        - Models (ActiveRecord) - Views  
- Responsive UI            - Router System        - Triggers
```

## 📁 Estructura del Proyecto

```
inteia/
├── controllers/              # Controladores MVC
│   ├── AdminController.php
│   ├── UsuarioController.php
│   └── LoginController.php
├── models/                   # Modelos de datos
│   ├── ActiveRecord.php
│   ├── Admin.php
│   ├── Categoria.php
│   ├── Producto.php
│   ├── Pedido.php
│   └── Notificacion.php
├── views/                    # Vistas y templates
│   ├── admin/
│   ├── usuario/
│   └── auth/
├── public/                   # Assets públicos
│   ├── index.php            # Punto de entrada
│   └── build/               # Assets compilados
├── src/                     # Código fuente
│   ├── js/                  # JavaScript ES6+
│   └── scss/                # Estilos SCSS
├── docs/                    # Documentación
│   ├── proceso_pedidos.bpmn # Modelo BPMN
│   ├── ARQUITECTURA.md      # Documentación técnica
│   ├── FUNCIONAMIENTO.md    # Manual de usuario
│   └── BASE_DE_DATOS.md     # Esquema de BD
└── includes/                # Configuración
    ├── app.php
    ├── database.php
    └── funciones.php
```

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.0+**: Lenguaje principal
- **MySQL**: Base de datos
- **MVC Pattern**: Arquitectura
- **Active Record**: ORM personalizado

### Frontend
- **JavaScript ES6+**: Lógica del cliente
- **SCSS**: Preprocesador CSS
- **HTML5**: Markup semántico
- **Font Awesome**: Iconografía

### Build Tools
- **Gulp**: Automatización de tareas
- **Babel**: Transpilación JavaScript
- **PostCSS**: Procesamiento CSS
- **Terser**: Minificación

## 🔧 Instalación y Configuración

### Requisitos
- PHP 8.0+
- MySQL 5.7+
- Node.js 14+ (para build tools)
- Composer

### Instalación Rápida

1. **Instalar dependencias**
   ```bash
   # Dependencias de Node.js
   npm install
   
   # Dependencias de Composer
   composer update
   ```

2. **Configurar base de datos**
   - El archivo SQL se encuentra en la carpeta `sql/`
   - Crear una base de datos llamada: `prueba_inteia`
   - Importar en tu gestor preferido
   - Video de referencia: https://www.youtube.com/watch?v=jokDZXRwJ4o

3. **Ejecutar la aplicación**
   ```bash
   # Ir a la carpeta public
   cd public
   
   # Iniciar servidor PHP
   php -S localhost:3000
   
   # Acceder en el navegador
   # http://localhost:3000/login
   ```

### Configuración Detallada

#### 1. Configuración de Base de Datos
Editar `includes/database.php`:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'prueba_inteia');
define('DB_USER', 'root');
define('DB_PASS', '');
?>
```

#### 2. Compilar Assets
```bash
# Desarrollo con watch
npm run dev

# Producción
npm run build
```

#### 3. Configuración del Servidor Web

**Apache (.htaccess)**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

**Nginx**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 🚦 Flujo de Procesos (BPMN)

El sistema implementa un flujo completo documentado en el archivo BPMN que incluye:

1. **Usuario**: Exploración → Pedido → Notificación
2. **Administrador**: Revisión → Aprobación/Rechazo → Monitoreo
3. **Sistema**: Validación → Notificación → Actualización

Ver: [proceso_pedidos.bpmn](docs/proceso_pedidos.bpmn)

## 📊 API Endpoints

### Usuarios (`/api/usuarios/`)
```
GET    /dashboard          # Datos del dashboard
GET    /categorias         # Lista de categorías
GET    /productos          # Lista de productos
POST   /pedidos            # Crear nuevo pedido
GET    /mis-pedidos        # Obtener mis pedidos
GET    /notificaciones     # Obtener notificaciones
POST   /notificaciones/marcar-leida  # Marcar como leída
```

### Administradores (`/api/admin/`)
```
GET    /dashboard          # Dashboard administrativo
GET    /pedidos            # Lista de pedidos
POST   /pedidos/aprobar    # Aprobar pedido
POST   /pedidos/rechazar   # Rechazar pedido
```

## 💾 Base de Datos

### Tablas Principales
- **admins**: Usuarios del sistema
- **categorias**: Categorías con presupuestos
- **productos**: Catálogo de productos
- **pedidos**: Registro de pedidos
- **notificaciones**: Sistema de alertas

### Usuario Administrador Inicial
```sql
INSERT INTO admins (nombre, email, password, rol, estado) 
VALUES ('Admin', 'admin@inteia.com', '$2y$10$hash...', 'administrador', 'activo');
```

## 👥 Roles y Permisos

### Usuario Básico
- ✅ Ver catálogo de productos
- ✅ Crear pedidos (validación presupuestaria)
- ✅ Consultar estado de pedidos
- ✅ Recibir notificaciones

### Administrador
- ✅ Todas las funciones de usuario básico
- ✅ Aprobar/rechazar pedidos
- ✅ Gestionar productos y categorías
- ✅ Configurar presupuestos
- ✅ Monitorear dashboard financiero

## 📖 Documentación Completa

### 📚 Documentos Disponibles

1. **[ARQUITECTURA.md](docs/ARQUITECTURA.md)**: Documentación técnica completa
   - Patrón MVC implementado
   - API REST detallada
   - Decisiones de diseño
   - Consideraciones de escalabilidad

2. **[FUNCIONAMIENTO.md](docs/FUNCIONAMIENTO.md)**: Manual de funcionamiento
   - Flujos de usuario paso a paso
   - Casos de uso detallados
   - Manejo de errores
   - Configuración inicial

3. **[BASE_DE_DATOS.md](docs/BASE_DE_DATOS.md)**: Esquema de base de datos
   - Diagrama de relaciones
   - Descripción de tablas
   - Vistas y triggers
   - Consultas importantes

4. **[proceso_pedidos.bpmn](docs/proceso_pedidos.bpmn)**: Modelo de procesos
   - Flujo completo BPMN 2.0
   - Interacciones entre actores
   - Decisiones y validaciones automáticas

## 🎯 Casos de Uso Principales

### Escenario 1: Usuario Crea Pedido
1. Usuario explora catálogo
2. Selecciona producto y cantidad
3. Sistema valida presupuesto disponible
4. Usuario confirma pedido
5. Pedido queda pendiente de aprobación

### Escenario 2: Admin Aprueba Pedido
1. Admin revisa pedidos pendientes
2. Evalúa pedido específico
3. Aprueba con comentarios opcionales
4. Sistema actualiza presupuesto automáticamente
5. Usuario recibe notificación de aprobación

## 🚀 Funcionalidades Implementadas

```
✅ Sistema de usuarios y autenticación
✅ Catálogo de productos por categorías
✅ Creación de pedidos con validación presupuestaria
✅ Sistema de aprobación/rechazo por administradores
✅ Notificaciones automáticas en tiempo real
✅ Dashboard administrativo con monitoreo financiero
✅ API RESTful completa
✅ Documentación técnica y funcional
✅ Modelo BPMN de procesos
✅ Interfaz responsive y moderna
```

## 📞 Acceso Rápido

### URLs de Acceso
- **Login**: http://localhost:3000/login
- **Dashboard Admin**: http://localhost:3000/admin
- **Dashboard Usuario**: http://localhost:3000/usuario

### Credenciales de Prueba
```
Administrador:
- Email: admin@inteia.com
- Password: [definir según BD importada]

Usuario Básico:
- Email: usuario@inteia.com
- Password: [definir según BD importada]
```

## 🔒 Seguridad

- **Autenticación**: Sesiones PHP seguras
- **Autorización**: Validación de roles en cada endpoint
- **Validación**: Sanitización de inputs y SQL injection prevention
- **CSRF Protection**: Tokens en formularios críticos

## 🤝 Soporte

Para soporte y consultas:
- 📧 Email: soporte@inteia.com
- 📋 Issues: [GitHub Issues](https://github.com/usuario/prueba_inteia/issues)

---

## 🏆 Estado del Proyecto

**Estado**: ✅ Completado y funcional  
**Versión**: 1.0.0  
**Última actualización**: Noviembre 2025

**Autor**: Manuel Pajares - [pajaloka2000](https://github.com/pajaloka2000)
