<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();
$id = isset($_GET['id']) ? $_GET['id'] : null;


if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
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
    <style>
        .contenedor {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    </style>

    <body>
        <div class="container mt-3">
            <div class="contenedor sombra">
                <h2 style="text-transform:uppercase;">Estás en el apartado de devolución de herramientas</h2>
                <br>
                <p>Recuerda que debes tener un instructor al lado para poder hacer la devolución.</p>
                <br>
                <div>
                    <a href="lista.php" class="btn btn-primary mr-2" onclick="regresar()">Regresar</a>
                    <button class="btn btn-success mr-2" onclick="confirmarDevolucion()">Confirmar</button>
                    <button class="btn btn-danger" onclick="confirmarReporte()">Hacer Reporte</button>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script>
            function confirmarDevolucion() {
                // Se muestra un cuadro de diálogo para que el usuario ingrese un código de confirmación
                var codigoIngresado = prompt("Por favor ingresa el código para hacer la devolucion:");

                // Se verifica si el código ingresado es igual a un valor específico (en este caso, "2500591")
                if (codigoIngresado === "2500591") {
                    // Si el código es correcto, se obtiene el ID del reporte de la URL
                    var id = <?php echo json_encode($_GET['id']); ?>;
                    // Se redirige a la página de reporte, pasando el ID como parámetro en la URL
                    window.location.href = "devolver.php?id=" + id;
                } else {
                    // Si el código es incorrecto, se muestra un mensaje de alerta y no se realiza ninguna acción
                    alert("El código ingresado es incorrecto. No se puede hacer el reporte.");
                }
            }
        </script>
        <script>
            // Esta función se llama cuando se hace clic en un elemento para confirmar un reporte
            function confirmarReporte() {
                // Se muestra un cuadro de diálogo para que el usuario ingrese un código de confirmación
                var codigoIngresado = prompt("Por favor ingresa el código para hacer el reporte:");

                // Se verifica si el código ingresado es igual a un valor específico (en este caso, "2500591")
                if (codigoIngresado === "2500591") {
                    // Si el código es correcto, se obtiene el ID del reporte de la URL
                    var id = <?php echo json_encode($_GET['id']); ?>;
                    // Se redirige a la página de reporte, pasando el ID como parámetro en la URL
                    window.location.href = "reporte.php?id=" + id;
                } else {
                    // Si el código es incorrecto, se muestra un mensaje de alerta y no se realiza ninguna acción
                    alert("El código ingresado es incorrecto. No se puede hacer el reporte.");
                }
            }
        </script>


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
    </body>

</html>

</html>