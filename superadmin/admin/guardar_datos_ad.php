<?php
require_once("../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();

header('Content-Type: application/json'); // Establecer el encabezado JSON desde el inicio

$response = [];

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener datos del formulario
        $documento = $_POST["documento"];
        $contrasena = $_POST["contrasena"];
        $nombre = $_POST["nombre"];
        $email = $_POST["email"];
        $empresa = $_POST["empresa"];
        $tyc = $_POST["tyc"];

        // Verificar si ya existe un registro con el mismo documento, nombre o email
        $validar = $conectar->prepare("SELECT * FROM `usuario` WHERE documento = ? OR nombre = ? OR email = ?");
        $validar->bindParam(1, $documento);
        $validar->bindParam(2, $nombre);
        $validar->bindParam(3, $email);
        $validar->execute();
        $fila1 = $validar->fetch(PDO::FETCH_ASSOC);

        if (empty($documento) || empty($nombre) || empty($contrasena)) {
            $response = array("status" => "error", "message" => "Existen campos vacíos");
        } elseif (empty($tyc)) {
            $response = array("status" => "error", "message" => "Debes aceptar los términos y condiciones");
        } elseif ($fila1) {
            $response = array("status" => "error", "message" => "Documento, nombre o email ya existen, por favor cámbialos");
        } else {
            // Encriptar la contraseña
            $encriptar = password_hash($contrasena, PASSWORD_BCRYPT, ["cost" => 15]);

            // Insertar en la base de datos `usuario`
            $insertsql = $conectar->prepare("INSERT INTO `usuario` (`documento`, `contraseña`, `nombre`, `id_tip_doc`, `email`, `id_rol`, `estado`, `nit`, `tyc`) 
                VALUES (:documento, :contrasena, :nombre, :id_tip_doc, :email, 1, 'activo', :empresa, :tyc)");

            $insertsql->bindParam(':documento', $documento);
            $insertsql->bindParam(':contrasena', $encriptar);
            $insertsql->bindParam(':nombre', $nombre);
            $insertsql->bindParam(':id_tip_doc', $id_tip_doc);
            $insertsql->bindParam(':empresa', $empresa);
            $insertsql->bindParam(':email', $email);
            $insertsql->bindParam(':tyc', $tyc);

            $insertsql->execute();

            $response = array("status" => "success", "message" => "¡Registro exitoso!");
        }
    }
} catch (Exception $e) {
    $response = array("status" => "error", "message" => "Error en el servidor: " . $e->getMessage());
}

echo json_encode($response);
?>
