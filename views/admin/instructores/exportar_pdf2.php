<?php
// Incluir el archivo de la base de datos y la biblioteca FPDF
require_once("../../../bd/database.php");
require_once("../../../fpdf.php");

// Conexión a la base de datos
$db = new Database();
$conectar = $db->conectar();
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}

// Obtener la información de los instructores asociados
$usua = $conectar->prepare("
    SELECT * 
    FROM usuario
    INNER JOIN detalle_usuarios ON usuario.documento = detalle_usuarios.documento
    INNER JOIN rol ON usuario.id_rol = rol.id_rol
    WHERE usuario.id_rol = 4
");
$usua->execute();
$asigna = $usua->fetchAll(PDO::FETCH_ASSOC);

// Crear un nuevo objeto FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Establecer el título del documento
$pdf->SetTitle('Reporte de Instructores Asociados');

// Definir la fuente y el tamaño del texto
$pdf->SetFont('Arial', '', 12);

// Agregar encabezados de columna
$pdf->Cell(30, 10, 'Documento', 1);
$pdf->Cell(50, 10, 'Nombre', 1);
$pdf->Cell(60, 10, 'Direccion email', 1);
$pdf->Cell(40, 10, 'ficha', 1);
$pdf->Cell(40, 10, 'Estado', 1);
$pdf->Ln();

// Agregar datos de los instructores asociados a la hoja de cálculo
foreach ($asigna as $usua) {
    $pdf->Cell(30, 10, $usua["documento"], 1);
    $pdf->Cell(50, 10, $usua["nombre"], 1);
    $pdf->Cell(60, 10, $usua["email"], 1);
    $pdf->Cell(40, 10, $usua["ficha"], 1);
    $pdf->Cell(40, 10, $usua["estado"], 1);
    $pdf->Ln();
}

// Establecer el nombre del archivo PDF para descargar
$filename = 'reporte_instructores_asociados.pdf';

// Descargar el archivo PDF
$pdf->Output($filename, 'D');

// Salir del script
exit();
?>
