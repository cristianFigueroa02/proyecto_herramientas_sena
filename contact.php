<?php
require_once("bd/database.php");
$db = new Database();
$conectar = $db->conectar();
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // Recuperar los datos del formulario
   $nombre = $_POST['name'];
   $email = $_POST['email'];
   $telefono = $_POST['phone'];
   $mensaje = $_POST['mensaje'];

   // Preparar la consulta de inserción
   $stmt = $conectar->prepare("INSERT INTO contactos (nombre, email, telefono, mensaje) VALUES (:nombre, :email, :telefono, :mensaje)");

   // Vincular los parámetros
   $stmt->bindParam(':nombre', $nombre);
   $stmt->bindParam(':email', $email);
   $stmt->bindParam(':telefono', $telefono);
   $stmt->bindParam(':mensaje', $mensaje);

   // Ejecutar la consulta de inserción
   if ($stmt->execute()) {
      echo '<script>alert("¡Mensaje enviado de correcto!");</script>';
   } else {
      echo '<script>alert("Error al enviar el mensaje.");</script>';
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
<!-- body -->

<body class="main-layout in_page">
   <!-- loader  -->
   <div class="loader_bg">
      <div class="loader"><img src="images/loading.gif" alt="#" /></div>
   </div>
   <!-- end loader -->
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
                           <li class="nav-item active">
                              <a class="nav-link" href="contact.php">Contactanos</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" href="login.php">Login</a>
                           </li>
                           <li class="nav-item">
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
   <!-- end header inner -->
   <!-- end header -->
   <!--  contact -->
   <div class="contact" style="padding: 10px;">
      <div class="container">
         <div class="row">
            <div class="col-md-12">
               <div class="titlepage">
                  <h2 style="color: black; text-align:center;">Contactanos</h2>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6 offset-md-3">
               <form id="request" class="main_form d-flex justify-content-center align-items-center" method="post">
                  <div class="row">
                     <div class="col-md-12">
                        <input class="contactus" placeholder="Nombre" type="text" name="name">
                     </div>
                     <div class="col-md-12">
                        <input class="contactus" placeholder="Email" type="email" name="email">
                     </div>
                     <div class="col-md-12">
                        <input class="contactus" placeholder="Numero telefonico" type="text" name="phone">
                     </div>
                     <div class="col-md-12">
                        <textarea class="textarea" placeholder="Mensaje" name="mensaje"></textarea>
                     </div>
                     <div class="col-md-12 text-center">
                        <button class="send_btn" type="submit">ENVIAR</button>
                     </div>

                  </div>
               </form>

            </div>
         </div>
      </div>
   </div>

   <!-- end contact -->
   <!--  footer -->
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