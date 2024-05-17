<?php
require_once("bd/database.php");
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
        $id_tip_doc = $_POST["id_tip_doc"];
        $email = $_POST["email"];
        $ficha = $_POST["ficha"];
        $tyc = $_POST["tyc"];

        // Verificar si la ficha existe en la base de datos
        $validarFicha = $conectar->prepare("SELECT * FROM `formacion` WHERE id_formacion = ?");
        $validarFicha->bindParam(1, $ficha);
        $validarFicha->execute();
        $filaFicha = $validarFicha->fetch(PDO::FETCH_ASSOC);

        if (!$filaFicha) {
            $response = array("status" => "error", "message" => "La ficha ingresada no existe");
        } else {
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
                // Obtener el NIT del administrador (usuario con rol = 1)
                $getNit = $conectar->prepare("SELECT nit FROM usuario WHERE id_rol = 1 LIMIT 1");
                $getNit->execute();
                $nitData = $getNit->fetch(PDO::FETCH_ASSOC);
                $nitAdmin = $nitData ? $nitData['nit'] : null;

                if ($nitAdmin === null) {
                    $response = array("status" => "error", "message" => "No se pudo obtener el NIT del administrador");
                } else {
                    // Encriptar la contraseña
                    $encriptar = password_hash($contrasena, PASSWORD_BCRYPT, ["cost" => 15]);

                    // Insertar en la base de datos `usuario`
                    $insertsql = $conectar->prepare("INSERT INTO `usuario` (`documento`, `contraseña`, `nombre`, `id_tip_doc`, `email`, `id_rol`, `estado`, `nit`, `tyc`) 
                    VALUES (:documento, :contrasena, :nombre, :id_tip_doc, :email, 2, 'activo', :nit, :tyc)");

                    $insertsql->bindParam(':documento', $documento);
                    $insertsql->bindParam(':contrasena', $encriptar);
                    $insertsql->bindParam(':nombre', $nombre);
                    $insertsql->bindParam(':id_tip_doc', $id_tip_doc);
                    $insertsql->bindParam(':email', $email);
                    $insertsql->bindParam(':nit', $nitAdmin);
                    $insertsql->bindParam(':tyc', $tyc);

                    if ($insertsql->execute()) {
                        // Inserción exitosa en `usuario`
                        $response = array("status" => "success", "message" => "Registro exitoso");

                        // Insertar en la base de datos `detalle_usuarios`
                        $insertdet = $conectar->prepare("INSERT INTO `detalle_usuarios` (`documento`, `ficha`) VALUES (:documento, :ficha)");
                        $insertdet->bindParam(':documento', $documento);
                        $insertdet->bindParam(':ficha', $ficha);

                        if (!$insertdet->execute()) {
                            // Si la inserción falla en `detalle_usuarios`
                            $response = array("status" => "error", "message" => "Error al insertar en detalle_usuarios");
                        }
                    } else {
                        // Inserción fallida en `usuario`
                        $response = array("status" => "error", "message" => "Error al insertar en usuario");
                    }
                }
            }
        }
    }
} catch (Exception $e) {
    $response = array("status" => "error", "message" => "Error en el servidor: " . $e->getMessage());
}

echo json_encode($response);
?>
