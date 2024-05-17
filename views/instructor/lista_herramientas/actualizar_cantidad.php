<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

// Obtener el ID de la herramienta y la cantidad enviados por AJAX
$idHerramienta = $_POST['id_herramienta'];
$cantidad = $_POST['cantidad'];

// Verificar si la cantidad es positiva
if ($cantidad >= 0) {
    try {
        // Primera consulta: actualizar el campo `cantidad`
        $sqlCantidad = "UPDATE herrramienta SET cantidad = cantidad + :cantidad WHERE id_herramienta = :id_herramienta";

        // Preparar la consulta para `cantidad`
        $stmtCantidad = $conectar->prepare($sqlCantidad);

        // Bind de parámetros
        $stmtCantidad->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmtCantidad->bindParam(':id_herramienta', $idHerramienta, PDO::PARAM_INT);

        // Ejecutar la consulta para `cantidad`
        $stmtCantidad->execute();

        // Verificar si se actualizó correctamente la cantidad
        if ($stmtCantidad->rowCount() > 0) {
            // Segunda consulta: actualizar el campo `stock`
            $sqlStock = "UPDATE herrramienta SET stock = stock + :cantidad WHERE id_herramienta = :id_herramienta";

            // Preparar la consulta para `stock`
            $stmtStock = $conectar->prepare($sqlStock);

            // Bind de parámetros
            $stmtStock->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmtStock->bindParam(':id_herramienta', $idHerramienta, PDO::PARAM_INT);

            // Ejecutar la consulta para `stock`
            $stmtStock->execute();

            // Verificar si se actualizó correctamente el stock
            if ($stmtStock->rowCount() > 0) {
                echo "Cantidad actualizada correctamente.";
            } else {
                echo "Error al actualizar el stock.";
            }
        } else {
            echo "Error al actualizar la cantidad.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "La cantidad no puede ser negativa.";
}
?>
