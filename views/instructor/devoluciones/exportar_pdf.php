<?php
require_once("../../../bd/database.php");
require_once("../../../fpdf.php");

// Establecer conexión a la base de datos
$db = new Database();
$conectar = $db->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}

// Verificar si el documento está definido en la sesión
if (isset($_SESSION['documento'])) {
    $documento = $_SESSION['documento'];

    // Consultar todos los datos necesarios para el informe en una sola consulta
    $consulta = $conectar->prepare("
        SELECT 
    usuario.documento AS documento,
    usuario.nombre AS nombre,
    detalle_usuarios.ficha AS ficha,
    formacion.formacion AS formacion,
    prestamos.fecha_prestamo AS fecha_prestamo,
    prestamos.fecha_devolucion AS fecha_devolucion,
    prestamos.estado_prestamo AS estado_prestamo,
    detalle_pres.cantidad_prestada AS cantidad_prestada,
    herrramienta.nombre_he AS nombre_he
FROM 
    prestamos 
INNER JOIN 
    usuario ON prestamos.documento = usuario.documento
INNER JOIN 
    (
        SELECT 
            MAX(id_detalle) AS id_detalle,
            documento,
            ficha
        FROM 
            detalle_usuarios
        GROUP BY 
            documento, ficha
    ) AS unique_detalle ON prestamos.documento = unique_detalle.documento
INNER JOIN 
    formacion ON unique_detalle.ficha = formacion.id_formacion
INNER JOIN 
    detalle_usuarios ON unique_detalle.id_detalle = detalle_usuarios.id_detalle
INNER JOIN 
    detalle_pres ON prestamos.id_prestamo = detalle_pres.id_prestamo
INNER JOIN 
    herrramienta ON detalle_pres.id_herramienta = herrramienta.id_herramienta
WHERE 
    prestamos.estado_prestamo = 'devuelto'
ORDER BY 
    prestamos.fecha_devolucion;

    ");
    $consulta->execute();
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Crear un nuevo objeto FPDF con un tamaño de página más grande (A3, orientación horizontal)
    $pdf = new FPDF('L', 'mm', 'A3');
    $pdf->AddPage();

    // Establecer el título del documento
    $pdf->SetTitle('Reporte de Prestamos');

    // Definir la fuente y el tamaño del texto
    $pdf->SetFont('Arial', '', 12);

    // Obtener el ancho de la página
    $anchoPagina = $pdf->GetPageWidth();

    // Agregar el título del reporte
    $pdf->Cell($anchoPagina, 10, 'Reporte de Devoluciones', 0, 1, 'C'); // 0 para el borde, 1 para salto de línea, 'C' para centrado

    // Agregar encabezados de columna
    $pdf->Cell($anchoPagina * 0.1, 10, 'Documento', 1);
    $pdf->Cell($anchoPagina * 0.15, 10, 'Nombre', 1);
    $pdf->Cell($anchoPagina * 0.05, 10, 'Ficha', 1);
    $pdf->Cell($anchoPagina * 0.22, 10, 'Formacion', 1);
    $pdf->Cell($anchoPagina * 0.07, 10, 'Fecha Inicio', 1);
    $pdf->Cell($anchoPagina * 0.1, 10, 'Fecha Fin', 1);
    $pdf->Cell($anchoPagina * 0.05, 10, 'Estado', 1);
    $pdf->Cell($anchoPagina * 0.15, 10, 'Herramienta', 1);
    $pdf->Cell($anchoPagina * 0.05, 10, 'Cantidad', 1);
    $pdf->Ln();

    // Agregar datos de la tabla a la hoja de cálculo
    foreach ($datos as $row) {
        // Dividir el nombre y la formación en múltiples líneas
        $nombre = wordwrap($row["nombre"], 20, "\n", true);
        $formacion = wordwrap($row["formacion"], 30, "\n", true);

        $pdf->Cell($anchoPagina * 0.1, 10, $row["documento"], 1);
        $pdf->Cell($anchoPagina * 0.15, 10, $nombre, 1);
        $pdf->Cell($anchoPagina * 0.05, 10, $row["ficha"], 1);
        $pdf->Cell($anchoPagina * 0.22, 10, $formacion, 1);
        $pdf->Cell($anchoPagina * 0.07, 10, $row["fecha_prestamo"], 1);
        $pdf->Cell($anchoPagina * 0.1, 10, $row["fecha_devolucion"], 1);
        $pdf->Cell($anchoPagina * 0.05, 10, $row["estado_prestamo"], 1);
        $pdf->Cell($anchoPagina * 0.15, 10, $row["nombre_he"], 1);
        $pdf->Cell($anchoPagina * 0.05, 10, $row["cantidad_prestada"], 1);
        $pdf->Ln();
    }

    // Establecer el nombre del archivo PDF para descargar
    $filename = 'reporte_devoluciones.pdf';

    // Descargar el archivo PDF
    $pdf->Output($filename, 'D');

    // Salir del script
    exit();
} else {
    // Manejo de error si 'documento' no está definido en la sesión
    echo "Error: El documento no está definido en la sesión.";
    exit();
}
?>
