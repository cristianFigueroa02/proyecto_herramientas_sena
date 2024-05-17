<?php
require_once("../../../bd/database.php");
require_once("../../../fpdf.php");

$db = new Database();
$conectar = $db->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php");
    exit();
}

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
        INNER JOIN detalle_usuarios ON usuario.documento=detalle_usuarios.documento
        WHERE usuario.id_rol = 2
    ");
    $usua->execute();
    $asigna = $usua->fetchAll(PDO::FETCH_ASSOC);

    $pdf = new FPDF('L');
    $pdf->AddPage();

    $pdf->SetTitle('Reporte de Aprendices'); // Cambio en el título del documento

    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(40, 10, 'Documento', 1);
    $pdf->Cell(60, 10, 'Nombre', 1);
    $pdf->Cell(40, 10, 'Ficha', 1);
    $pdf->Cell(60, 10, 'Direccion', 1);
    $pdf->Cell(60, 10, 'Empresa', 1);
    $pdf->Cell(30, 10, 'Estado', 1);
    $pdf->Ln();

    foreach ($asigna as $usua) {
        $pdf->Cell(40, 10, $usua["documento"], 1);
        $pdf->Cell(60, 10, $usua["nombre"], 1);
        $pdf->Cell(40, 10, $usua["ficha"], 1);
        $pdf->Cell(60, 10, $usua["email"], 1);
        $pdf->Cell(60, 10, $usua["nombre_empre"], 1);
        $pdf->Cell(30, 10, $usua["estado"], 1);
        $pdf->Ln();
    }

    $filename = 'reporte_aprendices.pdf';

    $pdf->Output($filename, 'D');

    exit();
} else {
    echo "Error: El documento no está definido en la sesión.";
    exit();
}
?>
