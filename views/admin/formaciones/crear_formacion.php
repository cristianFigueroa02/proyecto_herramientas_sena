<?php
require_once("../../../bd/database.php");

session_start();

// Reutilizar la conexión existente si está disponible
if (!isset($conectar)) {
    $db = new Database();
    $conectar = $db->conectar();
}

// Redirigir a la página de inicio si no está logueado
if (!isset($_SESSION['documento'])) {
    header("Location: ./../../login.php");
    exit();
}

if (isset($_POST["registro"]) && $_POST["registro"] == "formu") {
    $ficha = $_POST["ficha"];
    $nombre = $_POST['nombre'];
    $jornada = $_POST['jornada'];

    if ($nombre == "" || $ficha == "" || $jornada == "") {
        echo '<script>alert("EXISTEN DATOS VACIOS");</script>';
        echo '<script>window.location="crear_formacion.php";</script>';
    } else {
        // Preparar y ejecutar la consulta SQL
        $insertsql = $conectar->prepare("INSERT INTO formacion(id_formacion,formacion,jornada) VALUES (?, ?, ?)");
        $insertsql->execute([$ficha, $nombre, $jornada]);

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
    <main class="contenedor sombra">
        <div class="container mt-5">
            <h2>Crear Formacion</h2>
            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="nombre">Ficha:</label>
                    <input type="number" class="form-control" id="ficha" name="ficha" required>
                </div>

<div class="form-group">
    <label for="nombre">Nombre de la formación:</label>
    <input type="text" class="form-control" id="nombre" name="nombre" pattern="[a-zA-Z0-9\s]+" title="El nombre no debe contener caracteres especiales" required>
    <small>El nombre no debe contener caracteres especiales.</small>
</div>

                <div class="form-group">
                    <label for="jornada">Jornada:</label>
                    <select class="form-control" id="jornada" name="jornada" required>
                        <option value="" disabled selected>Selecciona una jornada</option> <!-- Placeholder -->
                        <option value="mañana">Mañana</option>
                        <option value="tarde">Tarde</option>
                        <option value="noche">Noche</option>
                    </select>
                </div>


                <input type="submit" class="btn btn-success" value="Registrar">
                <input type="hidden" name="registro" value="formu">
                <a href="lista_formaciones.php" class="btn btn-danger">Volver</a>
            </form>
        </div>
    </main>
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