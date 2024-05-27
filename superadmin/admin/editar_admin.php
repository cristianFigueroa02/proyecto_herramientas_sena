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


if (isset($_GET['id'])) {
    // Recupera el ID de la URL
    $id = $_GET['id'];

    $validar = $conectar->prepare("SELECT * FROM usuario
    INNER JOIN empresa ON usuario.nit=empresa.nit
    WHERE documento = ?");
    $validar->execute([$id]);
    $nit = $validar->fetch();


    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $email = $_POST['gmail'];

        // Prepare and execute the update query
        $updateQuery = $conectar->prepare("UPDATE usuario SET nombre = ?,email = ? WHERE documento = ?");
        $updateQuery->execute([$nombre, $email, $id]);
        // Redirect to the page displaying the updated data or any other desired location
        header("Location: lista_admin.php");
        exit();
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
    </header> <!-- ... (your existing body content) ... -->

    <section class="section">
        <div class="container my-5">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <h2>Editar admin</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="nombre">NIT de la empresa:</label>
                            <input type="text" class="form-control" value="<?php echo $nit['nit']; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Dirección:</label>
                            <input type="text" class="form-control" value="<?php echo $nit['nombre_empre']; ?>" id="direccion" name="direccion" pattern="[a-zA-Z0-9\s#.']+" disabled>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Numero Telefonico:</label>
                            <input type="text" class="form-control" value="<?php echo $nit['telefono']; ?>" id="telefono" name="telefono" disabled>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre del administrador:</label>
                            <input type="text" class="form-control" value="<?php echo $nit['nombre']; ?>" id="nombre" name="nombre" pattern="[a-zA-Z0-9\s]+" title="El nombre del admin solo puede contener letras, números y espacios, y debe tener al menos 10 caracteres" minlength="10" required oninput="validateAdminName(this)">
                        </div>

                        <script>
                            function validateAdminName(input) {
                                var pattern = /^[a-zA-Z0-9\s]+$/;
                                var minLength = 10;
                                if (!pattern.test(input.value) || input.value.length < minLength) {
                                    input.setCustomValidity("El nombre del admin solo puede contener letras, números y espacios, y debe tener al menos 10 caracteres");
                                } else {
                                    input.setCustomValidity("");
                                }
                            }
                        </script>


                        <div class="form-group">
                            <label for="gmail">E-mail:</label>
                            <input type="email" class="form-control" value="<?php echo $nit['gmail']; ?>" id="gmail" name="gmail" required oninput="validateEmail(this)">
                        </div>

                        <script>
                            function validateEmail(input) {
                                var pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|co|net|org|edu(\.[a-zA-Z]{2})?)$/;
                                if (!pattern.test(input.value)) {
                                    input.setCustomValidity("El correo electrónico debe ser válido y terminar sin caracteres adicionales");
                                } else {
                                    input.setCustomValidity("");
                                }
                            }
                        </script>

                        <button type="submit" class="btn btn-success" style="margin-top:1rem; margin-left:1.6em;">Actualizar</button>
                        <a href="lista_admin.php" class="btn btn-danger" style="margin-top:1rem; margin-left:1.6em;">Cancelar</a>
                    </form>
                </div>
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