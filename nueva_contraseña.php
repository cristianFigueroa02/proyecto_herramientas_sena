<?php
require_once("bd/database.php");
$db = new Database();
$conectar = $db->conectar();

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener la contraseña actual y la nueva contraseña del formulario
    $actual_contraseña = $_POST["actual_contraseña"];
    $nueva_contra = $_POST["nueva_contra"];

    // Obtener la contraseña almacenada en la base de datos basada en el documento proporcionado
    $documento = $_POST["documento"];
    $sql = "SELECT contraseña FROM usuario WHERE documento = :documento";
    $stmt = $conectar->prepare($sql);
    $stmt->bindParam(':documento', $documento);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Si se encontró un usuario con ese documento, obtener la contraseña encriptada almacenada en la base de datos
        $contraseña_encriptada_bd = $result["contraseña"];

        // Verificar si la contraseña actual proporcionada coincide con la contraseña encriptada almacenada en la base de datos
        if (password_verify($actual_contraseña, $contraseña_encriptada_bd)) {
            // La contraseña actual es correcta, aquí deberías encriptar la nueva contraseña y actualizarla en tu base de datos
            $nueva_contra_encriptada = password_hash($nueva_contra, PASSWORD_DEFAULT);

            // Actualizar la contraseña encriptada en la base de datos
            $sql_update = "UPDATE usuario SET contraseña = :nueva_contra WHERE documento = :documento";
            $stmt_update = $conectar->prepare($sql_update);
            $stmt_update->bindParam(':nueva_contra', $nueva_contra_encriptada);
            $stmt_update->bindParam(':documento', $documento);
            $stmt_update->execute();

            // Mostrar mensaje de éxito
            echo "<script>alert('Contraseña cambiada con exito'); window.location.href='login.php';</script>";
        } else {
            // La contraseña actual es incorrecta
            echo "<script>alert('La contraseña actual esta incorrecta'); window.location.href='recuperar_contraseña.php';</script>";
        }
    } else {
        // No se encontró ningún usuario con ese documento
        echo "<p>No se encontró ningún usuario con el documento proporcionado.</p>";
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
    <title>SENA</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- bootstrap css -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Responsive-->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- fevicon -->
    <link rel="icon" href="images/fevicon.png" type="image/gif" />
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
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
                                    <a href="index.html"><img src="images/Sena_Colombia_logo.svg.png" alt="#" /></a>
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
                                        <a class="nav-link" href="index.html">Principal</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="contact.php">Contactanos</a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="login.php">login</a>
                                    </li>
                                    <li class="nav-item ">
                                        <a class="nav-link" href="registro.php">Registro</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div style="margin-bottom:50px;" class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title text-center">TU nueva contraseña</h4>

                            <form method="post">
                                <div class="col-12">
                                    <label for="documento" class="form-label">Ingresa tu documento:</label>
                                    <input type="text" id="documento" name="documento" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label for="documento" class="form-label">Ingresa tu actual contraseña:</label>
                                    <input type="password" id="documento" name="actual_contraseña" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label for="documento" class="form-label">Ingresa tu nueva contraseña:</label>
                                    <input type="password" id="documento" name="nueva_contra" class="form-control" required>
                                </div>
                                <br>
                                <div class="col-12">
                                    <input class="btn btn-primary w-100" type="submit" value="Recuperar Contraseña">
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>
        </div>
    </main>

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
    <script src="js/jquery-3.0.0.min.js"></script>
    <!-- sidebar -->
    <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>