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


if ((isset($_POST["registro"])) && ($_POST["registro"] == "formu")) {
    $nit = $_POST['nit'];
    $nombre_empre = $_POST['nombre_empre'];
    $direccion = $_POST['direccion'];
    $gmail = $_POST['gmail'];
    $telefono = $_POST['telefono'];

    // Verificar si ya existe un registro con el mismo NIT
    $validar_nit = $conectar->prepare("SELECT * FROM empresa WHERE nit = ?");
    $validar_nit->execute([$nit]);
    $existe_nit = $validar_nit->fetch();

    if ($existe_nit) {
        echo '<script> alert ("Ya existe una empresa con ese NIT.");</script>';
        echo '<script> window.location="crear_empresa.php"</script>';
    } else {
        // Insertar los datos en la base de datos
        $insertsql = $conectar->prepare("INSERT INTO empresa (nit, nombre_empre, direccion, gmail, telefono) VALUES (?, ?, ?, ?, ?)");
        $insertsql->execute([$nit, $nombre_empre, $direccion, $gmail, $telefono]);
        echo '<script>alert ("Registro exitoso.");</script>';
        echo '<script> window.location= "lista_empresa.php"</script>';
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
                                    <a href="#"><img src="../../images/Sena_Colombia_logo.svg.png" alt="#" /></a>
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
            <h2>Registro de Empresa</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="nit">NIT:</label>
                    <input type="text" id="nit" name="nit" class="form-control" pattern="[0-9]{8,12}" title="El NIT debe contener entre 8 y 12 dígitos numéricos" required>
                </div>
                <div class="form-group">
                    <label for="nombre_empre">Nombre de la Empresa:</label>
                    <input type="text" id="nombre_empre" name="nombre_empre" class="form-control" pattern="[a-zA-Z0-9\s]+" title="El nombre de la empresa solo puede contener letras, números y espacios" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" class="form-control" pattern="[a-zA-Z0-9\s.,#'/-:]+" title="La dirección puede contener letras, números, espacios y caracteres especiales" required>
                </div>

                <div class="form-group">
                    <label for="gmail">Correo Electrónico:</label>
                    <input type="email" id="gmail" name="gmail" class="form-control" required oninput="validateEmail(this)">
                </div>

                <script>
                    function validateEmail(input) {
                        var pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|co|net|org|edu(\.[a-zA-Z]{2})?)$/;
                        if (!pattern.test(input.value)) {
                            input.setCustomValidity("El correo electrónico debe ser válido y sin caracteres adicionales al final del correo");
                        } else {
                            input.setCustomValidity("");
                        }
                    }
                </script>


                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" class="form-control" pattern="\d{10}" title="El teléfono debe contener 10 dígitos numéricos" maxlength="10" required>
                </div>

                <button type="submit" class="btn btn-success">Registrar</button>
                <input type="hidden" name="registro" value="formu">
                <a href="lista_empresa.php" class="btn btn-danger">Cancelar</a>
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