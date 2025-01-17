<?php
// Datos de conexión a la base de datos
$host = "localhost"; // Servidor
$usuario = "root";   // Usuario por defecto en XAMPP
$clave = "";         // Contraseña (deja vacío si no configuraste una)
$base_datos = "pasioninka"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $clave, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar el juego de caracteres
$conn->set_charset("utf8");

?>
