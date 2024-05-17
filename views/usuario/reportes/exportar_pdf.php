<?php
require_once("../../../bd/database.php");
require_once("../../../fpdf.php"); // Asegúrate de que la ruta a fpdf.php sea correcta

$db = new Database();
$conectar = $db->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php");
    exit();
}

// Verifica si la clave 'documento' está definida en la sesión antes de usarla
if (isset($_SESSION['documento'])) {
    $documento = $_SESSION['documento'];

    // Realizar la consulta 'usua' para obtener los datos de reportes y prestamos relacionados
    $usua = $conectar->prepare("SELECT reportes.id_reporte, prestamos.documento, reportes.fecha_reporte, reportes.estado_reporte
                                FROM reportes
                                INNER JOIN prestamos ON reportes.id_prestamo = prestamos.id_prestamo
                                INNER JOIN usuario ON prestamos.documento = usuario.documento
                                WHERE prestamos.estado_prestamo = 'reportado' AND usuario.documento = :documento");
    $usua->bindParam(':documento', $documento);
    $usua->execute();
    $asigna = $usua->fetchAll(PDO::FETCH_ASSOC);

    // Crear un objeto FPDF
    $pdf = new FPDF();

    // Agregar una página al PDF
    $pdf->AddPage();

    // Establecer fuente y tamaño de fuente
    $pdf->SetFont('Arial', '', 12);

    // Agregar encabezados de columna al PDF
    $pdf->Cell(40, 10, 'Número de reporte');
    $pdf->Cell(40, 10, 'Documento');
    $pdf->Cell(40, 10, 'Fecha de reporte');
    $pdf->Cell(40, 10, 'Estado del reporte');
    $pdf->Ln(); // Nueva línea para el siguiente registro

    // Recorrer los datos obtenidos y agregarlos al PDF
    foreach ($asigna as $usua) {
        $pdf->Cell(40, 10, $usua['id_reporte']);
        $pdf->Cell(40, 10, $usua['documento']);
        $pdf->Cell(40, 10, $usua['fecha_reporte']);
        $pdf->Cell(40, 10, $usua['estado_reporte']);
        $pdf->Ln(); // Nueva línea para el siguiente registro
    }

    // Descargar el archivo PDF
    $filename = 'reporte_prestamos_reportados.pdf';
    $pdf->Output($filename, 'D');

    // Salir del script
    exit();
} else {
    // Manejo de error si 'documento' no está definido en la sesión
    echo "Error: El documento no está definido en la sesión.";
    exit();
}
?>