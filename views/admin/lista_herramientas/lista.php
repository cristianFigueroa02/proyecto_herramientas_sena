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

        $usua = $conectar->prepare("SELECT * FROM herrramienta,categoria WHERE herrramienta.id_cate = categoria.id_cate");
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
    <style>
        .search-input {
            border: 1px solid #6c757d;
            /* Color de borde más oscuro */
            border-radius: 0.25rem;
            /* Bordes redondeados */
            color: #495057;
            /* Color de texto */
        }
    </style>

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
                                    <h2 class="titulo-principal" style="color:#000;">Bienvenido admin <?= $usuario['nombre']; ?> </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container mt-3">
            <a href="crear_herramientas.php" class="btn btn-success mb-2">Crear una herramienta</a>

            <input type="text" id="searchInput" class="form-control mb-2 search-input" placeholder="Buscar por nombre de herramienta">

            <table id="toolTable" class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr style="text-transform: uppercase;">
                        <th>Nombre</th>
                        <th>Tipo de herramienta</th>
                        <th>Estado</th>
                        <th>cantidad</th>
                        <th>stock</th>
                        <th>Código de barras</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asigna as $usua) { ?>
                        <tr>
                            <td><?= $usua["nombre_he"] ?></td>
                            <td><?= $usua["categoria"] ?></td>
                            <td><?= $usua["estado"] ?></td>
                            <td><?= $usua["cantidad"] ?></td>
                            <td><?= $usua["stock"] ?></td>
                            <td><img src="../../../images/<?= $usua["codigo_barras"] ?>.png" style="max-width: 450px;"></td>
                            <td><img src="../../../images/<?= $usua["img_herramienta"] ?>" style="max-width: 75px;"></td>
                            <td>
                                <a href="editar_herramienta.php?id=<?= $usua["id_herramienta"] ?>" class="btn btn-primary">Actualizar</a>
                                <a href="#" id="añadirCantidad" data-id="<?= $usua["id_herramienta"] ?>" class="btn btn-success">Añadir cantidad</a>

                                <a href="eliminar_herramienta.php?id=<?= $usua["id_herramienta"] ?>" class="btn btn-danger" onclick="return confirm('¿Está seguro de que desea eliminar esta herramienta?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <a href="exportar_pdf.php" class="btn btn-primary">Generar Reporte</a>

            <a href="../index.php" class="btn btn-danger">Regresar</a>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="../../../paginacion_buscados.js"> </script>
<script>
    $(document).ready(function() {
        $('#añadirCantidad').click(function(e) {
            e.preventDefault();
            var idHerramienta = $(this).data('id');

            // Pedir al usuario que ingrese la cantidad utilizando un prompt personalizado
            var cantidad = promptWithText("Ingrese la cantidad a añadir:", "0");

            // Verificar si se ingresó una cantidad válida
            if (cantidad !== null && !isNaN(cantidad) && cantidad !== '') {
                // Verificar si la cantidad es negativa
                if (parseInt(cantidad) >= 0) {
                    // Realizar la solicitud AJAX para actualizar la cantidad
                    $.ajax({
                        type: 'POST',
                        url: 'actualizar_cantidad.php',
                        data: {
                            id_herramienta: idHerramienta,
                            cantidad: cantidad
                        },
                        success: function(response) {
                            alert('Cantidad añadida correctamente.');
                            // Recargar la página después de una respuesta exitosa
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert('Error al añadir la cantidad.');
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    alert('La cantidad no puede ser negativa.');
                }
            }
        });
    });

    // Función para mostrar un prompt personalizado con texto predeterminado
    function promptWithText(text, defaultValue) {
        return window.prompt(text, defaultValue);
    }
</script>




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

    </body>

    </html>