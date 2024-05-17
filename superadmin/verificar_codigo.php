<?php
session_start();

// Definir una variable global para indicar si el código es válido
$GLOBALS['codigo_valido'] = false;

// Verificar si se recibió un código
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['codigo'])) {
    $codigo_correcto = "2500591"; // Código correcto
    $codigo_ingresado = $_POST['codigo'];

    if ($codigo_ingresado === $codigo_correcto) {
        $_SESSION['codigo_valido'] = true; // Establecer la variable de sesión
        $GLOBALS['codigo_valido'] = true; // Establecer la variable global como verdadera
        echo "valido"; // Devolver respuesta "valido"
    } else {
        echo "invalido"; // Devolver respuesta "invalido"
    }
} else {
    // Si no se recibió un código, devolver respuesta "invalido"
    echo "invalido";
}
?>
