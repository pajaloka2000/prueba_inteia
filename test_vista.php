<?php
require_once 'includes/app.php';
use Model\Categoria;

try {
    $result = Categoria::consultarSQL('SHOW CREATE VIEW vista_presupuesto_categorias');
    echo "Vista encontrada:\n";
    var_dump($result);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    
    // Intentar mostrar todas las vistas
    try {
        $views = Categoria::consultarSQL("SHOW TABLES LIKE '%vista%'");
        echo "Vistas disponibles:\n";
        var_dump($views);
    } catch (Exception $e2) {
        echo 'Error al mostrar vistas: ' . $e2->getMessage() . "\n";
    }
    
    // Verificar si existe la vista con un SELECT
    try {
        $data = Categoria::consultarSQL('SELECT * FROM vista_presupuesto_categorias LIMIT 1');
        echo "Datos de la vista:\n";
        var_dump($data);
    } catch (Exception $e3) {
        echo 'Vista no existe: ' . $e3->getMessage() . "\n";
        
        // Consultar directamente las categorías con presupuestos
        try {
            $query = "SELECT 
                c.id,
                c.nombre,
                c.presupuesto as presupuesto_asignado,
                COALESCE(SUM(CASE WHEN p.estado IN ('aprobado', 'entregado') THEN p.total ELSE 0 END), 0) as presupuesto_usado,
                (c.presupuesto - COALESCE(SUM(CASE WHEN p.estado IN ('aprobado', 'entregado') THEN p.total ELSE 0 END), 0)) as presupuesto_disponible,
                CASE 
                    WHEN c.presupuesto > 0 THEN 
                        (COALESCE(SUM(CASE WHEN p.estado IN ('aprobado', 'entregado') THEN p.total ELSE 0 END), 0) / c.presupuesto) * 100 
                    ELSE 0 
                END as porcentaje_usado,
                c.estado
                FROM categorias c
                LEFT JOIN productos pr ON pr.categoria_id = c.id
                LEFT JOIN pedidos p ON p.producto_id = pr.id
                GROUP BY c.id, c.nombre, c.presupuesto, c.estado
                ORDER BY c.nombre";
            
            $categorias_presupuesto = Categoria::consultarSQL($query);
            echo "Categorías con presupuesto calculado:\n";
            var_dump($categorias_presupuesto);
            
        } catch (Exception $e4) {
            echo 'Error en consulta manual: ' . $e4->getMessage() . "\n";
        }
    }
}
?>