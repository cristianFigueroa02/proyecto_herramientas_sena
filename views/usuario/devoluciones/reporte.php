<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();
if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}

// Obtener el ID del préstamo enviado desde la solicitud AJAX
$id_prestamo = isset($_GET['id']) ? $_GET['id'] : null;

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["herramientas"])) {
    // Obtener las herramientas seleccionadas y sus cantidades correspondientes
    $herramientas_seleccionadas = $_POST["herramientas"];
    $cantidades_a_devolver = $_POST["cantidad"];
    
    // Validar que ninguna cantidad a devolver sea 0
    foreach ($herramientas_seleccionadas as $id_herramienta) {
        if (isset($cantidades_a_devolver[$id_herramienta]) && (int)$cantidades_a_devolver[$id_herramienta] == 0) {
            echo "<script>alert('Debe seleccionar una cantidad mayor a 0 para poder hacaer el reporte');</script>";
            echo "<script>window.location.href = window.location.href;</script>";
            exit();
        }
    }

    $fecha_actual = date("Y-m-d");

    // Insertar el reporte en la tabla `reportes`
    $stmt_reporte = $conectar->prepare("INSERT INTO reportes (id_prestamo, fecha_reporte, estado_reporte) VALUES (:id_prestamo, :fecha_actual, 'activo')");
    $stmt_reporte->bindParam(':id_prestamo', $id_prestamo);
    $stmt_reporte->bindParam(':fecha_actual', $fecha_actual);
    $stmt_reporte->execute();

    // Obtener el ID del reporte insertado
    $id_reporte = $conectar->lastInsertId();

    // Actualizar el estado del préstamo a 'reportado'
    $stmt_prestamo = $conectar->prepare("UPDATE prestamos SET estado_prestamo = 'reportado' WHERE id_prestamo = :id_prestamo");
    $stmt_prestamo->bindParam(':id_prestamo', $id_prestamo);
    $stmt_prestamo->execute();

    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';

    // Obtener todas las herramientas prestadas con sus cantidades para el préstamo actual
    $stmt_todas_herramientas = $conectar->prepare("SELECT id_herramienta, cantidad_prestada FROM detalle_pres WHERE id_prestamo = :id_prestamo");
    $stmt_todas_herramientas->bindParam(':id_prestamo', $id_prestamo);
    $stmt_todas_herramientas->execute();
    $herramientas_prestadas = $stmt_todas_herramientas->fetchAll(PDO::FETCH_ASSOC);

    // Procesar cada herramienta prestada
    foreach ($herramientas_prestadas as $herramienta_prestada) {
        $id_herramienta = $herramienta_prestada['id_herramienta'];
        $cantidad_prestada = $herramienta_prestada['cantidad_prestada'];

        // Verificar si la herramienta está seleccionada para devolver
        if (in_array($id_herramienta, $herramientas_seleccionadas)) {
            // Obtener la cantidad a devolver para la herramienta seleccionada
            $cantidad_devolver = (int)$cantidades_a_devolver[$id_herramienta];

            // Calcular la cantidad restante después de la devolución
            $cantidad_restante = $cantidad_prestada - $cantidad_devolver;

            // Si hay cantidad restante, actualiza el stock
            if ($cantidad_restante > 0) {
                $stmt_update_stock = $conectar->prepare("UPDATE herrramienta SET stock = stock + :cantidad WHERE id_herramienta = :id_herramienta");
                $stmt_update_stock->bindParam(':cantidad', $cantidad_restante);
                $stmt_update_stock->bindParam(':id_herramienta', $id_herramienta);
                $stmt_update_stock->execute();
            }

            // Realizar la inserción en `deta_reportes`
            $stmt_detalles = $conectar->prepare("INSERT INTO deta_reportes (id_reporte, id_herramienta, descripcion, cantidad_reportada) VALUES (:id_reporte, :id_herramienta, :descripcion, :cantidad)");
            $stmt_detalles->bindParam(':id_reporte', $id_reporte);
            $stmt_detalles->bindParam(':id_herramienta', $id_herramienta);
            $stmt_detalles->bindParam(':descripcion', $descripcion);
            $stmt_detalles->bindParam(':cantidad', $cantidad_devolver);
            $stmt_detalles->execute();

            // Actualizar `cantidad_reportada` en `deta_reportes`
            $stmt_cantidad = $conectar->prepare("UPDATE deta_reportes SET cantidad_reportada = :cantidad WHERE id_reporte = :id_reporte AND id_herramienta = :id_herramienta");
            $stmt_cantidad->bindParam(':cantidad', $cantidad_devolver);
            $stmt_cantidad->bindParam(':id_reporte', $id_reporte);
            $stmt_cantidad->bindParam(':id_herramienta', $id_herramienta);
            $stmt_cantidad->execute();

            // Actualizar `cantidad_prestada` en `detalle_pres` restando `cantidad_devolver`
            $stmt_update_detalle = $conectar->prepare("UPDATE detalle_pres SET cantidad_prestada = cantidad_prestada - :cantidad WHERE id_prestamo = :id_prestamo AND id_herramienta = :id_herramienta");
            $stmt_update_detalle->bindParam(':cantidad', $cantidad_devolver);
            $stmt_update_detalle->bindParam(':id_prestamo', $id_prestamo);
            $stmt_update_detalle->bindParam(':id_herramienta', $id_herramienta);
            $stmt_update_detalle->execute();
        } else {
            // Si la herramienta no está seleccionada para devolver, agrégala al array de herramientas restantes
            $herramientas_restantes[] = [
                'id_herramienta' => $id_herramienta,
                'cantidad_restante' => $cantidad_prestada
            ];
            // Actualizar el stock de las herramientas no seleccionadas sumando las cantidades restantes
            foreach ($herramientas_restantes as $herramienta_restante) {
                $id_herramienta = $herramienta_restante['id_herramienta'];
                $cantidad_restante = $herramienta_restante['cantidad_restante'];

                // Actualizar el stock sumando la cantidad restante
                $stmt_update_stock_no_sel = $conectar->prepare("UPDATE herrramienta SET stock = stock + :cantidad WHERE id_herramienta = :id_herramienta");
                $stmt_update_stock_no_sel->bindParam(':cantidad', $cantidad_restante);
                $stmt_update_stock_no_sel->bindParam(':id_herramienta', $id_herramienta);
                $stmt_update_stock_no_sel->execute();
            }
        }
    }
    echo '<script>alert("Reporte hecho con éxito.");</script>';
    echo '<script>window.location= "../reportes/lista_reportes.php"</script>';
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
    <main class="contenedor sombra">
        <div class="container mt-5">
            <h2>Generar reporte</h2>
            <form method="POST" onsubmit="return validarSeleccion()">
                <!-- Agregar checkbox para herramientas -->
                <div class="form-group">
                    <label for="herramientas">Herramientas:</label><br>
                    <?php
                    // Suponiendo que $id_prestamo contiene el ID del préstamo
                    $query = "SELECT * 
        FROM detalle_pres
        INNER JOIN prestamos ON detalle_pres.id_prestamo = prestamos.id_prestamo 
        INNER JOIN herrramienta ON detalle_pres.id_herramienta = herrramienta.id_herramienta
        WHERE detalle_pres.id_prestamo = :id_prestamo";
                    $statement = $conectar->prepare($query);
                    $statement->bindParam(':id_prestamo', $id_prestamo);
                    $statement->execute();
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

                    // Verificar si se encontraron detalles asociados al préstamo
                    if ($result) {
                        foreach ($result as $row) {
                            $id_herramienta = $row['id_herramienta'];
                            $nombre_herramienta = $row['nombre_he'];
                            $cantidad_prestada = $row['cantidad_prestada']; // Cantidad prestada
                            // Imprimir nombre de la herramienta y usar el ID como valor del checkbox
                            echo '<div style="margin-bottom: 10px;">';
                            echo '<label><input type="checkbox" name="herramientas[]" value="' . $id_herramienta . '"> ' . $nombre_herramienta . '</label>';
                            echo '<label for="cantidad[' . $id_herramienta . ']" style="margin-left: 10px;">Cantidad a reportar:</label>';
                            // Establece el valor del campo de entrada al valor de la cantidad prestada
                            echo '<input type="number" name="cantidad[' . $id_herramienta . ']" min="0" max="' . $cantidad_prestada . '" value="' . $cantidad_prestada . '" style="margin-left: 10px;">';
                            echo '</div>';
                        }
                    } else {
                        echo "No se encontraron herramientas asociadas a este préstamo.";
                    }
                    ?>
                </div>

                <!-- Agregar textarea para describir lo sucedido -->
<div class="form-group">
    <label for="descripcion">Descripción:</label><br>
    <textarea id="descripcion" name="descripcion" rows="4" cols="50" required></textarea>
</div>


                <!-- Agregar botones de enviar y volver -->
                <input type="submit" class="btn btn-success" value="Registrar">
                <input type="hidden" name="registro" value="formu">
                <a href="lista.php" class="btn btn-danger">Volver</a>
            </form>

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


        </div>
    </main>
    <!-- footer -->
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