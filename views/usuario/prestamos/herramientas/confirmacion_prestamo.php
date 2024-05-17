<?php
require_once("../../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ids"])) {
    // Verifica si $_POST["ids"] es un array o no
    if (is_array($_POST["ids"])) {
        // $_POST["ids"] es un array, puedes proceder con el procesamiento
        $ids_seleccionados = $_POST["ids"];
    } else {
        // Si $_POST["ids"] no es un array, conviértelo en uno
        $ids_seleccionados = array($_POST["ids"]);
    }




    // Verifica si la clave 'documento' está definida en la sesión antes de usarla
    if (isset($_SESSION['documento'])) {
        $documento = $_SESSION['documento'];

        $sql = "SELECT COUNT(*) AS total
                FROM reportes
                INNER JOIN prestamos ON reportes.id_prestamo = prestamos.id_prestamo
                WHERE reportes.estado_reporte = 'activo' AND prestamos.documento = :documento";

        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':documento', $documento);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $total_filas_activas = $row["total"];

            // Verificar si el total de filas activas es igual o mayor a tres
            if ($total_filas_activas >= 1) {
                echo '<script>alert("Tienes 1 reporte, no puedes hacer más préstamos");</script>';
                echo '<script>window.location="lista_herramientas.php"</script>';
                exit();
            }
        }

        $mysql = "SELECT COUNT(*) AS total
                FROM prestamos
                WHERE prestamos.estado_prestamo = 'prestado' AND prestamos.documento = :documento";

        $conteo = $conectar->prepare($mysql);
        $conteo->bindParam(':documento', $documento);
        $conteo->execute();
        $total = $conteo->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $total_filas = $total["total"];

            // Verificar si el total de filas activas es igual o mayor a tres
            if ($total_filas >= 5) {
                echo '<script>alert("Tienes varios prestamos activos, primero devuelvelos");</script>';
                echo '<script>window.location="lista_herramientas.php"</script>';
                exit();
            }
        }



        $usuarioQuery = $conectar->prepare("SELECT * FROM usuario WHERE documento = :documento");
        $usuarioQuery->bindParam(':documento', $documento);
        $usuarioQuery->execute();
        $usuario = $usuarioQuery->fetch();

        if ($usuario) {
            // Crear una cadena de placeholders para el número de IDs seleccionados
            $placeholders = implode(',', array_fill(0, count($ids_seleccionados), '?'));

            // Preparar la consulta con los placeholders
            $query = "SELECT * FROM herrramienta JOIN categoria ON herrramienta.id_cate = categoria.id_cate WHERE id_herramienta IN ($placeholders)";
            $usua = $conectar->prepare($query);

            // Asociar cada valor con su marcador de posición correspondiente
            foreach ($ids_seleccionados as $index => $id) {
                $usua->bindValue(($index + 1), $id);
            }

            // Ejecutar la consulta
            $usua->execute();

            // Obtener los resultados
            $asigna = $usua->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Manejo de error si el usuario no existe
            echo "<script>alert('Error: No se encontró el usuario.');</script>";
            exit; // Salir del script si ocurre un error
        }
    } else {
        // Manejo de error si 'documento' no está definido en la sesión
        echo "<script>alert('Error: El documento no está definido en la sesión.');</script>";
        exit; // Salir del script si ocurre un error
    }
}

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ids_seleccionados"])) {
    // Obtener los IDs seleccionados, los días a sumar y las cantidades seleccionadas
    $ids_seleccionados = $_POST["ids_seleccionados"];
    $dias = $_POST["dias"];
    $stock = $_POST["cantidad"];

    // Verificar que ninguna cantidad sea igual a 0
    foreach ($stock as $cantidad) {
        if ($cantidad == 0  || empty($cantidad)) {
            // Si alguna cantidad es igual a 0, muestra un mensaje de alerta
            echo '<script>alert("No se permite una cantidad igual a 0. Por favor, verifica los datos ingresados.");</script>';
            echo '<script>window.location= "lista_herramientas.php"</script>';
            exit(); // Terminar la ejecución si se encuentra una cantidad igual a 0
        }
    }


    // Calcular la suma total de las cantidades
    $suma_cantidades = array_sum($stock);

    // Verificar si la suma total de las cantidades es mayor a 5
    if ($suma_cantidades > 100000) {
        echo '<script>alert("No se puede hacer el préstamo. Solo se permiten hasta 1000 herramientas en total.");</script>';
        echo '<script>window.location= "lista_herramientas.php"</script>';
        exit(); // Terminar la ejecución si la suma es mayor a 5
    }

    // Calcular la nueva fecha sumando los días seleccionados a la fecha actual
    $fecha_actual = date("Y-m-d"); // Obtener la fecha actual en formato YYYY-MM-DD
    $nueva_fecha = date("Y-m-d", strtotime($fecha_actual . " +$dias days")); // Sumar los días seleccionados

    // Verifica si la clave 'documento' está definida en la sesión antes de usarla
    if (isset($_SESSION['documento'])) {
        $documento = $_SESSION['documento'];

        // Realizar la inserción en la tabla principal
        $stmt = $conectar->prepare("INSERT INTO prestamos (documento, fecha_prestamo, fecha_devolucion, estado_prestamo) VALUES (:documento, :fecha_actual, :nueva_fecha, 'prestado')");
        $stmt->bindParam(':documento', $documento);
        $stmt->bindParam(':fecha_actual', $fecha_actual);
        $stmt->bindParam(':nueva_fecha', $nueva_fecha);
        $stmt->execute();

        // Obtener el ID de la última inserción
        $id_prestamo = $conectar->lastInsertId();

        // Procesar cada herramienta seleccionada
        foreach ($ids_seleccionados as $index => $id_herramienta) {
            // Obtener la cantidad correspondiente de stock para esta herramienta
            $cantidad = $stock[$index];

            // Realizar la inserción en la tabla de detalles para cada herramienta seleccionada
            $stmt1 = $conectar->prepare("INSERT INTO detalle_pres (id_prestamo, id_herramienta, cantidad_prestada) VALUES (:id_prestamo, :id_herramienta, :cantidad)");
            $stmt1->bindParam(':cantidad', $cantidad);
            $stmt1->bindParam(':id_prestamo', $id_prestamo);
            $stmt1->bindParam(':id_herramienta', $id_herramienta);
            $stmt1->execute();

            // Actualizar el stock de la herramienta
            $updateQuery = $conectar->prepare("UPDATE herrramienta SET stock = stock - :cantidad WHERE id_herramienta = :id_herramienta");
            $updateQuery->bindParam(':cantidad', $cantidad);
            $updateQuery->bindParam(':id_herramienta', $id_herramienta);
            $updateQuery->execute();

            // Verificar el stock actualizado de la herramienta
            $query_stock = $conectar->prepare("SELECT stock FROM herrramienta WHERE id_herramienta = :id_herramienta");
            $query_stock->bindParam(':id_herramienta', $id_herramienta);
            $query_stock->execute();
            $stock_actualizado = $query_stock->fetchColumn();

            // Si el stock es igual a 0, actualizar el estado de la herramienta a "no disponible"
            if ($stock_actualizado == 0) {
                $update_estado = $conectar->prepare("UPDATE herrramienta SET estado = 'no disponible' WHERE id_herramienta = :id_herramienta");
                $update_estado->bindParam(':id_herramienta', $id_herramienta);
                $update_estado->execute();
            }
        }


        // Mensaje de éxito
        echo "<script>alert('Se han insertado todas las herramientas en un solo préstamo correctamente con el ID de préstamo: $id_prestamo y la nueva fecha: $nueva_fecha');</script>";
        echo '<script> window.location= "../prestamos_usuario/lista_prestamos.php"</script>';
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
    <link rel="stylesheet" href="../../../../css/style.css">
    <!-- Responsive-->
    <link rel="stylesheet" href="../../../../css/responsive.css">
    <!-- styles usuario -->
    <link rel="stylesheet" href="../../../../css/styles_usuario.css">
    <!-- fevicon -->
    <link rel="icon" href="../../../../images/fevicon.png" type="image/gif" />
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="../../../../css/jquery.mCustomScrollbar.min.css">
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
                                    <a href="../prestamos.php"><img src="../../../../images/Sena_Colombia_logo.svg.png" alt="#" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-3">
        <h2>Herramientas seleccionadas</h2>
        <div class="table-responsive">
            <form method="post" id="formHerramientas">
                <table class="table table-sm table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr style="text-transform: uppercase;">
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>cantidad a prestar</th>
                            <th>Código</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asigna as $usua) { ?>
                            <tr>
                                <td><?= $usua["nombre_he"] ?></td>
                                <td><?= $usua["categoria"] ?></td>
                                <td><?= $usua["estado"] ?></td>
<td>
    <input type="number" name="cantidad[]" min="0" max="<?= $usua['stock'] ?>" value="0" class="form-control mt-2" placeholder="Cantidad a prestar" oninvalid="this.setCustomValidity('La cantidad disponible es de <?= $usua['stock'] ?>')" oninput="this.setCustomValidity('')">
</td>



                                </td>
                                <td><img src="../../../../images/<?= $usua["codigo_barras"] ?>.png" style="max-width: 100px;"></td>
                                <td><img src="../../../../images/<?= $usua["img_herramienta"] ?>" style="max-width: 100px;"></td>
                                <td>
                                    <!-- Botón de eliminar -->
                                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarId(<?= $usua['id_herramienta'] ?>)">Eliminar</button>
                                    <!-- Campo oculto para almacenar IDs seleccionados -->
                                    <input type="hidden" name="ids_seleccionados[]" value="<?= $usua['id_herramienta'] ?>">
                                    <!-- Campo para seleccionar la cantidad a prestar -->
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <label for="dias">Cuántos días quieres prestar:</label>
                <select name="dias" id="dias" class="form-control mb-2" style="font-size: 15px;">
                    <?php for ($i = 1; $i <= 7; $i++) { ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?> día<?php echo ($i > 1) ? 's' : ''; ?></option>
                    <?php } ?>
                </select>
                <button type="submit" class="btn btn-success" style="padding: 5px 20px;">Prestar</button>
                <a href="lista_herramientas.php" class="btn btn-danger" style="margin-top: 5px; margin-bottom: 5px;">Regresar</a>
            </form>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../paginacion_buscados.js"> </script>
    <script>
        function eliminarId(id) {
            // Obtener todos los elementos ocultos que contienen los IDs seleccionados
            var inputIds = document.getElementsByName('ids_seleccionados[]');

            // Verificar cuántos elementos quedan después de eliminar la fila
            if (inputIds.length === 1) {
                alert("Presatamos eliminado");
                window.location.href = "lista_herramientas.php";
            }

            // Obtener la fila correspondiente a la herramienta que se desea eliminar
            var fila = document.querySelector('input[value="' + id + '"]').parentNode.parentNode;

            // Eliminar la fila de la tabla
            fila.parentNode.removeChild(fila);
        }
    </script>

    </div>

    <!-- footer -->
    <footer>
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class=" col-md-3 col-sm-6">
                        <h3>variedad</h3>
                        <p class="variat pad_roght2">Ofrecemos una amplia variedad de herramientas
                            de alta calidad para satisfacer todas tus necesidades de
                            construcción.Tenemos todo lo que necesitas para completar
                            tus proyectos con éxito.
                        </p>
                    </div>
                    <div class=" col-md-3 col-sm-6">
                        <h3>dejanos ayudarte </h3>
                        <p class="variat pad_roght2">Nuestro objetivo es facilitarte el acceso a las herramientas
                            que necesitas para tus proyectos. Con nuestro proceso de préstamo simple y transparente,
                            puedes obtener las herramientas adecuadas sin complicaciones ni demoras.
                        </p>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <h3>NUESTRO DISEÑO</h3>
                        <p class="variat">En nuestra empresa, nos esforzamos por ofrecer un diseño intuitivo
                            y fácil de usar en todas nuestras plataformas. Nuestra interfaz está diseñada
                            pensando en la comodidad y la accesibilidad del usuario.
                        </p>
                    </div>
                    <div class="col-md-6 offset-md-6">
                        <form id="hkh" class="bottom_form">
                            <input class="enter" placeholder="" type="text" name="Enter your email">
                            <button class="sub_btn">Prestamos de herramientas</button>
                        </form>
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