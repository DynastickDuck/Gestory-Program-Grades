<?php
// Definir variables para la conexion de la base de datos
$host= "localhost";
$database= "GPG";
$user= "root";
$password= "";

// realizamos la coneccion por medio de un try catch
try {
    // intentamos conectar
    $conn = new PDO("mysql:host=$host;dbname=$database", $user, $password);
} catch (PDOException $e) {
    // si no se pudo conectar, mostramos el error
    echo "Error al conectar a la base de datos: " . $e->getMessage();
}




