<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();


if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}
if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $validar = $conectar->prepare("SELECT * FROM formacion WHERE id_formacion = ?");
    $validar->execute([$id]);
    $herramientas = $validar->fetch();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $ficha = $_POST['ficha'];
        $nombre = $_POST['nombre'];
        $jornada = $_POST['jornada'];

        // Obtener la información actual de la formación
        $validar = $conectar->prepare("SELECT id_formacion, formacion, jornada FROM formacion WHERE id_formacion = ?");
        $validar->execute([$id]);
        $herramienta_actual = $validar->fetch();

        $updateQuery = $conectar->prepare("UPDATE formacion SET id_formacion = ?, formacion = ?, jornada = ? WHERE id_formacion = ?");
        $updateQuery->execute([$ficha, $nombre, $jornada, $id]);
        echo '<script>alert("Actualización Exitosa");</script>';


        // Redirigir después de la actualización
        echo '<script>window.location= "lista_formaciones.php"</script>';
    }


    // Retrieve existing data for the selected record
    else {
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
    </header> <!-- ... (your existing body content) ... -->

    <section class="section">
        <div class="container my-5">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <h2>Actualizar Herramienta</h2>
                    <form method="POST">


                        <div class="form-group">
                            <label for="nombre">Ficha:</label>
                            <input type="number" class="form-control" id="ficha" name="ficha" value="<?php echo $herramientas['id_formacion']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre de la herramienta:</label>
                            <input type="text" class="form-control" value="<?php echo $herramientas['formacion']; ?>" id="nombre" name="nombre" pattern="[a-zA-Z0-9\s]+" title="El nombre no debe contener caracteres especiales" required>
                            <small>El nombre no debe contener caracteres especiales.</small>
                        </div>

                        <div class="form-group">
                            <label for="jornada">Jornada:</label>
                            <select class="form-control" id="jornada" name="jornada" required>
                                <!-- Comparar el valor actual con los valores de las opciones -->
                                <option value="mañana" <?php if ($herramientas['jornada'] === 'mañana') echo 'selected'; ?>>Mañana</option>
                                <option value="tarde" <?php if ($herramientas['jornada'] === 'tarde') echo 'selected'; ?>>Tarde</option>
                                <option value="noche" <?php if ($herramientas['jornada'] === 'noche') echo 'selected'; ?>>Noche</option>
                            </select>
                        </div>


                </div>


                <button type="submit" class="btn btn-success" style="margin-top:1rem; margin-left:1.6rem;">Actualizar</button>
                </form>
            </div>
            <a href="lista_formaciones.php" class="btn btn-danger">Volver</a>
        </div>
        </div>
    </section>
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

<!-- ... (your existing script imports) ... -->

<!-- ... (your existing script content) ... -->
</body>

</html>