<?php
require_once("../../../../bd/database.php");
require_once("../../../../fpdf.php"); // Ruta correcta hacia el archivo fpdf.php

$db = new Database();
$conectar = $db->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}

// Verifica si la clave 'documento' está definida en la sesión antes de usarla
if (isset($_SESSION['documento'])) {
    $documento = $_SESSION['documento'];

    // Consulta para obtener los datos de los préstamos
    $usua = $conectar->prepare("SELECT * FROM prestamos INNER JOIN usuario ON prestamos.documento = usuario.documento WHERE prestamos.estado_prestamo = 'reportado' AND usuario.documento='$documento'");
    $usua->execute();
    $asigna = $usua->fetchAll(PDO::FETCH_ASSOC);
    // Crear un nuevo objeto FPDF
    $pdf = new FPDF();

    // Agregar una página
    $pdf->AddPage();

    // Establecer la fuente
    $pdf->SetFont('Arial', '', 12);

    // Agregar encabezados de columna
    $pdf->Cell(40, 10, 'Documento', 1);
    $pdf->Cell(40, 10, 'Fecha inicio préstamo', 1);
    $pdf->Cell(40, 10, 'Fecha fin préstamo', 1);
    $pdf->Cell(40, 10, 'Estado del préstamo', 1);
    $pdf->Ln(); // Nueva línea después de la cabecera

    // Agregar datos de la tabla al PDF
    foreach ($asigna as $usua) {
        $pdf->Cell(40, 10, $usua["documento"], 1);
        $pdf->Cell(40, 10, $usua["fecha_prestamo"], 1);
        $pdf->Cell(40, 10, $usua["fecha_devolucion"], 1);
        $pdf->Cell(40, 10, $usua["estado_prestamo"], 1);
        $pdf->Ln(); // Nueva línea después de cada fila
    }

    // Establecer el nombre del archivo PDF para descargar
    $filename = 'reporte_prestamos.pdf';

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