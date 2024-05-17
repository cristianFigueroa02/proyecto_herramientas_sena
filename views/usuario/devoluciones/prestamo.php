<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();
if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}
// Recibe la cadena de consulta
$herramientas_restantes = $_GET;

// Array para almacenar los datos de las herramientas restantes
$datos_herramientas_restantes = [];

// Extraer IDs de herramientas restantes de `$herramientas_restantes`
$ids_herramientas = array_column($herramientas_restantes, 'id_herramienta');

// Verificar si hay IDs de herramientas restantes
if (!empty($ids_herramientas)) {
    // Construir la cláusula WHERE con placeholders para los IDs de herramientas
    $placeholders = implode(',', array_fill(0, count($ids_herramientas), '?'));

    // Crear la consulta preparada
    $query = "SELECT * FROM herrramienta JOIN categoria ON herrramienta.id_cate = categoria.id_cate WHERE id_herramienta IN ($placeholders)";
    $stmt = $conectar->prepare($query);

    // Vincular los IDs de herramientas restantes a los marcadores de posición
    foreach ($ids_herramientas as $index => $id) {
        $stmt->bindValue($index + 1, $id);
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados de la consulta
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Almacenar los resultados en `$datos_herramientas_restantes`
    foreach ($resultados as $resultado) {
        $datos_herramientas_restantes[] = $resultado;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ids_seleccionados"])) {
    // Obtener los IDs seleccionados, los días a sumar, y las cantidades seleccionadas
    $ids_seleccionados = $_POST["ids_seleccionados"];
    $dias = $_POST["dias"];
    $cantidades_seleccionadas = $_POST["cantidad"];

    // Calcular la suma total de las cantidades seleccionadas
    $suma_cantidades = array_sum($cantidades_seleccionadas);

    // Verificar si la suma total de las cantidades es mayor a 5
    if ($suma_cantidades > 5) {
        echo '<script>alert("No se puede realizar el préstamo. Solo se permite prestar hasta 5 herramientas en total.");</script>';
        echo '<script>window.location= "lista_herramientas.php"</script>';
        exit(); // Finalizar la ejecución si la suma es mayor a 5
    }

    // Calcular la fecha actual y la nueva fecha sumando los días seleccionados
    $fecha_actual = date("Y-m-d");
    $nueva_fecha = date("Y-m-d", strtotime($fecha_actual . " +$dias days"));

    // Verifica si el documento está definido en la sesión
    if (isset($_SESSION['documento'])) {
        $documento = $_SESSION['documento'];

        // Inserta los datos en la tabla de préstamos
        $stmt = $conectar->prepare("INSERT INTO prestamos (documento, fecha_prestamo, fecha_devolucion, estado_prestamo) VALUES (:documento, :fecha_actual, :nueva_fecha, 'prestado')");
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':fecha_actual', $fecha_actual);
        $stmt->bindParam(':nueva_fecha', $nueva_fecha);
        $stmt->execute();

        // Obtener el ID del préstamo recién insertado
        $id_prestamo = $conectar->lastInsertId();

        // Procesar cada herramienta seleccionada
        foreach ($ids_seleccionados as $index => $id_herramienta) {
            // Obtener la cantidad seleccionada para cada herramienta
            $cantidad_seleccionada = $cantidades_seleccionadas[$index];

            // Insertar los detalles del préstamo en la tabla `detalle_pres`
            $stmt1 = $conectar->prepare("INSERT INTO detalle_pres (id_prestamo, id_herramienta, cantidad_prestada) VALUES (:id_prestamo, :id_herramienta, :cantidad_prestada)");
            $stmt1->bindParam(':id_prestamo', $id_prestamo);
            $stmt1->bindParam(':id_herramienta', $id_herramienta);
            $stmt1->bindParam(':cantidad_prestada', $cantidad_seleccionada);
            $stmt1->execute();

        }

        // Mensaje de confirmación
        echo "<script>alert('Se han prestado todas las herramientas correctamente. ID de préstamo: $id_prestamo. Fecha de devolución: $nueva_fecha');</script>";
        echo '<script> window.location= "../prestamos/prestamos_usuario/lista_prestamos.php"</script>';
    } else {
        // Si el documento no está definido en la sesión, mostrar error
        echo '<script>alert("Error: El documento no está definido en la sesión.");</script>';
        echo '<script>window.location= "lista_herramientas.php"</script>';
        exit(); // Salir del script si ocurre un error
    }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- mobile metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
    <!-- site metas -->
    <title>limelight</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="../../../css/style.css">
    <!-- Responsive-->
    <link rel="stylesheet" href="../../../css/responsive.css">
    <!-- styles usuario -->
    <link rel="stylesheet" href="../../../css/styles_usuario.css">
    <!-- fevicon -->
    <link rel="icon" href="../../../images/fevicon.png" type="image/gif" />
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="../../../css/jquery.mCustomScrollbar.min.css">
    <!-- Tweaks for older IEs-->
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
</head>

<body class="main-layout in_page">
    <!-- header -->
    <header>
        <!-- header inner -->
        <div class="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-3 col logo_section">
                        <div class="full">
                            <div class="center-desk">
                                <div class="logo">
                                    <a href="#"><img src="../../../images/Sena_Colombia_logo.svg.png" alt="#" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
<div class="container mt-3">
    <h2>Herramientas a prestar nuevamente</h2>
    <div class="table-responsive">
        <form method="post" id="formHerramientas">
            <table class="table table-sm table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr style="text-transform: uppercase;">
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Cantidad a prestar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Mapeamos los `id_herramienta` con `cantidad_restante` de la URL
                    $cantidades_restantes = [];
                    foreach ($herramientas_restantes as $key => $value) {
                        if (is_array($value)) {
                            $id_herramienta = $value['id_herramienta'];
                            $cantidad_restante = $value['cantidad_restante'];
                            $cantidades_restantes[$id_herramienta] = $cantidad_restante;
                        }
                    }

                    // Iterar sobre `$datos_herramientas_restantes` y mostrar sus datos en la tabla
                    foreach ($datos_herramientas_restantes as $herramienta) {
                        $id_herramienta = $herramienta['id_herramienta'];

                        // Obtener la cantidad restante para este `id_herramienta`
                        $cantidad_restante = isset($cantidades_restantes[$id_herramienta]) ? $cantidades_restantes[$id_herramienta] : 0;

                        // Mostrar los detalles de la herramienta en la tabla
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($herramienta['nombre_he']) . '</td>';
                        echo '<td>' . htmlspecialchars($herramienta['categoria']) . '</td>';
                        echo '<td>' . htmlspecialchars($herramienta['estado']) . '</td>';
                        echo '<td>';
                        echo '<input type="text" name="cantidad[]" 
                               value="' . $cantidad_restante . '" 
                               class="form-control mt-2" 
                               readonly>';
                        echo '</td>';
                        echo '<!-- Campo oculto para almacenar IDs seleccionados -->';
                        echo '<input type="hidden" name="ids_seleccionados[]" value="' . htmlspecialchars($id_herramienta) . '">';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
            <label for="dias">Cuántos días quieres prestar:</label>
            <select name="dias" id="dias" class="form-control mb-2" style="font-size: 15px;">
                <?php for ($i = 1; $i <= 7; $i++) { ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?> día<?php echo ($i > 1) ? 's' : ''; ?></option>
                <?php } ?>
            </select>
            <button type="submit" class="btn btn-success" style="padding: 5px 20px;">Prestar</button>
        </form>
    </div>
</div>



    <script>
                function validarSeleccion() {
                    var checkboxes = document.querySelectorAll('input[name="herramientas[]"]');
                    var seleccionado = false;
                    for (var i = 0; i < checkboxes.length; i++) {
                        if (checkboxes[i].checked) {
                            seleccionado = true;
                            break;
                        }
                    }
                    if (!seleccionado) {
                        alert("Por favor, selecciona al menos una herramienta.");
                        return false;
                    }
                    return true;
                }
            </script>

    <footer>
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class=" col-md-3 col-sm-6">
                        <ul class="social_icon">
                            <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                        </ul>
                        <p class="variat pad_roght2">There are many variat
                            ions of passages of L
                            orem Ipsum available
                            , but the majority h
                            ave suffered altera
                            tion in some form, by
                        </p>
                    </div>
                    <div class=" col-md-3 col-sm-6">
                        <h3>LET US HELP YOU </h3>
                        <p class="variat pad_roght2">There are many variat
                            ions of passages of L
                            orem Ipsum available
                            , but the majority h
                            ave suffered altera
                            tion in some form, by
                        </p>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <h3>INFORMATION</h3>
                        <ul class="link_menu">
                        </ul>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <h3>OUR Design</h3>
                        <p class="variat">There are many variat
                            ions of passages of L
                            orem Ipsum available
                            , but the majority h
                            ave suffered altera
                            tion in some form, by
                        </p>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <div class="container">
                    <div class="row">
                        <div class="col-md-10 offset-md-1">
                            <p>© 2019 All Rights Reserved. Design by <a href="https://html.design/"> Cristian Figueroa</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end footer -->
    <!-- Javascript files-->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <!-- sidebar -->
    <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>