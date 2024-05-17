<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php"); // Redirigir a la página de inicio si no está logueado
    exit();
}
$tip_forQuery = $conectar->prepare("SELECT id_formacion,formacion FROM formacion");
$tip_forQuery->execute();
$tiposfor = $tip_forQuery->fetchAll(PDO::FETCH_ASSOC);

$tip_docQuery = $conectar->prepare("SELECT * FROM usuario INNER JOIN rol ON usuario.id_rol = rol.id_rol WHERE rol.id_rol = 4");
$tip_docQuery->execute();
$tiposdoc = $tip_docQuery->fetchAll(PDO::FETCH_ASSOC);



if (isset($_POST["MM_insert"]) && $_POST["MM_insert"] == "formreg") {
    // Obtener datos del formulario
    $documento = $_POST["documento"];
    $ficha = $_POST["ficha"];

    if ($documento == "" || $ficha == "") {
        echo '<script>alert("EXISTEN CAMPOS VACÍOS");</script>';
        echo '<script>window.location="asignacion_instructor.php"</script>';
    } else {
        // Verificar si el documento ya está asociado con la ficha en la tabla `detalle_usuarios`
        $checkQuery = $conectar->prepare("SELECT COUNT(*) FROM detalle_usuarios WHERE documento = ? AND ficha = ?");
        $checkQuery->execute([$documento, $ficha]);
        $count = $checkQuery->fetchColumn();

        if ($count > 0) {
            // Si la combinación de `documento` y `ficha` ya existe, muestra un mensaje de alerta
            echo '<script>alert("El documento ya está asociado con la ficha especificada.");</script>';
            echo '<script>window.location="asignacion_instructor.php"</script>';
        } else {
            // Si la combinación de `documento` y `ficha` no existe, procede con la inserción
            $insertdeta = $conectar->prepare("INSERT INTO detalle_usuarios(documento, ficha) VALUES (?, ?)");
            $insertdeta->execute([$documento, $ficha]);

            echo '<script>alert("Registro exitoso");</script>';
            echo '<script>window.location="lista_instructores.php"</script>';
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
    <link rel="stylesheet" href="../../../css/bootstrap.min.css">
    <!-- style css -->
    <link rel="stylesheet" href="../../../css/style.css">
    <!-- Responsive-->
    <link rel="stylesheet" href="../../../css/responsive.css">
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
        <main>
            <div class="container mt-5">
                <form method="post" action="" autocomplete="off">

                    <div class="form-group">
                        <label for="documento">Nombre instructor:</label>
                        <select class="form-control" id="documento" name="documento" required>
                            <option value="" disabled selected>Selecciona el instructor</option> <!-- Placeholder -->
                            <?php foreach ($tiposdoc as $tipo) : ?>
                                <option value="<?php echo $tipo['documento']; ?>"><?php echo $tipo['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="formulario__grupo" id="grupo__ficha">
                        <label for="fichaInput">Ficha:</label>
                        <input type="number" class="form-control" id="fichaInput" name="ficha" value="<?= $ficha_usuario ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="formacion">Formación:</label>
                        <select class="form-control" id="formacion" name="formacion" required>
                            <?php
                            // Integrar resultados de la consulta en el select
                            foreach ($tiposfor as $row) {
                                echo "<option value='{$row['id_formacion']}'" . ($id_formacion_usuario == $row['id_formacion'] ? 'selected' : '') . ">{$row['formacion']}</option>";
                            }
                            ?>
                        </select>
                        <button onclick="abrirVentanaCrear()">crear formacion</button>

                        <script>
                            function abrirVentanaCrear() {
                                // URL absoluta que quieres abrir en la ventana emergente
                                var url = "../formaciones/crear_formacion.php";

                                // Ancho y alto de la ventana emergente (ajustar según necesites)
                                var ancho = 600;
                                var alto = 400;

                                // Calcular las coordenadas left y top para centrar la ventana emergente
                                var left = (screen.width - ancho) / 2;
                                var top = (screen.height - alto) / 2;

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
                    </div>


                    <script>
                        var fichaInput = document.getElementById("fichaInput");
                        var formacionSelect = document.getElementById("formacion");

                        fichaInput.addEventListener("input", function() {
                            var inputNumber = parseInt(fichaInput.value.trim());

                            // Limpiar el campo de selección de formación
                            formacionSelect.innerHTML = '';

                            // Realizar una solicitud AJAX para obtener la formación asociada al número de ficha ingresado
                            var xhr = new XMLHttpRequest();
                            xhr.open("GET", "obtener_formacion.php?ficha=" + inputNumber, true);
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === 4 && xhr.status === 200) {
                                    // Parsear la respuesta JSON
                                    var response = JSON.parse(xhr.responseText);

                                    // Si se encuentra una formación, agregarla como opción al campo de selección de formación
                                    if (response) {
                                        var option = document.createElement("option");
                                        option.text = response.formacion;
                                        option.value = response.formacion;
                                        formacionSelect.add(option);
                                    } else {
                                        // Si no se encuentra una formación, mostrar un mensaje indicando que no se encontraron resultados
                                        var option = document.createElement("option");
                                        option.text = "Formación no encontrada";
                                        option.value = "";
                                        formacionSelect.add(option);
                                    }
                                }
                            };
                            xhr.send();
                        });
                    </script>
                    <input type="hidden" name="MM_insert" value="formreg">
                    <button type="submit" class="btn btn-success">Registrarme</button>
                </form>
                <a href="lista_instructores.php" class="btn btn-danger" style="margin:10px auto">Cancelar</a>
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