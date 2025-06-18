<?php
require_once 'funciones.php'; // Archivo que contiene las funciones del usuario
require_once 'eventos.php';   // Archivo que contiene las funciones para los eventos

$logeado = verificarLogin();

// Redirigir al usuario a inicio.php si cerró sesión despues de 3 segundos
if (!empty($_SESSION['logout_message'])) {
    header("Refresh: 2; URL = inicio.php");
}

// Redireccionar al error 403 si el usuario no está logeado
if (!$logeado && empty($_SESSION['logout_message'])) {
    header('Location: 403.html');
}
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Novedades - ADSO</title>
    <meta name="description" content="Clase del 09 de Octubre del 2023">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel='stylesheet' href='assets/fonts/fontawesome-all.min.css'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Serif+Dogra&amp;display=swap">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body id="page-top">

<?php require_once 'navbar.php'; ?>
<!-- resto del código igual -->

<script src="assets/js/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/theme.js"></script>
<script>
    // JavaScript igual, sin cambios
</script>
<?php require_once "alerts.php"; ?>

</body>

</html>
