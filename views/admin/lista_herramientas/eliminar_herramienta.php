<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

// Verifica si se ha proporcionado un ID en la URL
if(isset($_GET['id'])) {
    // Recupera el ID de la URL
    $id = $_GET['id'];

    // Consulta para eliminar la entidad
    $sql = "DELETE FROM herrramienta WHERE id_herramienta = $id";

    // Ejecuta la consulta
    if ($conectar->query($sql) === TRUE) {
        // Si la consulta se ejecuta correctamente, redirige y luego termina el script
        header("Location: lista.php");
        exit();
    } 
    header("Location: lista.php");

} else {
    // Si no se proporciona un ID, redirige a alguna página de error o a donde sea necesario
    header("Location: error.php");
    exit();
}
?>
