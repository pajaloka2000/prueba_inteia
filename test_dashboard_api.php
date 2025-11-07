<?php
require_once 'includes/app.php';

// Simular una sesión de administrador
session_start();
$_SESSION['login'] = true;
$_SESSION['rol'] = 'administrador';
$_SESSION['id'] = 1;
$_SESSION['nombre'] = 'Admin Test';

use Controllers\AdminController;

echo "Probando API Dashboard Admin...\n\n";

try {
    // Capturar la salida del método API
    ob_start();
    AdminController::apiDashboard();
    $response = ob_get_clean();
    
    echo "Respuesta del API:\n";
    echo $response;
    echo "\n\n";
    
    // Intentar decodificar JSON
    $data = json_decode($response, true);
    if ($data) {
        echo "Datos decodificados exitosamente:\n";
        echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
        
        if (isset($data['data'])) {
            echo "Estadísticas:\n";
            var_dump($data['data']['estadisticas']);
            
            echo "\nCantidad de productos: " . count($data['data']['productos']) . "\n";
            echo "Cantidad de categorías: " . count($data['data']['categorias']) . "\n";
            echo "Cantidad de subcategorías: " . count($data['data']['subcategorias']) . "\n";
            echo "Cantidad de usuarios: " . count($data['data']['usuarios']) . "\n";
            
            echo "\nPresupuestos por categoría:\n";
            foreach ($data['data']['presupuestos_categorias'] as $presupuesto) {
                echo "- {$presupuesto['nombre']}: Asignado \${$presupuesto['presupuesto_asignado']}, Usado \${$presupuesto['presupuesto_usado']}, Disponible \${$presupuesto['presupuesto_disponible']}\n";
            }
        }
    } else {
        echo "Error al decodificar JSON\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>