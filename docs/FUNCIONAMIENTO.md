# Manual de Funcionamiento - Sistema de Gestión de Pedidos Inteia

## Índice
1. [Introducción](#introducción)
2. [Flujo del Usuario Básico](#flujo-del-usuario-básico)
3. [Flujo del Administrador](#flujo-del-administrador)
4. [Procesos Automáticos del Sistema](#procesos-automáticos-del-sistema)
5. [Casos de Uso Detallados](#casos-de-uso-detallados)
6. [Manejo de Errores](#manejo-de-errores)
7. [Configuración Inicial](#configuración-inicial)

---

## Introducción

El Sistema de Gestión de Pedidos Inteia es una aplicación web que permite a usuarios básicos solicitar productos respetando presupuestos asignados por categoría, mientras que los administradores tienen control total sobre la aprobación de pedidos y gestión del sistema.

### Roles del Sistema
- **Usuario Básico**: Puede explorar catálogo, crear pedidos y consultar su estado
- **Administrador**: Puede gestionar todo el sistema, aprobar/rechazar pedidos y configurar presupuestos

---

## Flujo del Usuario Básico

### 1. Inicio de Sesión

**Proceso:**
1. Usuario accede a `/login`
2. Ingresa email y contraseña
3. Sistema valida credenciales
4. Si es válido, redirige a `/usuario`
5. Si es inválido, muestra mensaje de error

**Pantalla de Login:**
```
┌─────────────────────────────────────┐
│           LOGIN INTEIA              │
├─────────────────────────────────────┤
│ Email:    [________________]        │
│ Password: [________________]        │
│                                     │
│            [INICIAR SESIÓN]         │
│                                     │
│ ¿Olvidaste tu contraseña?           │
└─────────────────────────────────────┘
```

### 2. Dashboard del Usuario

Al iniciar sesión exitosamente, el usuario llega al dashboard principal:

**Elementos del Dashboard:**
- **Header**: Información del usuario y navegación
- **Estadísticas**: Resumen de productos, categorías disponibles
- **Notificaciones**: Panel de notificaciones con badge
- **Catálogo**: Lista de categorías con productos disponibles

**API Calls del Dashboard:**
```javascript
// 1. Cargar datos generales
GET /api/usuarios/dashboard
Response: {
    "estadisticas": { "total_productos": 45, "total_categorias": 8 },
    "categorias": [...],
    "productos": [...],
    "subcategorias": [...]
}

// 2. Cargar notificaciones
GET /api/usuarios/notificaciones
Response: {
    "notificaciones": [
        {
            "id": 1,
            "titulo": "Pedido Aprobado",
            "mensaje": "Tu pedido #123 ha sido aprobado",
            "leida": false,
            "created_at": "2025-11-07 10:30:00"
        }
    ]
}
```

### 3. Exploración del Catálogo

**Navegación por Categorías:**
```
Dashboard → Categoría → Subcategoría → Producto
    ↓          ↓            ↓           ↓
   /usuario → /usuario/categoria?id=1 → productos filtrados
```

**Vista de Categoría:**
- Lista de subcategorías disponibles
- Productos directos de la categoría
- Información del presupuesto disponible
- Botones para crear pedidos

### 4. Creación de Pedidos

**Proceso Paso a Paso:**

1. **Seleccionar Producto:**
   - Usuario hace clic en "Crear Pedido" junto a un producto
   - Se abre modal/formulario con detalles del producto

2. **Configurar Pedido:**
   ```html
   ┌─────────────────────────────────────┐
   │         CREAR PEDIDO                │
   ├─────────────────────────────────────┤
   │ Producto: Laptop Dell XPS 13        │
   │ Precio: $1,200.00                   │
   │ Cantidad: [2] ← spinner input       │
   │ Total: $2,400.00                    │
   │                                     │
   │ Presupuesto Disponible: $15,000.00  │
   │ Después del pedido: $12,600.00      │
   │                                     │
   │   [CANCELAR]  [CREAR PEDIDO]        │
   └─────────────────────────────────────┘
   ```

3. **Validación Presupuestaria:**
   - Sistema valida si hay presupuesto suficiente
   - Si no hay presupuesto, muestra error
   - Si hay presupuesto, permite continuar

4. **Envío del Pedido:**
   ```javascript
   POST /api/usuarios/pedidos
   Body: {
       "producto_id": 1,
       "cantidad": 2,
       "comentarios": "Urgente para proyecto"
   }
   
   Response: {
       "success": true,
       "message": "Pedido creado exitosamente",
       "data": {
           "pedido_id": 123,
           "estado": "pendiente"
       }
   }
   ```

### 5. Consulta de Pedidos

**Acceso a "Mis Pedidos":**
- Desde el header: botón "Mis Pedidos"
- URL: `/usuario/pedidos`

**Vista de Mis Pedidos:**
```
┌─────────────────────────────────────────────────────────────┐
│                       MIS PEDIDOS                           │
├─────────────────────────────────────────────────────────────┤
│ Filtros: [Estado ▼] [Fecha] [🔄 Actualizar]                │
├─────────────────────────────────────────────────────────────┤
│ RESUMEN                                                     │
│ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────────┐        │
│ │   3     │ │   5     │ │   1     │ │  $12,450.00 │        │
│ │Pendiente│ │Aprobado │ │Rechazado│ │Total Gastado│        │
│ └─────────┘ └─────────┘ └─────────┘ └─────────────┘        │
├─────────────────────────────────────────────────────────────┤
│ HISTORIAL DE PEDIDOS                                        │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Pedido #123        📅 07/11/2025     🟡 Pendiente      │ │
│ │ • Laptop Dell XPS 13 x2                                │ │
│ │ • Total: $2,400.00                                     │ │
│ └─────────────────────────────────────────────────────────┘ │
│                                                             │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ Pedido #122        📅 06/11/2025     ✅ Aprobado       │ │
│ │ • Mouse Inalámbrico x1                                 │ │
│ │ • Total: $45.00                                        │ │
│ │ 💬 Comentario admin: "Aprobado para uso inmediato"     │ │
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### 6. Recepción de Notificaciones

**Flujo de Notificaciones:**
1. Admin aprueba/rechaza pedido
2. Sistema crea notificación automáticamente
3. Usuario ve badge de notificación en header
4. Usuario hace clic en campana de notificaciones
5. Se despliega panel con notificaciones
6. Usuario puede marcar como leídas

**Panel de Notificaciones:**
```html
┌─────────────────────────────────────┐
│ 🔔 Notificaciones            [×]    │
├─────────────────────────────────────┤
│ [Marcar todas como leídas]          │
├─────────────────────────────────────┤
│ ● Pedido Aprobado             ⏰2h   │
│   Tu pedido #123 ha sido aprobado   │
│   [Marcar como leída]               │
├─────────────────────────────────────┤
│   Pedido Rechazado            ⏰1d   │
│   Tu pedido #121 fue rechazado      │
│   Razón: Presupuesto insuficiente   │
└─────────────────────────────────────┘
```

---

## Flujo del Administrador

### 1. Dashboard Administrativo

**Acceso:** URL `/admin` después del login como administrador

**Elementos del Dashboard:**
- **Estadísticas Generales**: Contadores de productos, categorías, usuarios
- **Resumen Financiero**: Cards de presupuestos por categoría
- **Gestión de Datos**: Tablas de productos, categorías, subcategorías y usuarios

**Dashboard Layout:**
```
┌─────────────────────────────────────────────────────────────┐
│                    PANEL DE ADMINISTRACIÓN                  │
├─────────────────────────────────────────────────────────────┤
│ ESTADÍSTICAS                                                │
│ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐            │
│ │ 45  │ │  8  │ │ 15  │ │ 12  │ │50K  │ │  3  │            │
│ │Prod │ │Cat  │ │Sub  │ │User │ │$$$  │ │Pend │            │
│ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘            │
├─────────────────────────────────────────────────────────────┤
│ RESUMEN FINANCIERO                           [🔄 Actualizar]│
│ ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐ │
│ │ TECNOLOGÍA      │ │ OFICINA         │ │ MARKETING       │ │
│ │ $50,000 Total   │ │ $30,000 Total   │ │ $20,000 Total   │ │
│ │ $35,000 Usado   │ │ $15,000 Usado   │ │ $8,000 Usado    │ │
│ │ $15,000 Disp.   │ │ $15,000 Disp.   │ │ $12,000 Disp.   │ │
│ │ ████████░░ 70%  │ │ █████░░░░░ 50%  │ │ ████░░░░░░ 40%  │ │
│ │ ⚠️ Presup. Alto │ │ ✅ Saludable    │ │ ✅ Saludable    │ │
│ └─────────────────┘ └─────────────────┘ └─────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### 2. Gestión de Pedidos

**Acceso:** 
- Botón "Gestionar Pedidos" en header
- URL: `/admin/pedidos`

**Vista de Gestión de Pedidos:**
```
┌─────────────────────────────────────────────────────────────┐
│                     GESTIÓN DE PEDIDOS                      │
├─────────────────────────────────────────────────────────────┤
│ ESTADÍSTICAS DE PEDIDOS                                     │
│ ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────────┐        │
│ │    3    │ │    8    │ │    2    │ │  $45,230.00 │        │
│ │Pendiente│ │Aprobado │ │Rechazado│ │Valor Total  │        │
│ └─────────┘ └─────────┘ └─────────┘ └─────────────┘        │
├─────────────────────────────────────────────────────────────┤
│ FILTROS: [Estado ▼] [Categoría ▼] [Usuario ▼] [🔄]        │
├─────────────────────────────────────────────────────────────┤
│ TABLA DE PEDIDOS                                            │
│ ┌─────┬────────────┬──────────┬────────┬─────────┬─────────┐ │
│ │ ID  │ Usuario    │ Producto │ Total  │ Estado  │ Acciones│ │
│ ├─────┼────────────┼──────────┼────────┼─────────┼─────────┤ │
│ │#123 │Juan Pérez  │Laptop XPS│$2,400  │🟡Pend. │✅❌ Ver │ │
│ │     │juan@co.com │Tecnología│        │         │         │ │
│ ├─────┼────────────┼──────────┼────────┼─────────┼─────────┤ │
│ │#122 │Ana García  │Mouse     │$45     │✅Aprob.│   📋 Ver │ │
│ │     │ana@co.com  │Tecnología│        │         │         │ │
│ └─────┴────────────┴──────────┴────────┴─────────┴─────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### 3. Proceso de Aprobación/Rechazo

**Flujo de Decisión:**

1. **Revisar Pedido:**
   - Admin hace clic en botón ✅ (aprobar) o ❌ (rechazar)
   - Se abre modal de confirmación

2. **Modal de Aprobación:**
   ```html
   ┌─────────────────────────────────────┐
   │        APROBAR PEDIDO #123          │
   ├─────────────────────────────────────┤
   │ Usuario: Juan Pérez                 │
   │ Producto: Laptop Dell XPS 13        │
   │ Cantidad: 2                         │
   │ Total: $2,400.00                    │
   │                                     │
   │ Presupuesto Categoría Tecnología:   │
   │ • Total: $50,000.00                 │
   │ • Usado: $35,000.00                 │
   │ • Disponible: $15,000.00            │
   │ • Después: $12,600.00               │
   │                                     │
   │ Comentarios (opcional):             │
   │ [________________________________]  │
   │                                     │
   │   [CANCELAR]    [APROBAR PEDIDO]    │
   └─────────────────────────────────────┘
   ```

3. **Envío de Decisión:**
   ```javascript
   // Para Aprobación
   POST /api/admin/pedidos/aprobar
   Body: {
       "pedido_id": 123,
       "comentarios": "Aprobado para uso inmediato"
   }
   
   // Para Rechazo
   POST /api/admin/pedidos/rechazar
   Body: {
       "pedido_id": 123,
       "comentarios": "Presupuesto insuficiente para este periodo"
   }
   ```

4. **Procesos Automáticos Post-Decisión:**
   - Sistema actualiza estado del pedido
   - Si es aprobación: descuenta del presupuesto disponible
   - Crea notificación automática para el usuario
   - Actualiza dashboard en tiempo real
   - Envía respuesta al admin con confirmación

### 4. Monitoreo en Tiempo Real

**Dashboard Actualizable:**
- Botón "🔄 Actualizar" en sección de Resumen Financiero
- Recarga automática de datos cada acción
- Indicadores visuales de estado presupuestario

**Colores de Alertas:**
- 🟢 Verde: 0-60% del presupuesto usado (Saludable)
- 🟡 Amarillo: 61-80% del presupuesto usado (Advertencia)
- 🔴 Rojo: 81-100% del presupuesto usado (Crítico)

---

## Procesos Automáticos del Sistema

### 1. Validación Presupuestaria Automática

**Trigger al Crear Pedido:**
```sql
-- Pseudo-código del proceso
FUNCTION validar_presupuesto(producto_id, cantidad, precio_unitario)
    total_pedido = cantidad * precio_unitario
    categoria = GET categoria FROM producto
    presupuesto_usado = SUM pedidos aprobados de categoria
    presupuesto_disponible = categoria.presupuesto - presupuesto_usado
    
    IF total_pedido > presupuesto_disponible THEN
        RETURN ERROR "Presupuesto insuficiente"
    ELSE
        RETURN SUCCESS
    END IF
```

### 2. Creación Automática de Notificaciones

**Proceso al Aprobar Pedido:**
```php
// En AdminController::apiAprobarPedido()
if ($resultado) {
    // Crear notificación automática
    Notificacion::crearNotificacion(
        $pedido->usuario_id, 
        'pedido_aprobado',
        "Tu pedido #{$pedido->id} ha sido aprobado", 
        $pedido->id
    );
}
```

**Proceso al Rechazar Pedido:**
```php
// En AdminController::apiRechazarPedido()
if ($resultado) {
    // Crear notificación automática
    Notificacion::crearNotificacion(
        $pedido->usuario_id, 
        'pedido_rechazado',
        "Tu pedido #{$pedido->id} ha sido rechazado", 
        $pedido->id
    );
}
```

### 3. Actualización de Presupuestos

**Proceso Automático:**
1. Pedido es aprobado
2. Sistema calcula nuevo presupuesto usado
3. Actualiza vista materializada de presupuestos
4. Dashboard admin se actualiza automáticamente

---

## Casos de Uso Detallados

### Caso de Uso 1: Usuario Crea Pedido Exitoso

**Precondiciones:**
- Usuario logueado como básico
- Existe producto con stock
- Hay presupuesto disponible en la categoría

**Flujo Principal:**
1. Usuario navega a Dashboard → Categoría → Producto
2. Hace clic en "Crear Pedido"
3. Especifica cantidad (ej: 2 unidades)
4. Sistema calcula total ($1,200 x 2 = $2,400)
5. Sistema valida presupuesto disponible ($15,000 - $2,400 = $12,600 ✓)
6. Usuario confirma pedido
7. Sistema crea pedido con estado "pendiente"
8. Sistema muestra mensaje de éxito
9. Usuario es redirigido a "Mis Pedidos"

**Postcondiciones:**
- Pedido creado en BD con estado "pendiente"
- Pedido visible en dashboard del admin
- Usuario puede consultar el estado del pedido

### Caso de Uso 2: Admin Aprueba Pedido

**Precondiciones:**
- Admin logueado
- Existe pedido en estado "pendiente"
- Hay presupuesto disponible

**Flujo Principal:**
1. Admin navega a "/admin/pedidos"
2. Ve lista de pedidos pendientes
3. Selecciona pedido específico
4. Hace clic en botón "Aprobar" (✅)
5. Se abre modal con detalles del pedido
6. Admin opcionalmente agrega comentarios
7. Confirma aprobación
8. Sistema actualiza estado a "aprobado"
9. Sistema descuenta del presupuesto de categoría
10. Sistema crea notificación para el usuario
11. Dashboard se actualiza automáticamente

**Postcondiciones:**
- Pedido estado cambiado a "aprobado"
- Presupuesto de categoría reducido
- Notificación creada para usuario
- Usuario puede ver el cambio de estado

### Caso de Uso 3: Pedido Rechazado por Presupuesto

**Precondiciones:**
- Usuario logueado
- Producto existe
- Presupuesto insuficiente en categoría

**Flujo Alternativo:**
1. Usuario intenta crear pedido de $5,000
2. Presupuesto disponible es solo $3,000
3. Sistema valida y detecta insuficiencia
4. Sistema muestra error: "Presupuesto insuficiente. Disponible: $3,000, Solicitado: $5,000"
5. Usuario no puede confirmar el pedido
6. Modal se mantiene abierto con error
7. Usuario debe ajustar cantidad o cancelar

**Postcondiciones:**
- Pedido no es creado
- Presupuesto no es afectado
- Usuario informado del motivo

---

## Manejo de Errores

### Errores de Frontend

**Validación de Formularios:**
```javascript
// Validación en tiempo real
if (cantidad <= 0) {
    mostrarError("La cantidad debe ser mayor a cero");
    return false;
}

if (total > presupuestoDisponible) {
    mostrarError(`Presupuesto insuficiente. Disponible: $${presupuestoDisponible}`);
    return false;
}
```

**Errores de Conexión:**
```javascript
try {
    const response = await fetch('/api/usuarios/pedidos', {
        method: 'POST',
        body: JSON.stringify(pedidoData)
    });
    
    if (!response.ok) {
        throw new Error('Error en la conexión');
    }
    
} catch (error) {
    mostrarError("Error de conexión. Por favor, intenta de nuevo.");
}
```

### Errores de Backend

**Validación de Datos:**
```php
public function crearPedido() {
    try {
        // Validar datos de entrada
        $datos = json_decode(file_get_contents('php://input'), true);
        
        if (!$datos['producto_id']) {
            throw new Exception('Producto requerido');
        }
        
        if ($datos['cantidad'] <= 0) {
            throw new Exception('Cantidad debe ser mayor a cero');
        }
        
        // Validar presupuesto
        if (!$this->validarPresupuesto($datos)) {
            throw new Exception('Presupuesto insuficiente');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
```

### Códigos de Error Estándar

| Código | Descripción | Acción |
|--------|-------------|---------|
| 400 | Datos inválidos | Validar formulario |
| 401 | No autorizado | Redirigir a login |
| 403 | Sin permisos | Mostrar mensaje de error |
| 404 | Recurso no encontrado | Redirigir o mostrar 404 |
| 500 | Error del servidor | Mostrar error genérico |

---

## Configuración Inicial

### 1. Configuración de Base de Datos

**Archivo: `/includes/database.php`**
```php
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_name = $_ENV['DB_NAME'] ?? 'inteia_db';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';
```

### 2. Creación de Usuario Administrador Inicial

```sql
INSERT INTO admins (nombre, email, password, rol, estado) 
VALUES (
    'Administrador',
    'admin@inteia.com',
    '$2y$10$hashedPassword',
    'administrador',
    'activo'
);
```

### 3. Configuración de Categorías y Presupuestos

```sql
-- Crear categorías con presupuestos iniciales
INSERT INTO categorias (nombre, presupuesto, estado) VALUES
('Tecnología', 50000.00, 'activa'),
('Oficina', 30000.00, 'activa'),
('Marketing', 20000.00, 'activa');

-- Crear productos de ejemplo
INSERT INTO productos (nombre, precio, categoria_id, estado) VALUES
('Laptop Dell XPS 13', 1200.00, 1, 'activo'),
('Mouse Inalámbrico', 45.00, 1, 'activo'),
('Silla Ergonómica', 350.00, 2, 'activo');
```

### 4. Variables de Entorno

**Archivo: `.env`**
```env
# Base de datos
DB_HOST=localhost
DB_NAME=inteia_db
DB_USER=root
DB_PASS=

# Configuración de la aplicación
APP_URL=http://localhost
APP_DEBUG=true

# Configuración de notificaciones
NOTIFICATIONS_ENABLED=true
REAL_TIME_UPDATES=true
```

---

## Conclusión

El Sistema de Gestión de Pedidos Inteia proporciona un flujo completo y robusto para la gestión presupuestaria y aprobación de pedidos. El sistema está diseñado para ser:

- **Intuitivo**: Interfaces claras para usuarios y administradores
- **Eficiente**: Validaciones automáticas y procesos optimizados
- **Confiable**: Manejo de errores y validaciones múltiples
- **Escalable**: Arquitectura preparada para crecimiento
- **Transparente**: Notificaciones y feedback constante

La aplicación balancea la autonomía del usuario con el control administrativo, proporcionando una herramienta efectiva para la gestión de recursos y presupuestos empresariales.