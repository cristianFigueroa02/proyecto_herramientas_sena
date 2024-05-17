<?php
// Se incluye el archivo que contiene la clase de la base de datos
require_once("../../../bd/database.php");

// Se crea una nueva instancia de la clase Database para establecer la conexión
$db = new Database();
$conectar = $db->conectar(); // Se establece la conexión con la base de datos
session_start(); // Se inicia la sesión

// Se verifica si el usuario no está logueado
if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Se redirige a la página de inicio de sesión si no está logueado
    exit(); // Se detiene la ejecución del script
}

// Se verifica si la clave 'documento' está definida en la sesión antes de usarla
if (isset($_SESSION['documento'])) {
    // Si 'documento' está definido en la sesión, se asigna a una variable
    $documento = $_SESSION['documento'];

    // Se verifica si 'id' está definido en la URL
    if (isset($_GET['id'])) {
        // Si 'id' está definido en la URL, se asigna a una variable
        $id = $_GET['id'];

        // Se ejecutan consultas para obtener información relacionada con el usuario y los préstamos
        $usuarioQuery = $conectar->prepare("SELECT * FROM usuario WHERE documento = '$documento'");
        $usuarioQuery->execute();
        $usuario = $usuarioQuery->fetch(); // Se obtienen los datos del usuario

        // Se ejecuta una consulta para obtener los detalles del reporte y las herramientas asociadas
        $usua = $conectar->prepare("SELECT deta_reportes.*, herrramienta.*
                            FROM deta_reportes
                            INNER JOIN reportes ON deta_reportes.id_reporte = reportes.id_reporte
                            INNER JOIN herrramienta ON deta_reportes.id_herramienta = herrramienta.id_herramienta
                            INNER JOIN prestamos ON reportes.id_prestamo = prestamos.id_prestamo
                            WHERE reportes.id_reporte = :reportes");
        $usua->bindParam(':reportes', $id);
        $usua->execute();
        $asigna = $usua->fetchAll(PDO::FETCH_ASSOC); // Se obtienen los detalles del reporte y las herramientas asociadas
    } else {
        // Manejo de error si 'id' no está definido en la URL
        echo "Error: El ID no está definido en la URL.";
    }
} else {
    // Manejo de error si 'documento' no está definido en la sesión
    echo "Error: El documento no está definido en la sesión.";
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
                                    <a href="../prestamos.php"><img src="../../../images/Sena_Colombia_logo.svg.png" alt="#" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-3">
        <h2>Herramientas</h2>
        <div class="table-responsive">
            <form method="post" id="formHerramientas" onsubmit="return validarSeleccion()" action="confirmacion_prestamo.php">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr style="text-transform: uppercase;">
                            <th>numero de reporte</th>
                            <th>Herramienta</th>
                            <th>codigo de barras</th>
                            <th>cantidad reportada</th>
                            <th>Descripcion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asigna as $usua) { ?>
                            <tr>
                                <td><?= $usua["id_reporte"] ?></td>
                                <td><?= $usua["nombre_he"] ?></td>
                                <td><img src="../../../images/<?= $usua["codigo_barras"] ?>.png" style="max-width: 150px;"></td>
                                <td><?= $usua["cantidad_reportada"] ?></td>
                                <td><?= $usua["descripcion"] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>


        <a href="lista_reportes.php" class="btn btn-danger" style="margin-bottom: 10px;">Regresar</a>
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