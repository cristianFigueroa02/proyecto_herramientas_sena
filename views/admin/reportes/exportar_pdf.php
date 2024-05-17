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

    // Consultar los datos necesarios de las tablas mencionadas
    $consulta = $conectar->prepare("
        SELECT 
    reportes.id_reporte,
    MAX(reportes.id_prestamo) AS id_prestamo,
    MAX(reportes.fecha_reporte) AS fecha_reporte,
    MAX(reportes.estado_reporte) AS estado_reporte,
    MAX(prestamos.documento) AS prestamo_documento,
    MAX(detalle_usuarios.documento) AS detalle_documento,
    MAX(detalle_usuarios.ficha) AS ficha,
    MAX(formacion.formacion) AS formacion,
    MAX(usuario.nombre) AS nombre,
    MAX(deta_reportes.cantidad_reportada) AS cantidad_reportada,
    herrramienta.nombre_he AS nombre_he
FROM 
    reportes 
INNER JOIN 
    prestamos ON reportes.id_prestamo = prestamos.id_prestamo
INNER JOIN 
    detalle_usuarios ON prestamos.documento = detalle_usuarios.documento
INNER JOIN 
    formacion ON detalle_usuarios.ficha = formacion.id_formacion
INNER JOIN 
    usuario ON prestamos.documento = usuario.documento
INNER JOIN 
    deta_reportes ON reportes.id_reporte = deta_reportes.id_reporte
INNER JOIN 
    herrramienta ON deta_reportes.id_herramienta = herrramienta.id_herramienta
WHERE 
    reportes.estado_reporte = 'activo' OR reportes.estado_reporte = 'inactivo'
ORDER BY
    reportes.fecha_reporte;

    ");
    $consulta->execute();
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Crear un nuevo objeto FPDF con un tamaño de página más grande (A3, orientación horizontal)
    $pdf = new FPDF('L', 'mm', 'A3');
    $pdf->AddPage();

    // Establecer el título del documento
    $pdf->SetTitle('Reporte de Reportes');

    // Definir la fuente y el tamaño del texto
    $pdf->SetFont('Arial', '', 12);

    // Obtener el ancho de la página
    $anchoPagina = $pdf->GetPageWidth();

    // Agregar título
    $pdf->Cell($anchoPagina, 10, 'Reporte de Reportes Activos e Inactivos', 0, 1, 'C');
    $pdf->Ln(10); // Agregar espacio entre título y tabla

    // Calcular el ancho de cada columna
    $anchoDocumento = $anchoPagina * 0.1;
    $anchoNombre = $anchoPagina * 0.15;
    $anchoFicha = $anchoPagina * 0.05;
    $anchoFormacion = $anchoPagina * 0.22;
    $anchoFechaReporte = $anchoPagina * 0.1;
    $anchoEstadoReporte = $anchoPagina * 0.1;
    $anchoCantidadReportada = $anchoPagina * 0.1;
    $anchoNombreHerramienta = $anchoPagina * 0.15;

    // Agregar encabezados de columna
    $pdf->Cell($anchoDocumento, 10, 'Documento', 1);
    $pdf->Cell($anchoNombre, 10, 'Nombre', 1);
    $pdf->Cell($anchoFicha, 10, 'Ficha', 1);
    $pdf->Cell($anchoFormacion, 10, 'Formación', 1);
    $pdf->Cell($anchoFechaReporte, 10, 'Fecha reporte', 1);
    $pdf->Cell($anchoEstadoReporte, 10, 'Estado Reporte', 1);
    $pdf->Cell($anchoCantidadReportada, 10, 'Cantidad Reportada', 1);
    $pdf->Cell($anchoNombreHerramienta, 10, 'Herramienta', 1);
    $pdf->Ln();

    // Agregar datos de la tabla a la hoja de cálculo
    foreach ($datos as $row) {
        $pdf->Cell($anchoDocumento, 10, $row["prestamo_documento"], 1);
        $pdf->Cell($anchoNombre, 10, $row["nombre"], 1);
        $pdf->Cell($anchoFicha, 10, $row["ficha"], 1);
        $pdf->Cell($anchoFormacion, 10, $row["formacion"], 1);
        $pdf->Cell($anchoFechaReporte, 10, $row["fecha_reporte"], 1);
        $pdf->Cell($anchoEstadoReporte, 10, $row["estado_reporte"], 1);
        $pdf->Cell($anchoCantidadReportada, 10, $row["cantidad_reportada"], 1);
        $pdf->Cell($anchoNombreHerramienta, 10, $row["nombre_he"], 1);
        $pdf->Ln();
    }

    // Establecer el nombre del archivo PDF para descargar
    $filename = 'reporte_reportes.pdf';

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
