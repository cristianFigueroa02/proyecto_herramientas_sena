<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

// Obtener el ID de la herramienta a devolver enviado desde la solicitud AJAX
$id = $_GET['id'];

// Consulta para obtener los detalles de `deta_reportes` relacionados con `reportes` y `herrramienta`
$usua = $conectar->prepare("SELECT *
                            FROM deta_reportes
                            INNER JOIN reportes ON deta_reportes.id_reporte = reportes.id_reporte
                            INNER JOIN herrramienta ON deta_reportes.id_herramienta = herrramienta.id_herramienta
                            WHERE reportes.id_reporte = :id_reportes");
$usua->bindParam(':id_reportes', $id);
$usua->execute();
$asigna = $usua->fetchAll(PDO::FETCH_ASSOC);

// Verificar si hay resultados y obtener `id_prestamo`
if (count($asigna) > 0) {
    $prestamo = $asigna[0]['id_prestamo'];
} else {
    // Manejar el caso cuando no hay resultados
    $prestamo = null; // O cualquier otro valor predeterminado
}

// Si se obtiene un `id_prestamo` válido, realizar actualizaciones
if ($prestamo !== null) {
    // Actualizar el estado del reporte a 'inactivo'
    $updateQuery = $conectar->prepare("UPDATE reportes SET estado_reporte = 'inactivo' WHERE id_reporte = ?");
    $updateQuery->execute([$id]);

    // Actualizar el estado del préstamo a 'devuelto'
    $updateEstado = $conectar->prepare("UPDATE prestamos SET estado_prestamo = 'devuelto' WHERE id_prestamo = ?");
    $updateEstado->execute([$prestamo]);

    // Recorrer cada detalle de `asigna`
    foreach ($asigna as $detalle) {
    $id_herramienta = $detalle['id_herramienta'];
    $id_prestamo = $detalle['id_prestamo'];  
    $cantidad_reportada = $detalle['cantidad_reportada'];
    $stock = $detalle['stock'];

    // Actualizar la cantidad prestada sumando la cantidad reportada en el préstamo correspondiente
    $stmt_detalle = $conectar->prepare("UPDATE detalle_pres SET cantidad_prestada = cantidad_prestada + :cantidad_reportada WHERE id_prestamo = :id_prestamo AND id_herramienta = :id_herramienta");
    $stmt_detalle->bindParam(':id_prestamo', $id_prestamo);
    $stmt_detalle->bindParam(':id_herramienta', $id_herramienta);
    $stmt_detalle->bindParam(':cantidad_reportada', $cantidad_reportada);
    $stmt_detalle->execute();

    // Calcular el total de stock
    $totalStock = $stock + $cantidad_reportada;

    // Actualizar el stock de la herramienta
    $stmt_update = $conectar->prepare("UPDATE herrramienta SET stock = :cantidad WHERE id_herramienta = :id_herramienta");
    $stmt_update->bindParam(':id_herramienta', $id_herramienta);
    $stmt_update->bindParam(':cantidad', $totalStock);
    $stmt_update->execute();

    // Cambiar el estado de la herramienta a disponible si el stock es mayor que 0
    if ($totalStock > 0) {
        $stmt_estado = $conectar->prepare("UPDATE herrramienta SET estado = 'disponible' WHERE id_herramienta = :id_herramienta");
        $stmt_estado->bindParam(':id_herramienta', $id_herramienta);
        $stmt_estado->execute();
    }


    }


    // Mostrar mensaje de confirmación y redireccionar
    echo '<script>';
    echo 'alert("El reporte se ha inactivado con éxito.");';
    echo 'window.location.href = "lista_reportes.php";'; // Redireccionar a lista_reportes.php
    echo '</script>';
} else {
    // Manejar el caso cuando `id_prestamo` es nulo (no se encontró ningún resultado)
    echo '<script>';
    echo 'alert("No se encontró un reporte asociado al ID proporcionado.");';
    echo 'window.location.href = "lista_reportes.php";'; // Redireccionar a lista_reportes.php
    echo '</script>';
}
