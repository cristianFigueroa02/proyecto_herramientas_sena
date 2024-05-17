<?php
require_once("../../../bd/database.php");
require_once("../../../fpdf.php"); // Incluir el archivo fpdf.php de la biblioteca FPDF

$db = new Database();
$conectar = $db->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}

// Verifica si la clave 'documento' está definida en la sesión antes de usarla
if (isset($_SESSION['documento'])) {
    $documento = $_SESSION['documento'];

    $usuarioQuery = $conectar->prepare("SELECT * FROM usuario WHERE documento = :documento");
    $usuarioQuery->bindParam(':documento', $documento);
    $usuarioQuery->execute();
    $usuario = $usuarioQuery->fetch();

    $usua = $conectar->prepare("
        SELECT * 
        FROM usuario
        INNER JOIN empresa ON usuario.nit=empresa.nit
        INNER JOIN rol ON usuario.id_rol = rol.id_rol
        WHERE usuario.id_rol = 4
    ");
    $usua->execute();
    $asigna = $usua->fetchAll(PDO::FETCH_ASSOC);

    // Crear un nuevo objeto FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Establecer el título del documento
    $pdf->SetTitle('Reporte de Instructores');

    // Definir la fuente y el tamaño del texto
    $pdf->SetFont('Arial', '', 12);

    // Agregar encabezados de columna
    $pdf->Cell(30, 10, 'Documento', 1);
    $pdf->Cell(40, 10, 'Nombre', 1);
    $pdf->Cell(60, 10, 'Direccion', 1);
    $pdf->Cell(40, 10, 'Empresa', 1);
    $pdf->Cell(30, 10, 'Estado', 1);
    $pdf->Ln();

    // Agregar datos de la tabla a la hoja de cálculo
    foreach ($asigna as $usua) {
        $pdf->Cell(30, 10, $usua["documento"], 1);
        $pdf->Cell(40, 10, $usua["nombre"], 1);
        $pdf->Cell(60, 10, $usua["email"], 1);
        $pdf->Cell(40, 10, $usua["nombre_empre"], 1);
        $pdf->Cell(30, 10, $usua["estado"], 1);
        $pdf->Ln();
    }

    // Establecer el nombre del archivo PDF para descargar
    $filename = 'reporte_instructores.pdf';

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
