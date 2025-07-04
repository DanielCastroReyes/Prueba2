<?php
require_once 'dirs.php'; // Si no usas rutas, puedes eliminar este require
require_once 'conexion.php'; // Archivo con la clase Conexion
require_once 'auth.php';     // Archivo con la clase Auth

// Función para obtener una instancia de la clase Conexion
function obtenerConexion(): PDO
{
    $conexion = new Conexion(); // Crear una instancia de la clase Conexion
    return $conexion->conectar(); // Obtener la instancia de conexión
}

// Función para obtener una instancia de la clase Auth
function obtenerAuth(): Auth
{
    $conexion = new Conexion(); // Crea una instancia de la clase Conexion
    return new Auth($conexion); // Crea una instancia de la clase Auth
}

// Función para obtener una instancia de la clase Conexion y Auth
function obtenerConexionYAuth(): array
{
    $conexion = new Conexion(); // Crear una instancia de la clase Conexion
    $pdo = $conexion->conectar(); // Obtener la instancia de conexión
    $auth = new Auth($conexion); // Crear una instancia de la clase Auth
    return [$pdo, $auth]; // Retornar un array con la instancia de conexión y autenticación
}

/**
 * Función para registrar las Excepciones en el archivo de log
 */

function logPDOException($e, $message): void
{
    // Obtener la fecha y hora actual en la zona horaria deseada
    $currentDateTime = date('d-m-Y H:i:s', strtotime('now -7 hours'));
    // Crear el mensaje de registro
    $logMessage = "[$currentDateTime] $message " . $e->getMessage() . PHP_EOL . $e . PHP_EOL;
    // Registrar el mensaje en el archivo de log
    error_log($logMessage, 3, 'error.log'); // Cambié la ruta a error.log en el mismo directorio
}

function logException($e, $message): void
{
    // Obtener la fecha y hora actual en la zona horaria deseada
    $currentDateTime = date('d-m-Y H:i:s', strtotime('now -7 hours'));
    // Crear el mensaje de registro
    $logMessage = "[$currentDateTime] $message " . $e->getMessage() . PHP_EOL . $e . PHP_EOL;
    // Registrar el mensaje en el archivo de log
    error_log($logMessage, 3, 'error.log'); // Igual aquí
}
