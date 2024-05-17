<?php
require_once("../../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}

// Verifica si la clave 'documento' está definida en la sesión antes de usarla
if (isset($_SESSION['documento'])) {
    $documento = $_SESSION['documento'];

    $usuarioQuery = $conectar->prepare("SELECT * FROM usuario WHERE documento = '$documento'");
    $usuarioQuery->execute();
    $usuario = $usuarioQuery->fetch();

    $usua = $conectar->prepare("SELECT * FROM prestamos INNER JOIN usuario ON prestamos.documento = usuario.documento WHERE prestamos.estado_prestamo = 'prestado' AND usuario.documento='$documento'");
    $usua->execute();
    $asigna = $usua->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Manejo de error si 'documento' no está definido en la sesión
    echo "Error: El documento no está definido en la sesión.";
    exit();
}

$fecha_actual = date("Y-m-d");

// Recorre cada préstamo del usuario
foreach ($asigna as $usua) {
    // Obtener la fecha de devolución del préstamo
    $fecha_devolucion = $usua["fecha_devolucion"];
    $id_prestamo = $usua["id_prestamo"];

    // Comparar la fecha de devolución con la fecha actual
    if ($fecha_devolucion <= $fecha_actual) {
        // Actualizar el estado del préstamo a "reportado"
        $stmt_update_prestamo = $conectar->prepare("UPDATE prestamos SET estado_prestamo = 'reportado' WHERE id_prestamo = :id_prestamo");
        $stmt_update_prestamo->bindParam(':id_prestamo', $id_prestamo);
        $stmt_update_prestamo->execute();

        // Insertar un registro en la tabla "reportes"
        // Insertar un registro en la tabla "reportes"
        $stmt_insert_reporte = $conectar->prepare("INSERT INTO reportes (id_prestamo, fecha_reporte, estado_reporte) VALUES (:id_prestamo, :fecha_actual, 'activo')");
        $stmt_insert_reporte->bindParam(':id_prestamo', $id_prestamo);
        $stmt_insert_reporte->bindParam(':fecha_actual', $fecha_actual);
        $stmt_insert_reporte->execute();

        // Obtener el ID del último reporte insertado
        $id_reporte = $conectar->lastInsertId();

        // Obtener los datos relacionados de la tabla "detalle_pres"
        $stmt_detalle_pres = $conectar->prepare("SELECT id_herramienta, cantidad_prestada FROM detalle_pres WHERE id_prestamo = :id_prestamo");
        $stmt_detalle_pres->bindParam(':id_prestamo', $id_prestamo);
        $stmt_detalle_pres->execute();
        $detalles_pres = $stmt_detalle_pres->fetchAll(PDO::FETCH_ASSOC);

        // Insertar datos en la tabla "deta_reportes"
        $stmt_insert_deta_reportes = $conectar->prepare("INSERT INTO deta_reportes (id_reporte, id_herramienta, cantidad_reportada,descripcion) VALUES (:id_reporte, :id_herramienta, :cantidad_reportada,'paso la fecha de entrega')");

        // Recorrer los resultados de "detalle_pres" e insertar en "deta_reportes"
        foreach ($detalles_pres as $detalle_pres) {
            $id_herramienta = $detalle_pres['id_herramienta'];
            $cantidad_reportada = $detalle_pres['cantidad_prestada'];

            // Ejecutar la inserción en "deta_reportes"
            $stmt_insert_deta_reportes->bindParam(':id_reporte', $id_reporte);
            $stmt_insert_deta_reportes->bindParam(':id_herramienta', $id_herramienta);
            $stmt_insert_deta_reportes->bindParam(':cantidad_reportada', $cantidad_reportada);
            $stmt_insert_deta_reportes->execute();
        }
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
    <header>
        <!-- header inner -->
        <div class="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-sm-3 col logo_section">
                        <div class="full">
                            <div class="center-desk">
                                <div class="logo">
                                    <a href="#"><img src="../../../../images/Sena_Colombia_logo.svg.png" alt="#" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 offset-md-1">
                        <nav class="navigation navbar navbar-expand-md navbar-dark ">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarsExample04">
                                <ul class="navbar-nav mr-auto">

                                    <li class="nav-item active">
                                        <a class="nav-link" href="#">tus prestamos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="lista_prestamo_reportes.php">Tus prestamos en reporte</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-3">
        <h2 style="text-transform:uppercase;">tus prestamos</h2>
        <div class="table-responsive">
            <form method="post" id="formHerramientas" onsubmit="return validarSeleccion()" action="confirmacion_prestamo.php">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr style="text-transform: uppercase;">
                            <th>Documento</th>
                            <th>fecha inicio prestamo</th>
                            <th>fecha fin prestamo</th>
                            <th>estado del prestamo</th>
                            <th>acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asigna as $usua) { ?>
                            <tr>
                                <td><?= $usua["documento"] ?></td>
                                <td><?= $usua["fecha_prestamo"] ?></td>
                                <td><?= $usua["fecha_devolucion"] ?></td>
                                <td><?= $usua["estado_prestamo"] ?></td>
                                <td><a href="detalle_prestamo.php?id=<?= $usua["id_prestamo"] ?>" class="btn btn-primary ">detalles prestamo</a></td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>


        <a href="../prestamos.php   " class="btn btn-danger" style="margin-bottom: 10px;">Regresar</a>
        <a href="exportar_pdf.php" class="btn btn-primary" style="margin-bottom: 10px;">Generar Reporte</a>
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