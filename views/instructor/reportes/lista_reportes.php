<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}

// Verifica si la clave 'documento' está definida en la sesión antes de usarla
if (isset($_SESSION['documento'])) {
    $documento = $_SESSION['documento'];


    $usuarioQuery = $conectar->prepare("SELECT * FROM usuario WHERE documento = '$documento'");
    $usuarioQuery->execute();
    $usuario = $usuarioQuery->fetch();
    $usua = $conectar->prepare("SELECT 
    reportes.id_reporte,
    MAX(reportes.id_prestamo) AS id_prestamo,
    MAX(reportes.fecha_reporte) AS fecha_reporte,
    MAX(reportes.estado_reporte) AS estado_reporte,
    MAX(prestamos.documento) AS prestamo_documento,
    MAX(prestamos.fecha_prestamo) AS fecha_prestamo,
    MAX(prestamos.fecha_devolucion) AS fecha_devolucion,
    MAX(prestamos.estado_prestamo) AS estado_prestamo,
    MAX(detalle_usuarios.id_detalle) AS id_detalle,
    MAX(detalle_usuarios.documento) AS detalle_documento,
    MAX(detalle_usuarios.ficha) AS ficha,
    MAX(formacion.id_formacion) AS id_formacion,
    MAX(formacion.formacion) AS formacion,
    MAX(formacion.jornada) AS jornada,
    MAX(usuario.documento) AS documento,
    MAX(usuario.contraseña) AS contraseña,
    MAX(usuario.nombre) AS nombre,
    MAX(usuario.id_tip_doc) AS id_tip_doc,
    MAX(usuario.email) AS email,
    MAX(usuario.id_rol) AS id_rol,
    MAX(usuario.estado) AS estado,
    MAX(usuario.nit) AS nit,
    MAX(usuario.tyc) AS tyc
FROM 
    reportes 
INNER JOIN 
    prestamos ON reportes.id_prestamo = prestamos.id_prestamo
INNER JOIN 
    detalle_usuarios ON prestamos.documento = detalle_usuarios.documento
INNER JOIN 
    formacion ON detalle_usuarios.ficha = formacion.id_formacion
INNER JOIN 
    usuario ON prestamos.documento = usuario.documento 
WHERE 
    reportes.estado_reporte = 'activo'
GROUP BY 
    reportes.id_reporte;
");
    $usua->execute();
    $asigna = $usua->fetchAll(PDO::FETCH_ASSOC);
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
                                    <a href="#"><img src="../../../images/Sena_Colombia_logo.svg.png" alt="#" /></a>
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
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#">Lista de reportes activos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="lista_reportes_inactivos.php">Lista de reportes inactivos</a>
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
        <h2 style="text-transform:uppercase;">Reportes activos</h2>
        <div class="table-responsive">
            <input type="text" id="searchInput" class="form-control mb-2 search-input" placeholder="Buscar reporte">
            <table id="toolTable" class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr style="text-transform: uppercase;">
                        <th>numero de reporte</th>
                        <th>documento</th>
                        <th>nombre</th>
                        <th>ficha</th>
                        <th>formacion</th>
                        <th>fecha reporte</th>
                        <th>estado del reporte</th>
                        <th>acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asigna as $usua) { ?>
                        <tr>
                            <td><?php print_r($usua["id_reporte"]); ?></td>
                            <td><?php print_r($usua["documento"]); ?></td>
                            <td><?php print_r($usua["nombre"]); ?></td>
                            <td><?php print_r($usua["ficha"]); ?></td>
                            <td><?php print_r($usua["formacion"]); ?></td>
                            <td><?php print_r($usua["fecha_reporte"]); ?></td>
                            <td><?php print_r($usua["estado_reporte"]); ?></td>
                            <td>
                                <a href="detalle_reporte_activo.php?id=<?= $usua["id_reporte"] ?>" class="btn btn-primary">detalle reporte</a>
                                                                <a href="inactivar.php?id=<?= $usua["id_reporte"] ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas inactivar este reporte?')">inactivar</a>

                            </td>
                        </tr>
                    <?php } ?>
                </tbody>



            </table>
            </form>
        </div>
        <script>
            function confirmAndOpenPopup(url, message) {
                if (confirm(message)) {
                    // Si el usuario hace clic en "Aceptar" en la alerta, abre una nueva ventana emergente con el enlace proporcionado.
                    window.open(url, '_blank');
                }
                // Devuelve false para evitar que el enlace se siga si el usuario hace clic en "Cancelar" en la alerta.
                return false;
            }
        </script>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="../../../paginacion_buscados.js"> </script>


        <a href="../index.php" class="btn btn-danger" style="margin-bottom: 10px;">Regresar</a>
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