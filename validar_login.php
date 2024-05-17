<?php
require_once("bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos del formulario
    $documento = $_POST["documento"];
    $contrasena = $_POST["contrasena"];

    $usuarioQuery = $conectar->prepare("SELECT * FROM usuario, licencia WHERE licencia.nit = usuario.nit AND licencia.estado = 'activo' AND documento = ?");
    $usuarioQuery->execute([$documento]);
    $usuario = $usuarioQuery->fetch();

    if ($usuario) {
        if (password_verify($contrasena, $usuario['contraseña'])) {
            if ($usuario['estado'] == "activo") {
                $_SESSION['documento'] = $usuario['documento'];
                $_SESSION['rol'] = $usuario['id_rol'];
                $_SESSION['estado'] = $usuario['estado'];

                $response = [
                    'success' => true,
                    'rol' => $_SESSION['rol']
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'La contraseña es incorrecta o la licencia no está activa'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'La contraseña es incorrecta o la licencia no está activa'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Usuario no encontrado'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
