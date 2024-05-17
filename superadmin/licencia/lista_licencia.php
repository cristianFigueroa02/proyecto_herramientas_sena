<?php
require_once("../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

// Validar si la variable de sesión no está establecida
if (!isset($_SESSION['codigo_valido']) || !$_SESSION['codigo_valido']) {
    // Redirigir al usuario a una URL específica si no se cumple la validación
    header("Location: ../index.php");
    exit; // Detener la ejecución del script
}

$usua = $conectar->prepare("SELECT * FROM licencia,empresa WHERE licencia.nit=empresa.nit ");
$usua->execute();
$asigna = $usua->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../../css/style.css">
    <!-- Responsive-->
    <link rel="stylesheet" href="../../css/responsive.css">
    <!-- styles usuario -->
    <link rel="stylesheet" href="../../css/styles_usuario.css">
    <!-- fevicon -->
    <link rel="icon" href="../../images/fevicon.png" type="image/gif" />
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="../../css/jquery.mCustomScrollbar.min.css">
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
                                    <a href="#"><img src="../../images/Sena_Colombia_logo.svg.png" alt="#" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
<div class="container mt-3">
    <a href="crear_licencia.php" class="btn btn-success mb-2">Crear una licencia</a>

    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
            <tr style="text-transform: uppercase;">
                <th>licencia</th>
                <th>fecha inicio</th>
                <th>fecha fin</th>
                <th>empresa</th>
                <th>estado</th>
                <th>Acciones</th> <!-- Nueva columna para acciones -->
            </tr>
        </thead>
        <tbody>
            <?php 
            // Array asociativo para almacenar las entradas por nombre de empresa
            $entradas_por_empresa = array();

            // Iterar sobre los resultados de la consulta y almacenar las entradas por nombre de empresa
            foreach ($asigna as $usua) {
                $nombre_empresa = $usua["nombre_empre"];

                // Si la entrada es activa o no hay una entrada activa para esta empresa en el array, almacenarla
                if ($usua["estado"] === "activo" || !isset($entradas_por_empresa[$nombre_empresa])) {
                    $entradas_por_empresa[$nombre_empresa] = $usua;
                }
            }

            // Mostrar las entradas por empresa en la tabla
            foreach ($entradas_por_empresa as $usua) {
                ?>
                <tr>
                    <td><?= $usua["licencia"] ?></td>
                    <td><?= $usua["fecha_inicio"] ?></td>
                    <td><?= $usua["fecha_fin"] ?></td>
                    <td><?= $usua["nombre_empre"] ?></td>
                    <td><?= $usua["estado"] ?></td>
                    <!-- Mostrar formulario solo si el estado es "inactivo" -->
<td>
    <?php if ($usua["estado"] === "inactivo") { ?>
        <form action="renovar_licencia.php" method="GET" onsubmit="return confirm('¿Está seguro de renovar esta licencia?');">
            <input type="hidden" name="nit_empresa" value="<?= $usua["nit"] ?>">
            <button type="submit" class="btn btn-primary">Renovar</button>
        </form>
    <?php } ?>
</td>

                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="../index.php" class="btn btn-danger">Regresar</a>
</div>




    </div>
    <!-- footer -->
    <footer<footer>
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