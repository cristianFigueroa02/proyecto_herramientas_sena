<?php
require_once("../../../bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

// Verificar si la sesión está iniciada
if (!isset($_SESSION['documento'])) {
    header("Location: ../../../login.php");
    exit();
}

// Verificar si se proporciona un ID en la URL
if (!isset($_GET['id'])) {
    exit("Error: ID no proporcionado");
}

$documento = $_GET['id'];

// Obtener los datos de la formación seleccionada
$validar = $conectar->prepare("SELECT * FROM formacion WHERE id_formacion = ?");
$validar->execute([$documento]); // Usar $documento en lugar de $id
$herramientas = $validar->fetch();

// Verificar si se enviaron datos mediante el método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    actualizarDatos($conectar, $documento); // Pasar $documento como argumento
}

function actualizarDatos($conectar, $documento) {
    $ficha = $_POST['ficha'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    // Depuración: imprimir valores para verificar la recepción de datos
    echo "Ficha: $ficha, Nombre: $nombre, Email: $email, Documento: $documento<br>";

    // Verificar si la ficha existe en la tabla `formacion`
    $verificarFichaQuery = $conectar->prepare("SELECT COUNT(*) FROM formacion WHERE id_formacion = :ficha");
    $verificarFichaQuery->bindParam(':ficha', $ficha);
    $verificarFichaQuery->execute();
    $fichaExiste = $verificarFichaQuery->fetchColumn();

    if ($fichaExiste > 0) {
        try {
            // Iniciar una transacción
            $conectar->beginTransaction();

            // Actualizar los datos en la tabla `usuario`
            $updateUsuarioQuery = $conectar->prepare("
                UPDATE usuario 
                SET 
                    nombre = :nombre,
                    email = :email
                WHERE documento = :documento
            ");
            $updateUsuarioQuery->bindParam(':nombre', $nombre);
            $updateUsuarioQuery->bindParam(':email', $email);
            $updateUsuarioQuery->bindParam(':documento', $documento);
            $updateUsuarioQuery->execute();

            // Actualizar la ficha en la tabla `detalle_usuarios`
            $updateFichaQuery = $conectar->prepare("
                UPDATE detalle_usuarios 
                SET ficha = :ficha
                WHERE documento = :documento
            ");
            $updateFichaQuery->bindParam(':ficha', $ficha);
            $updateFichaQuery->bindParam(':documento', $documento);
            $updateFichaQuery->execute();

            // Confirmar la transacción
            $conectar->commit();

            // Depuración: verificar el estado de las consultas
            if ($updateUsuarioQuery->rowCount() > 0 || $updateFichaQuery->rowCount() > 0) {
                echo '<script>alert("Actualización exitosa");</script>';
                echo '<script>window.location= "lista_instructores.php"</script>';
            } else {
                echo '<script>alert("No se realizaron cambios");</script>';
            }
        } catch (PDOException $e) {
            // Revertir la transacción en caso de error
            $conectar->rollBack();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo '<script>alert("La ficha no existe en la base de datos.");</script>';
        echo '<script>window.location= "lista_instructores.php?id=' . $documento . '"</script>';
    }
}

// Resto del código...

// Verificar si la clave 'documento' está definida en la sesión antes de usarla
if (isset($_SESSION['documento'])) {

    // Consulta para obtener los datos del usuario
    $usuarioQuery = $conectar->prepare("SELECT * FROM usuario INNER JOIN detalle_usuarios ON detalle_usuarios.documento=usuario.documento WHERE usuario.documento = :documento");
    $usuarioQuery->bindParam(':documento', $documento);
    $usuarioQuery->execute();
    $queryusuario = $usuarioQuery->fetch(PDO::FETCH_ASSOC);

    if ($queryusuario) {
        // Asigna los valores a los campos del formulario
        $documento_usuario = $queryusuario['documento'] ?? '';
        $contrasena_usuario = $queryusuario['contraseña'] ?? '';
        $nombre_usuario = $queryusuario['nombre'] ?? '';
        $email_usuario = $queryusuario['email'] ?? '';
        $id_tip_doc_usuario = $queryusuario['id_tip_doc'] ?? '';
        $ficha_usuario = $queryusuario['ficha'] ?? '';
        $formacion_usuario = $queryusuario['formacion'] ?? '';

        // Asigna los valores a los campos del formulario
    } else {
        echo "No se encontraron datos para el documento: $documento.";
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
<style>
    .formulario {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        padding: 20px;
        width: 400px;
    }

    .formulario__grupo {
        margin-bottom: 20px;
    }

    .formulario__label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .formulario__input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }

    .formulario__input:focus {
        outline: none;
        border-color: #66afe9;
    }

    .formulario__input-error {
        display: none;
        color: #d9534f;
        font-size: 12px;
        margin-top: 5px;
    }

    .formulario__grupo-incorrecto .formulario__input {
        border-color: #d9534f;
    }

    .formulario__grupo-incorrecto .formulario__input-error {
        display: block;
    }

    .formulario__grupo-correcto .formulario__input {
        border-color: #5cb85c;
    }

    .formulario__btn {
        background-color: #5cb85c;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .formulario__btn:hover {
        background-color: #4cae4c;
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
                                    <a href="index.html"><img src="../../../images/Sena_Colombia_logo.svg.png" alt="#" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>

        <body>
            <main>
                <div style="margin-bottom:50px;" class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title text-center">Registro instructor</h4>

                                    <form id="formulario" method="post" action="" autocomplete="off">
                                        <div class="formulario__grupo" id="grupo__documento">
                                            <label for="documento" class="formulario__label">Documento:</label>
                                            <input type="text" class="formulario__input" id="documento" name="documento" disabled value="<?= $documento_usuario ?>" required>
                                            
                                        </div>

                                        <div class="formulario__grupo" id="grupo__nombre">
                                            <label for="nombre" class="formulario__label">Nombres y apellidos:</label>
                                            <input type="text" class="formulario__input" id="nombre" name="nombre" value="<?= $nombre_usuario ?>" pattern="[a-zA-Z0-9\s]+" required>
    <small>El nombre no debe contener caracteres especiales.</small>
                                        </div>

                                        <div class="formulario__grupo" id="grupo__email">
                                            <label for="email" class="formulario__label">Email:</label>
                                            <input type="email" class="formulario__input" id="email" name="email" value="<?= $email_usuario ?>" required>
                                            <p class="formulario__input-error">El email debe ser válido.</p>
                                        </div>

                                        <div class="formulario__grupo" id="grupo__ficha">
                                            <label for="fichaInput" class="formulario__label">Ficha:</label>
                                            <input type="number" class="formulario__input" id="fichaInput" name="ficha" value="<?= $ficha_usuario ?>" required>
                                            <p class="formulario__input-error">La ficha debe ser válida y solo debe contener números.</p>
                                        </div>

                                        <div class="form-group">
                                            <label for="formacion" class="formulario__label">Formación:</label>
                                            <select class="formulario__input" id="formacion" name="formacion">
                                                <option value="" disabled selected>Selecciona su formación</option>
                                                <?php
                                                // Integrar resultados de la consulta en el select
                                                foreach ($tiposfor as $row) {
                                                    $selected = ($formacion_usuario == $row['id_formacion']) ? 'selected' : '';
                                                    echo "<option value='{$row['id_formacion']}' $selected>{$row['formacion']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-success" style="margin-bottom: 10px;">Actualizar</button>
                                        <a href="lista_instructores.php" class="btn btn-danger" style="margin-bottom: 10px;">Volver</a>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

        </body>


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