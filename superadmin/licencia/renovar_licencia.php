<?php
require_once("../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

// Validar si la variable de sesión no está establecida
if (!isset($_SESSION['codigo_valido']) || !$_SESSION['codigo_valido']) {
    // Redirigir al usuario a una URL específica si no se cumple la validación
    header("Location: ../index.php");
    exit; // Detener la ejecución del script
}

// Verificar si se recibió el NIT de la empresa desde el formulario
if (isset($_GET['nit_empresa'])) {
    // Obtener el NIT de la empresa desde el formulario
    $nit = $_GET['nit_empresa'];

    try {
        // Generar un ID único utilizando la función uniqid()
        $licencia = uniqid();

        // Obtener la fecha y hora actual
        $fecha_inicio = date("Y-m-d H:i:s");

        // Calcular la fecha de finalización (1 año después de la fecha de inicio)
        $fecha_fin = date("Y-m-d H:i:s", strtotime($fecha_inicio . " + 1 year"));

        // Definir el estado como "activo"
        $estado = "activo";

        // Insertar la nueva licencia en la base de datos
        $insertLicenciaQuery = $conectar->prepare("
            INSERT INTO licencia (licencia, estado, fecha_inicio, fecha_fin, nit) 
            VALUES (:licencia, :estado, :fecha_inicio, :fecha_fin, :nit)
        ");
        $insertLicenciaQuery->bindParam(':licencia', $licencia);
        $insertLicenciaQuery->bindParam(':estado', $estado);
        $insertLicenciaQuery->bindParam(':fecha_inicio', $fecha_inicio);
        $insertLicenciaQuery->bindParam(':fecha_fin', $fecha_fin);
        $insertLicenciaQuery->bindParam(':nit', $nit);
        $insertLicenciaQuery->execute();

        if ($insertLicenciaQuery->rowCount() > 0) {
            echo '<script>alert("Licencia renovada exitosamente.");</script>';
            echo '<script>window.location= "lista_licencia.php"</script>';
        } else {
            echo '<script>alert("Error al crear la licencia.");</script>';
            echo '<script>window.location= "lista_licencia.php"</script>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // Si no se recibió el NIT de la empresa desde el formulario, redirigir a la página anterior
    header("Location: lista_licencia.php");
    exit();
}
?>
