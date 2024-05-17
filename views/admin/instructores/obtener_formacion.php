<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();

// Obtener el número de ficha enviado por AJAX
$inputFicha = $_GET['ficha'];

// Realizar la búsqueda de la formación asociada al número de ficha
$tip_forQuery = $conectar->prepare("SELECT formacion FROM formacion WHERE id_formacion = :ficha");
$tip_forQuery->bindParam(':ficha', $inputFicha);
$tip_forQuery->execute();
$formacion = $tip_forQuery->fetch(PDO::FETCH_ASSOC);

// Enviar la formación como respuesta
echo json_encode($formacion);
