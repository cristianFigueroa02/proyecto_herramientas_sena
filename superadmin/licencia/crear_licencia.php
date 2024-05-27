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

$empresaQuery = $conectar->prepare("SELECT nit, nombre_empre FROM empresa");
$empresaQuery->execute();
$empresas = $empresaQuery->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "formreg") {
    $nit = $_POST["nit"];
    $licencia = uniqid();
    $fecha_inicio = date('Y-m-d H:i:s');
    $fecha_fin = date('Y-m-d H:i:s', strtotime('+1 year'));

    if ($nit == "") {
        echo '<script>alert("EXISTEN CAMPOS VACÍOS");</script>';
        echo '<script>window location="crear_licencia.php"</script>';
    } else {
        // Verificar si la empresa asociada al NIT existe en la base de datos
        $empresaExists = false;
        $empresaQuery = $conectar->prepare("SELECT nit FROM empresa WHERE nit = ?");
        $empresaQuery->execute([$nit]);
        $empresa = $empresaQuery->fetch(PDO::FETCH_ASSOC);

        if ($empresa) {
            // Verificar si la empresa ya tiene una licencia activa
            $licenciaQuery = $conectar->prepare("SELECT COUNT(*) AS count FROM licencia WHERE nit = ? AND estado = 'activo'");
            $licenciaQuery->execute([$nit]);
            $licenciaCount = $licenciaQuery->fetch(PDO::FETCH_ASSOC)['count'];

            if ($licenciaCount > 0) {
                echo '<script>alert("La empresa ya tiene una licencia activa");</script>';
                echo '<script>window.location="crear_licencia.php"</script>';
            } else {
                $insertsql = $conectar->prepare("INSERT INTO licencia (licencia, estado, fecha_inicio, fecha_fin, nit) VALUES (?, ?, ?, ?, ?)");
                $insertsql->execute([$licencia, 'activo', $fecha_inicio, $fecha_fin, $nit]);

                echo '<script>alert("Licencia activa con éxito");</script>';
                echo '<script>window.location="lista_licencia.php"</script>';
            }
        } else {
            echo '<script>alert("La empresa asociada al NIT ingresado no existe en la base de datos");</script>';
            echo '<script>window.location="crear_licencia.php"</script>';
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
    <main>
        <div class="container mt-5">
            <form method="post" action="" autocomplete="off">
                <div class="form-group">
                    <label for="id_tip_doc">Empresa:</label>
                    <select class="form-control" id="nit" name="nit" required>
                        <option value="" disabled selected>Selecciona la empresa</option> <!-- Placeholder -->
                        <?php foreach ($empresas as $tipo) : ?>
                            <option value="<?php echo $tipo['nit']; ?>"><?php echo $tipo['nombre_empre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button onclick="abrirVentana()">Crear empresa</button>
                    <script>
                        function abrirVentana() {
                            // URL absoluta que quieres abrir en la ventana emergente
                            var url = "../empresas/crear_empresa.php";

                            // Ancho y alto de la ventana emergente (la mitad de la pantalla)
                            var ancho = window.innerWidth / 2;
                            var alto = window.innerHeight / 2;

                            // Calcular las coordenadas left y top para centrar la ventana emergente
                            var left = (window.innerWidth - ancho) / 2 + window.screenX;
                            var top = (window.innerHeight - alto) / 2 + window.screenY;

                            // Intentar abrir la ventana emergente
                            var ventana = window.open(url, "_blank", "width=" + ancho + ", height=" + alto + ", left=" + left + ", top=" + top);

                            // Verificar si se pudo abrir la ventana emergente
                            if (ventana) {
                                // La ventana emergente se abrió correctamente
                            } else {
                                // Manejar el caso en que no se pudo abrir la ventana emergente
                                alert("No se pudo abrir la ventana emergente. Por favor, asegúrate de que los bloqueadores de ventanas emergentes estén desactivados.");
                            }
                        }
                    </script>

                    <br>
                    <input type="hidden" name="MM_insert" value="formreg">
                    <button type="submit" class="btn btn-success" style="margin-top: 10px;">Registrarme</button>
                    <a href="lista_licencia.php" class="btn btn-danger" style="margin-top: 10px;">Regresar</a>
            </form>
        </div>
    </main>
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